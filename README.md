# Absensi Karyawan

Aplikasi absensi berbasis Laravel, Filament, Breeze, dan Filament Shield. Admin mengelola departemen, team, jabatan, karyawan, user, role, dan data absensi. Pengguna karyawan mencatat absensi dan melihat riwayat yang diizinkan untuk role serta team-nya.

## Menjalankan aplikasi

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm install
npm run build
php artisan serve
```

Pastikan koneksi database pada `.env` sudah dikonfigurasi sebelum menjalankan migrasi. Lewati `Copy-Item` jika file `.env` sudah tersedia.

## Menyiapkan administrator

Daftarkan akun pertama melalui `/register`, lalu beri akses administrator menggunakan ID akun tersebut:

```powershell
php artisan shield:super-admin --user=1
```

Ganti `1` dengan ID akun yang benar. Administrator masuk melalui `/admin`; pengguna lain masuk melalui `/attendances`.

## Alur data

1. Buat departemen, team, dan jabatan pada panel admin.
2. Buat data karyawan dan hubungkan dengan team serta jabatan.
3. Hubungkan akun ke karyawan pada menu User. Role akun disinkronkan dengan jabatan karyawan.
4. Karyawan mencatat absensi melalui `/attendances`. Absensi kedua pada tanggal yang sama ditandai sebagai overtime dan membutuhkan alasan.

Role atasan hanya dapat melihat absensi karyawan dalam cakupan team; department manager dapat melihat cakupan departemennya. Akun administrator dapat mengelola seluruh data melalui Filament Shield.

Jalankan pengujian dengan `php artisan test`.
