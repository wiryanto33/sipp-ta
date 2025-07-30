<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Ambil user berdasarkan email
        $user = \App\Models\User::where('email', $request->email)->first();

        if (!$user || !\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => __('These credentials do not match our records.'),
            ]);
        }

        // Cek apakah status user aktif
        if ($user->status !== 'aktif') {
            // Redirect kembali ke login dengan pesan error dan badge
            return redirect()->back()
                ->withInput($request->only('email'))
                ->with([
                    'error_message' => 'Akun Anda belum diaktivasi. Silakan hubungi administrator.',
                    'error_badge' => [
                        'text' => 'Tidak Aktif',
                        'class' => 'bg-danger text-white',
                        'icon' => 'bi-x-circle'
                    ]
                ]);
        }

        // Jika semuanya valid, maka login user
        Auth::login($user, $request->boolean('remember'));

        $request->session()->regenerate();

        $role = $user->getRoleNames()->first();

        // Redirect dengan pesan sukses dan badge
        $welcomeMessage = $this->getWelcomeMessage($user, $role);

        $redirectRoute = $this->getRedirectRoute($role);

        return redirect()->route($redirectRoute)->with([
            'success_message' => $welcomeMessage['message'],
            'success_badge' => $welcomeMessage['badge']
        ]);
    }

    /**
     * Get welcome message based on user role
     */
    private function getWelcomeMessage($user, $role): array
    {
        $messages = [
            'admin' => [
                'message' => "Selamat datang, {$user->name}! Anda login sebagai",
                'badge' => [
                    'text' => 'Admin',
                    'class' => 'bg-primary text-white',
                    'icon' => 'bi-shield-check'
                ]
            ],
            'kaprodi' => [
                'message' => "Selamat datang, {$user->name}! Anda login sebagai ",
                'badge' => [
                    'text' => 'Kaprodi',
                    'class' => 'bg-success text-white',
                    'icon' => 'bi-person-badge'
                ]
            ],
            'dosen' => [
                'message' => "Selamat datang, {$user->name}! Anda login sebagai ",
                'badge' => [
                    'text' => 'Dosen',
                    'class' => 'bg-info text-white',
                    'icon' => 'bi-mortarboard'
                ]
            ],
            'mahasiswa' => [
                'message' => "Selamat datang, {$user->name}! Anda login sebagai ",
                'badge' => [
                    'text' => 'Mahasiswa',
                    'class' => 'bg-warning text-dark',
                    'icon' => 'bi-person'
                ]
            ]
        ];

        return $messages[$role] ?? [
            'message' => "Selamat datang, {$user->name}!",
            'badge' => [
                'text' => 'User',
                'class' => 'bg-secondary text-white',
                'icon' => 'bi-person-circle'
            ]
        ];
    }

    /**
     * Get redirect route based on role
     */
    private function getRedirectRoute($role): string
    {
        $routes = [
            'admin' => 'dashboard',
            'kaprodi' => 'dashboard',
            'mahasiswa' => 'mahasiswa.dashboard',
            'dosen' => 'dosen.dashboard'
        ];

        return $routes[$role] ?? 'dashboard';
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect dengan pesan logout dan badge
        return redirect('/')->with([
            'info_message' => 'Anda telah berhasil logout. Terima kasih!',
            'info_badge' => [
                'text' => 'Logout',
                'class' => 'bg-secondary text-white',
                'icon' => 'bi-box-arrow-right'
            ]
        ]);
    }
}
