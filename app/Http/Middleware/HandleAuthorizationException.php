<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Authorization\AuthorizationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleAuthorizationException
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            return $next($request);
        } catch (AuthorizationException $e) {
            // Log the unauthorized access attempt
            \Log::warning('Unauthorized access attempt', [
                'user_id' => auth()->id(),
                'user_roles' => auth()->user()?->getRoleNames(),
                'path' => $request->path(),
                'method' => $request->method(),
                'ip' => $request->ip(),
            ]);

            // Redirect to 403 page or dashboard based on user's primary role
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Anda tidak memiliki akses untuk halaman ini.',
                    'status' => 403,
                ], 403);
            }

            return $this->redirectBasedOnRole();
        }
    }

    /**
     * Redirect berdasarkan role pengguna
     */
    private function redirectBasedOnRole()
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
        }

        $user = auth()->user();
        $roles = $user->getRoleNames()->toArray();

        // Tentukan route berdasarkan role yang dimiliki
        if (in_array('admin', $roles)) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda tidak memiliki akses untuk halaman ini.');
        } elseif (in_array('kaprodi', $roles)) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda tidak memiliki akses untuk halaman ini.');
        } elseif (in_array('dosen', $roles)) {
            return redirect()->route('dosen.dashboard')
                ->with('error', 'Anda tidak memiliki akses untuk halaman ini.');
        } elseif (in_array('mahasiswa', $roles)) {
            return redirect()->route('mahasiswa.dashboard')
                ->with('error', 'Anda tidak memiliki akses untuk halaman ini.');
        }

        // Fallback: redirect ke login
        return redirect()->route('login')
            ->with('error', 'Anda tidak memiliki akses untuk halaman ini.');
    }
}
