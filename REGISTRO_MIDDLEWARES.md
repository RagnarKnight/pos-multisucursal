# Registrar middlewares en bootstrap/app.php

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->appendToGroup('web', [
        \App\Http\Middleware\EnsureUserIsActive::class,
        \App\Http\Middleware\SingleSession::class,
    ]);
})
```

## Orden importa:
1. `EnsureUserIsActive` — verifica que el usuario no fue desactivado
2. `SingleSession`      — verifica que la sesión actual es la última registrada

## Cambio de nombre del comando de backup:
El comando cambió de `db:backup` a `backup:full`

Actualizar el crontab:
```bash
* * * * * cd /ruta/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

Opciones manuales:
```bash
php artisan backup:full             # DB + imágenes
php artisan backup:full --solo-db   # solo SQLite
php artisan backup:full --solo-imgs # solo imágenes
```
