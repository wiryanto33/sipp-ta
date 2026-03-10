<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

/**
 * Contoh Implementasi Authorization dalam Controller
 *
 * Panduan untuk menggunakan authorization checks dalam controller methods
 */
class AuthorizationExampleController extends Controller
{
    /**
     * Contoh 1: Method-level authorization menggunakan middleware
     *
     * Route:
     * Route::middleware(['auth', 'role:admin|kaprodi'])->group(function () {
     *     Route::get('/admin-only', [AuthorizationExampleController::class, 'adminOnly']);
     * });
     */
    public function adminOnly()
    {
        // Jika user tidak memiliki role admin/kaprodi, 403 exception akan di-throw
        // Exception handler di bootstrap/app.php akan menangani dan redirect
        return response()->json([
            'message' => 'Anda memiliki akses admin',
            'user' => Auth::user()->name
        ]);
    }

    /**
     * Contoh 2: Manual authorization check di dalam method
     */
    public function deleteUser($id)
    {
        // Cek manual dengan gate/policy
        $this->authorize('delete', User::class);

        // Atau dengan simple check
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Hanya admin yang dapat menghapus user');
        }

        // Lanjutkan proses delete...
    }

    /**
     * Contoh 3: Role-specific action
     */
    public function getMahasiswaData()
    {
        // Hanya mahasiswa yang bisa akses data mereka sendiri
        if (!Auth::user()->hasRole('mahasiswa')) {
            abort(403, 'Hanya mahasiswa yang dapat mengakses fitur ini');
        }

        $mahasiswa = Auth::user()->mahasiswa;

        return response()->json($mahasiswa);
    }

    /**
     * Contoh 4: Multiple roles check
     */
    public function approveSubmission($id)
    {
        // Hanya kaprodi dan dosen yang bisa approve
        if (!Auth::user()->hasAnyRole(['kaprodi', 'dosen'])) {
            abort(403, 'Anda tidak memiliki izin untuk approve submission');
        }

        // Proses approval...
    }

    /**
     * Contoh 5: Permission-based check
     */
    public function editBimbingan($id)
    {
        // Check specific permission
        if (!Auth::user()->hasPermissionTo('edit-bimbingan')) {
            abort(403, 'Anda tidak memiliki permission untuk mengedit bimbingan');
        }

        // Lanjutkan editing...
    }

    /**
     * Contoh 6: Owner-based authorization (untuk resource yang dimiliki user)
     */
    public function editProfile($userId)
    {
        $user = User::findOrFail($userId);

        // User hanya bisa edit profile mereka sendiri, atau jika adalah admin
        if (Auth::id() !== $user->id && !Auth::user()->hasRole('admin')) {
            abort(403, 'Anda hanya bisa mengedit profile Anda sendiri');
        }

        // Lanjutkan edit profile...
    }

    /**
     * Contoh 7: Conditional authorization
     */
    public function submitPengajuan()
    {
        $user = Auth::user();

        // Mahasiswa bisa submit, tapi hanya jika profile sudah lengkap
        if ($user->hasRole('mahasiswa')) {
            if (!$user->mahasiswa || empty($user->mahasiswa->prodi_id)) {
                abort(403, 'Lengkapi profile Anda terlebih dahulu sebelum submit');
            }
        } else {
            abort(403, 'Hanya mahasiswa yang dapat submit pengajuan');
        }

        // Lanjutkan submit...
    }

    /**
     * Contoh 8: Try-catch untuk graceful error handling (API)
     */
    public function apiDeleteUser($id)
    {
        try {
            $this->authorize('delete', User::class);

            User::findOrFail($id)->delete();

            return response()->json([
                'message' => 'User berhasil dihapus',
                'status' => 'success'
            ]);
        } catch (\Illuminate\Authorization\AuthorizationException $e) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk menghapus user',
                'status' => 'error'
            ], 403);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan',
                'status' => 'error'
            ], 500);
        }
    }
}

/**
 * ============================================
 * BEST PRACTICES
 * ============================================
 *
 * 1. Gunakan Middleware untuk Route-level Authorization:
 *    - Route::middleware(['auth', 'role:admin'])->group(...)
 *    - Lebih clean dan reusable
 *
 * 2. Gunakan Policy Class untuk Model Authorization:
 *    - php artisan make:policy UserPolicy --model=User
 *    - Centralize business logic
 *
 * 3. Gunakan Helper Methods:
 *    - $user->hasRole('mahasiswa')
 *    - $user->hasPermissionTo('edit-users')
 *    - Lebih readable
 *
 * 4. Provide Clear Error Messages:
 *    - abort(403, 'Pesan yang jelas mengapa access ditolak')
 *    - Membantu user memahami masalah
 *
 * 5. Log Authorization Failures:
 *    - Sudah ditangani oleh exception handler
 *    - Check log di storage/logs/
 *
 * 6. Test Your Authorization:
 *    - Test sebagai different roles
 *    - Test edge cases
 *
 * ============================================
 * COMMON AUTHORIZATION PATTERNS
 * ============================================
 */

/**
 * Pattern 1: Admin-Only Resources
 * Route::middleware(['auth', 'role:admin'])->group(...)
 */

/**
 * Pattern 2: Teacher (Dosen) Management of Student (Mahasiswa) Work
 * if (!Auth::user()->hasRole('dosen') &&
 *     Auth::user()->prodi_id !== $mahasiswa->prodi_id) {
 *     abort(403);
 * }
 */

/**
 * Pattern 3: Student (Mahasiswa) Own Data Access
 * if (Auth::user()->hasRole('mahasiswa') &&
 *     Auth::id() !== $mahasiswa->user_id) {
 *     abort(403);
 * }
 */

/**
 * Pattern 4: Program Chair (Kaprodi) Program-Specific Access
 * if (!Auth::user()->hasRole('kaprodi') ||
 *     Auth::user()->prodi_id !== $resource->prodi_id) {
 *     abort(403);
 * }
 */
