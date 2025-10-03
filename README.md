<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center"><a href="https://tryout.iti.ac.id/" target="_blank"> Link Website Question Bank and Tryout SNBT Public </a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# SNBT Tryout Laravel

Aplikasi Tryout SNBT berbasis Laravel dengan database **MySQL**.

---

## 🚀 Cara Menjalankan Project

Jalankan perintah berikut di terminal:

```bash
# Install dependency
composer install

# Buat database di phpMyAdmin sesuai dengan nama pada file .env
# Contoh konfigurasi database di file .env:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=snbt
# DB_USERNAME=root
# DB_PASSWORD=

# Sesuaikan DB_USERNAME dan DB_PASSWORD dengan web server lokal anda.
# Default Laravel biasanya menggunakan root tanpa password.

# Edit file .env untuk konfigurasi aplikasi lokal:
# APP_NAME=Laravel
# APP_ENV=local
# APP_KEY=base64:s3lQKM21auTjQ296GahD4OJA1bBA2HkzDRirdlq/bjY=
# APP_DEBUG=true
# APP_URL=http://localhost

# Generate app key, buat symbolic link storage, lalu jalankan server
php artisan key:generate && php artisan storage:link && php artisan serve


## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
