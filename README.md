<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Prueba técnica para evertec

## Instalación
### Requerimientos:
- PHP 8.+
- Composer
- NodeJs
- MySql
### Pasos:
- git clone https://github.com/kevalo/test_evertec.git test_evertec
- cd test_evertec
- composer install
- npm install
- cp .env.example .env
- php artisan key:generate
- configurar los datos de conexión a la base de datos en el archivo .env
---
- Agregar al archivo .env las siguientes variables de entorno para la conexión a PlaceToPay:
- PTP_BASE_URL=https://checkout-co.placetopay.dev
- PTP_LOGIN=MyLogin
- PTP_SECRET_KEY=MySecretKey
- PTP_SESSION_LIMIT=60
---
- php artisan migrate
- php artisan db:seed --class=UserSeeder
- npm run dev
- php artisan serve

Usuario de administración (/login):
- email: admin@email.com
- password: 123456789
