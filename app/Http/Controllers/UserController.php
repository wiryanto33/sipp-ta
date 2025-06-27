<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Kaprodi;
use App\Models\Mahasiswa;
use App\Models\Prodi;
use App\Models\User;
use App\Traites\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rules;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            // Middleware untuk admin/koordinator
            new Middleware('permission:view users', only: ['index']),
            new Middleware('permission:create users', only: ['create', 'store']),
            new Middleware('permission:delete users', only: ['destroy', 'bulkDestroy', 'forceDestroy']),
            new Middleware('permission:show users', only: ['show']),

            // Middleware untuk edit - izinkan admin, koordinator, mahasiswa, dan dosen
            new Middleware('permission:edit users|edit mahasiswas|edit dosens', only: ['edit', 'update']),

            // Middleware khusus mahasiswa
            new Middleware('permission:view mahasiswas', only: ['mahasiswaIndex']),

            // Middleware khusus dosen
            new Middleware('permission:view dosens', only: ['dosenIndex']),

            new Middleware('permission:view kaprodis', only: ['kaprodiIndex']),

            // Middleware untuk profile - semua role bisa akses
            new Middleware('auth', only: ['profile']),
        ];
    }

    use FileUpload;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil semua users dengan relasi yang diperlukan
        $users = User::with(['mahasiswa.prodi', 'dosen.prodi', 'kaprodi.prodi', 'roles'])->get();

        // Pisahkan berdasarkan role
        $mahasiswas = $users->filter(fn($user) => $user->hasRole('mahasiswa'));
        $dosens = $users->filter(fn($user) => $user->hasRole('dosen'));
        $kaprodis = $users->filter(fn($user) => $user->hasRole('kaprodi'));
        $admins = $users->filter(fn($user) => $user->hasRole('admin'));

        return view('users.index', compact('users', 'mahasiswas', 'dosens', 'kaprodis', 'admins'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        $prodis = Prodi::all();
        return view('users.create', compact('roles', 'prodis'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi data user dasar
        $validatedUser = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'pangkat' => ['nullable', 'string', 'max:255'],
            'korps' => ['nullable', 'string', 'max:255'],
            'nrp' => ['nullable', 'string', 'max:8', 'unique:users,nrp'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role_id' => 'nullable|exists:roles,id',
            'status' => 'sometimes|in:aktif,nonaktif',
        ]);

        // Set default status
        $validatedUser['status'] = $validatedUser['status'] ?? 'aktif';

        // Ambil role terlebih dahulu
        $role = Role::findOrFail($request->role_id);

        // Validasi tambahan sesuai role
        $this->validateRoleSpecificData($request, $role->name);

        DB::beginTransaction();
        try {
            // Upload image jika ada
            if ($request->hasFile('image')) {
                $validatedUser['image'] = $this->uploadFile($request->file('image'));
            }

            // Hash password
            $validatedUser['password'] = Hash::make($validatedUser['password']);

            // Buat user
            $user = User::create($validatedUser);

            // Assign role
            $user->assignRole($role->name);

            // Buat data detail sesuai role
            $this->createRoleSpecificDetail($request, $user, $role->name);

            DB::commit();

            // Determine redirect route and message
            [$redirectRoute, $message] = $this->getRedirectRouteAndMessage($role->name, 'create');

            return redirect()->route($redirectRoute)->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();

            // Hapus image jika sudah diupload
            if (isset($validatedUser['image'])) {
                $this->deleteFile($validatedUser['image']);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::with(['mahasiswa.prodi', 'dosen.prodi', 'kaprodi.prodi', 'roles'])->findOrFail($id);
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::with(['mahasiswa.prodi', 'dosen.prodi', 'kaprodi.prodi', 'roles'])->findOrFail($id);
        $roles = Role::all();
        $prodis = Prodi::all();

        return view('users.edit', compact('user', 'roles', 'prodis'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        // Get validation rules based on current user role
        $validatedUser = $this->getValidationRulesForUpdate($request, $user, $id);

        DB::beginTransaction();
        try {
            // Upload image jika ada
            if ($request->hasFile('image')) {
                // Hapus image lama jika ada
                if ($user->image) {
                    $this->deleteFile($user->image);
                }
                $validatedUser['image'] = $this->uploadFile($request->file('image'));
            }

            // Hash password jika ada
            if (!empty($validatedUser['password'])) {
                $validatedUser['password'] = Hash::make($validatedUser['password']);
            } else {
                unset($validatedUser['password']);
            }

            // Update user
            $user->update($validatedUser);

            // Handle role and detail updates
            $this->handleUserRoleAndDetailUpdate($request, $user);

            DB::commit();

            return redirect()->back()->with('success', 'Data user berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        // Tidak bisa menghapus diri sendiri
        if (Auth::id() === $user->id) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        // Hapus image jika ada
        if ($user->image) {
            $this->deleteFile($user->image);
        }

        // Soft delete akan menangani cascade delete ke mahasiswa/dosen/kaprodi
        $user->delete();

        return redirect()->back()->with('success', 'Data user berhasil dihapus.');
    }

    /**
     * Show mahasiswa list
     */
    public function mahasiswaIndex()
    {
        // Jika user adalah mahasiswa, hanya tampilkan data sendiri
        if (Auth::user()->hasRole('mahasiswa')) {
            $mahasiswas = collect([Auth::user()->load(['mahasiswa.prodi', 'roles'])]);
        } elseif (Auth::user()->hasRole('kaprodi')) {
            // Jika user adalah kaprodi, tampilkan mahasiswa di prodi yang sama
            $prodiId = Auth::user()->kaprodi->prodi_id;
            $mahasiswas = User::role('mahasiswa')
                ->whereHas('mahasiswa', function ($query) use ($prodiId) {
                    $query->where('prodi_id', $prodiId);
                })
                ->with(['mahasiswa.prodi', 'roles'])
                ->get();
        } else {
            $mahasiswas = User::role('mahasiswa')
                ->with(['mahasiswa.prodi', 'roles'])
                ->get();
        }

        return view('users.mahasiswa.index', compact('mahasiswas'));
    }

    /**
     * Show dosen list
     */
    public function dosenIndex()
    {
        // Jika user adalah dosen, hanya tampilkan data sendiri
        if (Auth::user()->hasRole('dosen')) {
            $dosens = collect([Auth::user()->load(['dosen.prodi', 'roles'])]);
        } elseif (Auth::user()->hasRole('kaprodi')) {
            // Jika user adalah kaprodi, tampilkan dosen di prodi yang sama
            $prodiId = Auth::user()->kaprodi->prodi_id;
            $dosens = User::role('dosen')
                ->whereHas('dosen', function ($query) use ($prodiId) {
                    $query->where('prodi_id', $prodiId);
                })
                ->with(['dosen.prodi', 'roles'])
                ->get();
        } else {
            $dosens = User::role('dosen')
                ->with(['dosen.prodi', 'roles'])
                ->get();
        }

        return view('users.dosen.index', compact('dosens'));
    }

    /**
     * Show kaprodi list
     */
    public function kaprodiIndex()
    {
        // Jika user adalah kaprodi, hanya tampilkan data sendiri
        if (Auth::user()->hasRole('kaprodi')) {
            $kaprodis = collect([Auth::user()->load(['kaprodi.prodi', 'roles'])]);
        } else {
            $kaprodis = User::role('kaprodi')
                ->with(['kaprodi.prodi', 'roles'])
                ->get();
        }

        return view('users.kaprodi.index', compact('kaprodis'));
    }

    /**
     * Bulk delete users
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        // Hapus current user dari list jika ada
        $userIds = array_filter($request->user_ids, fn($id) => $id != Auth::id());

        if (empty($userIds)) {
            return redirect()->back()->with('error', 'Tidak ada user yang valid untuk dihapus.');
        }

        $users = User::whereIn('id', $userIds)->get();

        foreach ($users as $user) {
            // Hapus image jika ada
            if ($user->image) {
                $this->deleteFile($user->image);
            }
            $user->delete();
        }

        return redirect()->back()->with('success', count($userIds) . ' user berhasil dihapus.');
    }

    /**
     * Restore soft deleted user
     */
    public function restore(string $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->route('users.index')->with('success', 'Data user berhasil dipulihkan.');
    }

    /**
     * Force delete user permanently
     */
    public function forceDestroy(string $id)
    {
        $user = User::withTrashed()->findOrFail($id);

        // Tidak bisa force delete diri sendiri
        if (Auth::id() === $user->id) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun sendiri secara permanen.');
        }

        // Hapus image jika ada
        if ($user->image) {
            $this->deleteFile($user->image);
        }

        $user->forceDelete();

        return redirect()->route('users.index')->with('success', 'Data user berhasil dihapus permanen.');
    }

    /**
     * Profile mahasiswa/dosen/kaprodi - untuk akses data sendiri
     */
    public function profile()
    {
        $user = Auth::user()->load(['mahasiswa.prodi', 'dosen.prodi', 'kaprodi.prodi', 'roles']);
        return view('users.profile', compact('user'));
    }

    // ==================== PRIVATE HELPER METHODS ====================

    /**
     * Validate role-specific data
     */
    private function validateRoleSpecificData(Request $request, string $roleName): void
    {
        switch ($roleName) {
            case 'mahasiswa':
                $request->validate([
                    'prodi_id_mahasiswa' => 'required|exists:prodis,id',
                    'angkatan' => 'required|integer|min:2000|max:' . date('Y'),
                    'semester' => 'required|integer|min:1|max:14',
                    'ipk' => 'nullable|numeric|min:0|max:4',
                    'phone' => 'nullable|string|max:20',
                    'alamat' => 'nullable|string|max:255',
                ]);
                break;

            case 'dosen':
                $request->validate([
                    'prodi_id_dosen' => 'required|exists:prodis,id',
                    'nidn' => 'required|string|max:20|unique:dosens,nidn',
                    'jabatan_akademik' => 'required|string|max:255',
                    'bidang_studi' => 'required|string|max:255',
                    'phone' => 'nullable|string|max:20',
                    'alamat' => 'nullable|string|max:255',
                ]);
                break;

            case 'kaprodi':
                $request->validate([
                    'prodi_id_kaprodi' => 'required|exists:prodis,id',
                    'phone' => 'nullable|string|max:20',
                    'alamat' => 'nullable|string|max:255',
                ]);
                break;
        }
    }

    /**
     * Create role-specific detail
     */
    private function createRoleSpecificDetail(Request $request, User $user, string $roleName): void
    {
        switch ($roleName) {
            case 'mahasiswa':
                $this->createMahasiswaDetail($request, $user);
                break;
            case 'dosen':
                $this->createDosenDetail($request, $user);
                break;
            case 'kaprodi':
                $this->createKaprodiDetail($request, $user);
                break;
        }
    }

    /**
     * Get redirect route and message based on role
     */
    private function getRedirectRouteAndMessage(string $roleName, string $action): array
    {
        $messages = [
            'create' => [
                'mahasiswa' => ['mahasiswas.index', 'Data mahasiswa berhasil ditambahkan.'],
                'dosen' => ['dosens.index', 'Data dosen berhasil ditambahkan.'],
                'kaprodi' => ['kaprodis.index', 'Data kaprodi berhasil ditambahkan.'],
                'default' => ['users.index', 'Data user berhasil ditambahkan.']
            ]
        ];

        return $messages[$action][$roleName] ?? $messages[$action]['default'];
    }

    /**
     * Get validation rules for update based on user role
     */
    private function getValidationRulesForUpdate(Request $request, User $user, string $id): array
    {
        $baseRules = [
            'name' => ['nullable', 'string', 'max:255'],
            'pangkat' => ['nullable', 'string', 'max:255'],
            'korps' => ['nullable', 'string', 'max:255'],
            'nrp' => ['nullable', 'string', 'max:8', 'unique:users,nrp,' . $id],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ];

        // Admin/koordinator bisa mengubah semua field
        if (Auth::user()->hasRole(['admin', 'koordinator'])) {
            $baseRules['status'] = 'required|in:aktif,nonaktif';
            $baseRules['role_id'] = 'required|exists:roles,id';
        }

        return $request->validate($baseRules);
    }

    /**
     * Handle user role and detail updates
     */
    private function handleUserRoleAndDetailUpdate(Request $request, User $user): void
    {
        // Jika bukan admin/koordinator, hanya update detail
        if (!Auth::user()->hasRole(['admin', 'koordinator'])) {
            $this->updateRoleSpecificDetailForSelf($request, $user);
            return;
        }

        // Admin/koordinator logic
        $newRole = Role::find($request->role_id);
        $currentRoles = $user->roles->pluck('name')->toArray();

        if (!in_array($newRole->name, $currentRoles)) {
            // Sync role baru
            $user->syncRoles([$newRole->name]);
            // Handle perubahan role
            $this->handleRoleChange($user, $newRole->name, $request);
        } else {
            // Update detail sesuai role existing
            $this->updateRoleSpecificDetail($request, $user);
        }
    }

    /**
     * Update role-specific detail for self (non-admin)
     */
    private function updateRoleSpecificDetailForSelf(Request $request, User $user): void
    {
        if ($user->hasRole('mahasiswa')) {
            $this->updateMahasiswaDetailForSelf($request, $user);
        } elseif ($user->hasRole('dosen')) {
            $this->updateDosenDetailForSelf($request, $user);
        } elseif ($user->hasRole('kaprodi')) {
            $this->updateKaprodiDetailForSelf($request, $user);
        }
    }

    /**
     * Update role-specific detail (for admin)
     */
    private function updateRoleSpecificDetail(Request $request, User $user): void
    {
        if ($user->hasRole('mahasiswa')) {
            $this->updateMahasiswaDetail($request, $user);
        } elseif ($user->hasRole('dosen')) {
            $this->updateDosenDetail($request, $user);
        } elseif ($user->hasRole('kaprodi')) {
            $this->updateKaprodiDetail($request, $user);
        }
    }

    /**
     * Create mahasiswa detail
     */
    private function createMahasiswaDetail(Request $request, User $user): void
    {
        $validated = [
            'prodi_id' => $request->prodi_id_mahasiswa,
            'user_id' => $user->id,
            'role_id' => $request->role_id,
            'angkatan' => $request->angkatan,
            'semester' => $request->semester,
            'ipk' => $request->ipk,
            'phone' => $request->phone,
            'alamat' => $request->alamat,
        ];

        Mahasiswa::create($validated);
    }

    /**
     * Create dosen detail
     */
    private function createDosenDetail(Request $request, User $user): void
    {
        $validated = [
            'prodi_id' => $request->prodi_id_dosen,
            'user_id' => $user->id,
            'role_id' => $request->role_id,
            'nidn' => $request->nidn,
            'jabatan_akademik' => $request->jabatan_akademik,
            'bidang_studi' => $request->bidang_studi,
            'phone' => $request->phone,
            'alamat' => $request->alamat,
        ];

        Dosen::create($validated);
    }

    /**
     * Create kaprodi detail
     */
    private function createKaprodiDetail(Request $request, User $user): void
    {
        $validated = [
            'prodi_id' => $request->prodi_id_kaprodi,
            'user_id' => $user->id,
            'role_id' => $request->role_id,
            'phone' => $request->phone,
            'alamat' => $request->alamat,
        ];

        Kaprodi::create($validated);
    }

    /**
     * Update mahasiswa detail (untuk admin/koordinator)
     */
    private function updateMahasiswaDetail(Request $request, User $user): void
    {
        $validatedMahasiswa = $request->validate([
            'prodi_id_mahasiswa' => 'nullable|exists:prodis,id',
            'angkatan' => 'nullable|integer|min:2000|max:' . date('Y'),
            'semester' => 'nullable|integer|min:1|max:14',
            'ipk' => 'nullable|numeric|min:0|max:4',
            'phone' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:255',
        ]);

        if (isset($validatedMahasiswa['prodi_id_mahasiswa'])) {
            $validatedMahasiswa['prodi_id'] = $validatedMahasiswa['prodi_id_mahasiswa'];
            unset($validatedMahasiswa['prodi_id_mahasiswa']);
        }

        if ($user->mahasiswa) {
            $user->mahasiswa->update($validatedMahasiswa);
        } else {
            $validatedMahasiswa['user_id'] = $user->id;
            $validatedMahasiswa['role_id'] = $user->roles->first()->id;
            Mahasiswa::create($validatedMahasiswa);
        }
    }

    /**
     * Update mahasiswa detail (untuk mahasiswa sendiri - tanpa IPK)
     */
    private function updateMahasiswaDetailForSelf(Request $request, User $user): void
    {
        $validatedMahasiswa = $request->validate([
            'prodi_id_mahasiswa' => 'nullable|exists:prodis,id',
            'angkatan' => 'nullable|integer|min:2000|max:' . date('Y'),
            'semester' => 'nullable|integer|min:1|max:14',
            'phone' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:255',
            // IPK tidak diizinkan untuk mahasiswa
        ]);

        if (isset($validatedMahasiswa['prodi_id_mahasiswa'])) {
            $validatedMahasiswa['prodi_id'] = $validatedMahasiswa['prodi_id_mahasiswa'];
            unset($validatedMahasiswa['prodi_id_mahasiswa']);
        }

        if ($user->mahasiswa) {
            $user->mahasiswa->update($validatedMahasiswa);
        }
    }

    /**
     * Update dosen detail (untuk admin/koordinator)
     */
    private function updateDosenDetail(Request $request, User $user): void
    {
        $dosenId = $user->dosen?->id;

        $validatedDosen = $request->validate([
            'prodi_id_dosen' => 'nullable|exists:prodis,id',
            'nidn' => 'nullable|string|max:20|unique:dosens,nidn,' . $dosenId,
            'jabatan_akademik' => 'nullable|string|max:255',
            'bidang_studi' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:255',
        ]);

        if (isset($validatedDosen['prodi_id_dosen'])) {
            $validatedDosen['prodi_id'] = $validatedDosen['prodi_id_dosen'];
            unset($validatedDosen['prodi_id_dosen']);
        }

        if ($user->dosen) {
            $user->dosen->update($validatedDosen);
        } else {
            $validatedDosen['user_id'] = $user->id;
            $validatedDosen['role_id'] = $user->roles->first()->id;
            Dosen::create($validatedDosen);
        }
    }

    /**
     * Update dosen detail (untuk dosen sendiri)
     */
    private function updateDosenDetailForSelf(Request $request, User $user): void
    {
        $dosenId = $user->dosen?->id;

        $validatedDosen = $request->validate([
            'prodi_id_dosen' => 'nullable|exists:prodis,id',
            'nidn' => 'nullable|string|max:20|unique:dosens,nidn,' . $dosenId,
            'jabatan_akademik' => 'nullable|string|max:255',
            'bidang_studi' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:255',
        ]);

        if (isset($validatedDosen['prodi_id_dosen'])) {
            $validatedDosen['prodi_id'] = $validatedDosen['prodi_id_dosen'];
            unset($validatedDosen['prodi_id_dosen']);
        }

        if ($user->dosen) {
            $user->dosen->update($validatedDosen);
        }
    }

    /**
     * Update kaprodi detail (untuk admin/koordinator)
     */
    private function updateKaprodiDetail(Request $request, User $user): void
    {
        $validatedKaprodi = $request->validate([
            'prodi_id_kaprodi' => 'nullable|exists:prodis,id',
            'phone' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:255',
        ]);

        if (isset($validatedKaprodi['prodi_id_kaprodi'])) {
            $validatedKaprodi['prodi_id'] = $validatedKaprodi['prodi_id_kaprodi'];
            unset($validatedKaprodi['prodi_id_kaprodi']);
        }

        if ($user->kaprodi) {
            $user->kaprodi->update($validatedKaprodi);
        } else {
            $validatedKaprodi['user_id'] = $user->id;
            $validatedKaprodi['role_id'] = $user->roles->first()->id;
            Kaprodi::create($validatedKaprodi);
        }
    }

    /**
     * Update kaprodi detail (untuk kaprodi sendiri)
     */
    private function updateKaprodiDetailForSelf(Request $request, User $user): void
    {
        $validatedKaprodi = $request->validate([
            'prodi_id_kaprodi' => 'nullable|exists:prodis,id',
            'phone' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:255',
        ]);

        if (isset($validatedKaprodi['prodi_id_kaprodi'])) {
            $validatedKaprodi['prodi_id'] = $validatedKaprodi['prodi_id_kaprodi'];
            unset($validatedKaprodi['prodi_id_kaprodi']);
        }

        if ($user->kaprodi) {
            $user->kaprodi->update($validatedKaprodi);
        }
    }

    /**
     * Handle role change
     */
    private function handleRoleChange(User $user, string $newRole, Request $request): void
    {
        // Hapus detail lama
        $user->mahasiswa?->delete();
        $user->dosen?->delete();
        $user->kaprodi?->delete();

        // Buat detail baru sesuai role
        $this->createRoleSpecificDetail($request, $user, $newRole);
    }
}
