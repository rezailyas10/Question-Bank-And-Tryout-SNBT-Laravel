<p align="center">
<a href="https://laravel.com" target="_blank">
<img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
</a>
</p>


<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

<p align="center"><b>SNBT Tryout Laravel</b></p>

<p align="center">
Aplikasi Tryout SNBT berbasis Laravel dengan database <b>MySQL</b>.
</p>

<p align="center">
<a href="https://tryout.iti.ac.id/" target="_blank"> Link Website Question Bank and Tryout SNBT Public </a>
</p>
---

<p><b>🚀 Cara Menjalankan Project di Localhost</b></p>

# Install dependency Laravel
composer install

# Buat database di phpMyAdmin sesuai nama database di file .env
# Contoh konfigurasi .env:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=snbt
DB_USERNAME=root
DB_PASSWORD=

# Sesuaikan DB_USERNAME dan DB_PASSWORD dengan server lokal Anda (default root tanpa password)

# Atur konfigurasi Laravel di .env
APP_ENV=local
APP_KEY=base64:s3lQKM21auTjQ296GahD4OJA1bBA2HkzDRirdlq/bjY=
APP_DEBUG=true
APP_URL=http://localhost

# Generate key aplikasi, buat symbolic link storage, dan jalankan server Laravel
php artisan key:generate && php artisan storage:link && php artisan serve

# Akses aplikasi di browser: http://localhost:8000


## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
