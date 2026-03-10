# Implementasi 403 Unauthorized Handling untuk Semua Role

## Deskripsi

Implementasi comprehensive untuk menangani error 403 (Unauthorized) di seluruh aplikasi dengan redirect otomatis berdasarkan role pengguna.

## Komponen yang Telah Dibuat

### 1. **HandleAuthorizationException Middleware**

File: `app/Http/Middleware/HandleAuthorizationException.php`

**Fungsi:**

-   Menangkap exception dari authorization check
-   Log setiap upaya akses unauthorized
-   Redirect berdasarkan role pengguna ke dashboard masing-masing
-   Support JSON response untuk API requests

**Roles yang Ditangani:**

-   **Admin**: Redirect ke `route('dashboard')`
-   **Kaprodi**: Redirect ke `route('dashboard')`
-   **Dosen**: Redirect ke `route('dosen.dashboard')`
-   **Mahasiswa**: Redirect ke `route('mahasiswa.dashboard')`
-   **Not Authenticated**: Redirect ke `route('login')`

### 2. **Exception Handler di bootstrap/app.php**

File: `bootstrap/app.php`

**Pengubahan:**

-   Menambahkan import untuk `AuthorizationException` dan `AuthenticationException`
-   Membuat custom render callback untuk handling 403 dan 401 exceptions
-   Logging akses unauthorized dengan detail:
    -   User ID
    -   User Roles
    -   Path yang diakses
    -   HTTP Method
    -   IP Address

**Fitur:**

-   JSON response untuk API requests (403, 401)
-   Redirect ke dashboard sesuai role untuk browser requests
-   Auto-redirect ke login jika user tidak authenticated

### 3. **Custom Error Pages**

#### 403 Error Page

File: `resources/views/errors/403.blade.php`

**Features:**

-   Visual yang menarik dengan gradient background
-   Pesan yang user-friendly
-   Navigation buttons berdasarkan role
-   Info error code

#### 401 Error Page

File: `resources/views/errors/401.blade.php`

**Features:**

-   Halaman login prompt
-   Navigation buttons back/login
-   Error code display

### 4. **Authorize Middleware**

File: `app/Http/Middleware/Authorize.php`

**Fungsi:**

-   Middleware dasar untuk custom authorization logic
-   Dapat digunakan untuk route-specific authorization
-   Base untuk extensibility

## Cara Penggunaan

### 1. Dengan Role Middleware (Existing)

```php
Route::middleware(['auth', 'role:admin|kaprodi'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
});
```

Jika user tidak memiliki role yang sesuai → **403 Exception** → Redirect ke dashboard sesuai role

### 2. Dengan Permission Middleware (Existing)

```php
Route::middleware(['auth', 'permission:edit-users'])->group(function () {
    Route::get('/users/edit/{id}', [UserController::class, 'edit']);
});
```

Jika user tidak memiliki permission → **403 Exception** → Redirect ke dashboard sesuai role

### 3. Manual Authorization Check di Controller

```php
public function destroy(User $user)
{
    // Manual check
    $this->authorize('delete', $user);
    // atau
    if (!auth()->user()->can('delete', $user)) {
        abort(403, 'Anda tidak memiliki akses');
    }

    $user->delete();
}
```

## Logging

Setiap unauthorized access akan dicatat di log dengan informasi:

```
[2025-11-17 10:30:45] local.WARNING: Authorization exception - 403 {
    "user_id": 5,
    "user_roles": ["mahasiswa"],
    "path": "admin/users",
    "method": "GET",
    "ip": "127.0.0.1"
}
```

## API Response (JSON)

Ketika request ke endpoint API dengan Accept header JSON:

**403 Unauthorized:**

```json
{
    "message": "Anda tidak memiliki akses untuk melakukan tindakan ini.",
    "status": 403
}
```

**401 Unauthenticated:**

```json
{
    "message": "Unauthenticated.",
    "status": 401
}
```

## Testing

### Test 403 Unauthorized (Browser)

1. Login sebagai **Mahasiswa**
2. Akses `/dashboard-admin` (hanya untuk Admin/Kaprodi)
3. Expected: Redirect ke `/mahasiswa-dashboard` dengan pesan error

### Test 403 Unauthorized (API)

```bash
curl -X GET "http://localhost:8000/dashboard-admin" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer {token}"
```

Expected: JSON 403 response

### Test 401 Unauthenticated

1. Logout
2. Akses halaman yang butuh auth
3. Expected: Redirect ke `/login`

## Integrasi dengan Existing Routes

Routes yang sudah ada akan otomatis menggunakan exception handler baru:

✅ **Dashboard Routes:**

-   `/dashboard-admin` - role:admin|kaprodi
-   `/mahasiswa-dashboard` - role:mahasiswa
-   `/dosen-dashboard` - role:dosen

✅ **User Management:**

-   `/users` - All authenticated users
-   `/users/edit/{id}` - Depends on policy

✅ **Bimbingan:**

-   `/bimbingan` - Multiple roles with different access

## Future Enhancements

1. **Fine-grained Permissions:**

    - Implementasi policy classes untuk model-specific authorization
    - Resource-based permissions

2. **Audit Trail:**

    - Store unauthorized attempts ke database
    - Dashboard untuk viewing unauthorized accesses

3. **Rate Limiting:**

    - Implement rate limiting untuk prevent brute force
    - Temporary ban untuk repeated unauthorized attempts

4. **Custom Messages:**
    - Role-specific error messages
    - Context-aware messages berdasarkan route

## Kesimpulan

Implementasi ini menyediakan:
✓ Automatic 403/401 handling untuk semua routes
✓ Smart redirect berdasarkan role
✓ Comprehensive logging untuk security audit
✓ User-friendly error pages
✓ API JSON response support
✓ Extensible architecture untuk future features
