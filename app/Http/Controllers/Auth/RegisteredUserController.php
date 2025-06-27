<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'tipe' => ['required', 'in:mahasiswa,dosen'],
        ]);

        // Buat user dengan status nonaktif
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 'nonaktif',
        ]);

        // Assign role dan buat data tambahan
        if ($request->tipe === 'mahasiswa') {
            $user->assignRole('mahasiswa');

            Mahasiswa::create([
                'user_id' => $user->id,
                'role_id' => Role::where('name', 'mahasiswa')->first()->id,
                'prodi_id' => null,
                'angkatan' => now()->format('Y'),
                'semester' => 1,
            ]);
        } else {
            $user->assignRole('dosen');

            Dosen::create([
                'user_id' => $user->id,
                'role_id' => Role::where('name', 'dosen')->first()->id,
                'prodi_id' => null,
                'nidn' => null, // generate sementara
                'jabatan_akademik' => 'Asisten Ahli',
            ]);
        }

        event(new Registered($user));

        return redirect()->route('login')->with('status', 'Akun berhasil didaftarkan. Silakan tunggu aktivasi oleh admin.');
    }
}
