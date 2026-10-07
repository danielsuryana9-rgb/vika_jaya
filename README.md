# Vika Jaya – Web Owner (Laravel 12)

Tahap ini: **login Owner + dashboard + kerangka menu**. Menu lain masih halaman kosong.
Dashboard membaca data dari database MySQL (bukan data statis).

## Kebutuhan
PHP 8.2+ (ekstensi pdo_mysql), Composer, MySQL/MariaDB (XAMPP atau Laragon cukup).

## Cara menjalankan
```bash
composer install
copy .env.example .env        # Linux/Mac: cp .env.example .env
php artisan key:generate
```
Buat database kosong bernama `vika_jaya` (utf8mb4) di phpMyAdmin, sesuaikan `DB_USERNAME` / `DB_PASSWORD` di `.env`, lalu:
```bash
php artisan migrate --seed
php artisan serve
```
Buka http://localhost:8000

## Akun contoh
| Peran | Username | Password |
|---|---|---|
| Owner (web) | owner | owner123 |
| Kasir (mobile, nanti) | kasir1 / kasir2 | kasir123 |

Hanya Owner yang bisa login di web. Ganti password sebelum dipakai sungguhan.

## Struktur penting
- `database/migrations/` : 13 tabel sesuai ERD (kunci utama memakai nama `id_user`, `id_produk`, dst.)
- `database/seeders/DatabaseSeeder.php` : data contoh (hapus kalau sudah ada data asli)
- `app/Http/Controllers/` : `AuthController`, `DashboardController`, `PageController`
- `resources/views/` : `auth/login`, `layouts/app`, `dashboard`
- `public/css/app.css` : seluruh gaya (tanpa npm / Vite)

## Langkah berikutnya
Halaman Produk (CRUD), lalu REST API di `routes/api.php` (Laravel Sanctum) untuk aplikasi mobile Kasir.
# Vika Jaya
