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

<p>1. Install dependency Laravel dengan Composer:</p>
```bash
composer install

<p>2. Buat database di <b>phpMyAdmin</b> lalu beri nama sesuai dengan nama database pada file <code>.env</code>.  
Untuk memastikan kesesuaian, cek bagian konfigurasi berikut di file <code>.env</code>:</p>
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=snbt
DB_USERNAME=root
DB_PASSWORD=

<p>3. Sesuaikan DB_USERNAME dan DB_PASSWORD dengan server lokal Anda.
Default Laravel biasanya root tanpa password</p>
```bash
APP_ENV=local
APP_KEY=base64:s3lQKM21auTjQ296GahD4OJA1bBA2HkzDRirdlq/bjY=
APP_DEBUG=true
APP_URL=http://localhost

<p>4. Generate key, buat storage link, dan jalankan server Laravel</p>
```bash
php artisan key:generate
```bash
php artisan storage:link
```bash
php artisan serve

<p>5. Buka aplikasi di browser</p>
```bash
http://localhost:8000


## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
