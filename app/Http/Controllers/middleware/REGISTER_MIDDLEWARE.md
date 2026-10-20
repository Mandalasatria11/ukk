Register the middleware alias in `bootstrap/app.php` (Laravel 11/12), inside `withMiddleware`:

```php
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Configuration\Middleware;

->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => RoleMiddleware::class,
    ]);
})
```
