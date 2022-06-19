# Code Documentation

## Tech Stack
1. PHP version: 8.0.3
2. Laravel version: 8.x
3. Auth: Laravel Sanctum
4. Database: MySQL

## Installation
1. Clone this repository
```cmd
https://github.com/dinafauzyh/backend-test.git
```
2. Update composer
```cmd
composer update
```

3. Generate key
```cmd
php artisan key:generate
```

4. Migration
```cmd
php artisan migrate --seed
```

5. Publish Laravel Sanctum
```cmd
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

6. Run server
```cmd
php artisan serve
```

## Link Documentation Postman
https://documenter.getpostman.com/view/18321467/UzBmMmvX