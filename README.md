# Sistem Absensi Karyawan (Laravel + MySQL + Blade)

Implementasi sistem absensi karyawan berbasis web sesuai requirement:
- Login/logout karyawan & admin
- Check-in / check-out
- Simpan tanggal, jam masuk, jam keluar, GPS, selfie
- Deteksi telat (masuk > 08:00)
- Status: `Hadir`, `Telat`, `Tidak Hadir`
- Cegah absen lebih dari sekali per hari
- Rekap admin + filter + export Excel
- API endpoint untuk mobile app

## 1) Struktur Database

### Tabel `users`
- `id`
- `name`
- `email`
- `password`
- `role` (`admin` / `employee`)

### Tabel `attendance`
- `id`
- `user_id`
- `date`
- `check_in`
- `check_out`
- `status`
- `latitude`
- `longitude`
- `photo`

## 2) Fitur Utama yang Diimplementasikan

### Karyawan
- Halaman login (`/login`)
- Dashboard (`/dashboard`)
- Tombol **Absen Masuk** + upload selfie + GPS
- Tombol **Absen Pulang** + GPS
- Riwayat absensi

### Admin
- Dashboard admin (`/admin/dashboard`)
- Data karyawan
- Rekap absensi per hari/bulan (filter tanggal)
- Filter berdasarkan nama
- Export rekap ke Excel

## 3) Validasi Jam Kerja
- Check-in hanya di rentang **08:00 – 17:00**
- Check-in > 08:00 otomatis status `Telat`
- Check-out hanya setelah **17:00**

## 4) Middleware
- `auth`: proteksi route karyawan/admin
- `admin`: hanya role admin boleh akses route `/admin/*`

## 5) Routing

### Web (`routes/web.php`)
- `GET /login`
- `POST /login`
- `POST /logout`
- `GET /dashboard`
- `POST /attendance/check-in`
- `POST /attendance/check-out`
- `GET /admin/dashboard`
- `GET /admin/attendance/export`

### API (`routes/api.php`)
- `POST /api/login`
- `POST /api/logout`
- `GET /api/attendance/history`
- `POST /api/attendance/check-in`
- `POST /api/attendance/check-out`

> API route absensi diproteksi `auth:sanctum`.

## 6) Contoh JSON API

### Login
`POST /api/login`

```json
{
  "email": "karyawan@company.com",
  "password": "password"
}
```

Response:
```json
{
  "message": "Login berhasil",
  "user": {
    "id": 2,
    "name": "Budi",
    "email": "karyawan@company.com",
    "role": "employee"
  }
}
```

### Check-in
`POST /api/attendance/check-in`

```json
{
  "latitude": -6.2000000,
  "longitude": 106.8166660,
  "photo": "data:image/jpeg;base64,/9j/4AAQSk..."
}
```

Response:
```json
{
  "message": "Check-in berhasil",
  "data": {
    "id": 10,
    "user_id": 2,
    "date": "2026-02-16",
    "check_in": "08:10:02",
    "check_out": null,
    "status": "Telat",
    "latitude": "-6.2000000",
    "longitude": "106.8166660",
    "photo": "data:image/jpeg;base64,/9j/4AAQSk..."
  }
}
```

### Check-out
`POST /api/attendance/check-out`

```json
{
  "latitude": -6.2001000,
  "longitude": 106.8167000
}
```

Response:
```json
{
  "message": "Check-out berhasil",
  "data": {
    "id": 10,
    "check_out": "17:02:43"
  }
}
```

## 7) File yang Disediakan
- Migration: `database/migrations/*create_users_table.php`, `*create_attendance_table.php`
- Model: `app/Models/User.php`, `app/Models/Attendance.php`
- Controller:
  - `app/Http/Controllers/Auth/AuthController.php`
  - `app/Http/Controllers/AttendanceController.php`
  - `app/Http/Controllers/AdminController.php`
  - `app/Http/Controllers/Api/AuthApiController.php`
  - `app/Http/Controllers/Api/AttendanceApiController.php`
- Middleware: `app/Http/Middleware/AdminMiddleware.php`
- Export Excel: `app/Exports/AttendanceExport.php`
- Routes: `routes/web.php`, `routes/api.php`
- Blade UI:
  - `resources/views/auth/login.blade.php`
  - `resources/views/employee/dashboard.blade.php`
  - `resources/views/admin/dashboard.blade.php`
  - `resources/views/layouts/app.blade.php`

## 8) Catatan Setup Laravel
Jalankan di project Laravel aktif:

```bash
composer require laravel/sanctum maatwebsite/excel
php artisan migrate
php artisan storage:link
```

Pastikan middleware alias `admin` terdaftar di kernel.
