<?php

use App\Http\Middleware\CheckProfileCompletion;
use App\Http\Middleware\RedirectIfAuthenticatedWithRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Authorization\AuthorizationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // ✅ Tambahkan alias middleware kamu di sini
        $middleware->alias([
            'check.profile' => CheckProfileCompletion::class,
            'handle.authorization' => \App\Http\Middleware\HandleAuthorizationException::class,

            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle Authorization Exception (403)
        $exceptions->render(function (AuthorizationException $e, $request) {
            Log::warning('Authorization exception - 403', [
                'user_id' => Auth::id(),
                'user_roles' => Auth::user()?->getRoleNames(),
                'path' => $request->path(),
                'method' => $request->method(),
                'ip' => $request->ip(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Anda tidak memiliki akses untuk melakukan tindakan ini.',
                    'status' => 403,
                ], 403);
            }

            // Redirect berdasarkan role
            if (!Auth::check()) {
                return redirect()->route('login')
                    ->with('error', 'Anda tidak memiliki akses untuk halaman ini.');
            }

            $user = Auth::user();
            $roles = $user->getRoleNames()->toArray();

            if (in_array('admin', $roles)) {
                return redirect()->route('dashboard')
                    ->with('error', 'Anda tidak memiliki akses untuk melakukan tindakan ini.');
            } elseif (in_array('kaprodi', $roles)) {
                return redirect()->route('dashboard')
                    ->with('error', 'Anda tidak memiliki akses untuk melakukan tindakan ini.');
            } elseif (in_array('dosen', $roles)) {
                return redirect()->route('dosen.dashboard')
                    ->with('error', 'Anda tidak memiliki akses untuk melakukan tindakan ini.');
            } elseif (in_array('mahasiswa', $roles)) {
                return redirect()->route('mahasiswa.dashboard')
                    ->with('error', 'Anda tidak memiliki akses untuk melakukan tindakan ini.');
            }

            return redirect()->route('login')
                ->with('error', 'Anda tidak memiliki akses untuk halaman ini.');
        });

        // Handle Authentication Exception (401)
        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthenticated.',
                    'status' => 401,
                ], 401);
            }

            return redirect()->route('login');
        });
    })->create();
