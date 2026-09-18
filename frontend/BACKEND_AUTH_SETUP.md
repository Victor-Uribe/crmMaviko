# MAVIKO CRM — Autenticación Laravel + Sanctum

El frontend v2 espera autenticación SPA con cookies de sesión de Laravel Sanctum. No se recomienda guardar el token del CRM en localStorage.

## 1. Variables de entorno del backend

```env
FRONTEND_URL=http://localhost:5173
SANCTUM_STATEFUL_DOMAINS=localhost:5173,127.0.0.1:5173
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=false
```

Después:

```bash
php artisan config:clear
```

## 2. Verificar middleware stateful

En `bootstrap/app.php`, dentro de `withMiddleware`, Laravel debe tener:

```php
use Illuminate\Foundation\Configuration\Middleware;

->withMiddleware(function (Middleware $middleware): void {
    $middleware->statefulApi();
})
```

## 3. CORS

Si `config/cors.php` existe, usa una configuración equivalente a:

```php
return [
    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
        'login',
        'logout',
    ],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:5173',
        'http://127.0.0.1:5173',
    ],

    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
```

## 4. AuthController

Crear:

```bash
php artisan make:controller AuthController
```

`app/Http/Controllers/AuthController.php`:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $remember = (bool) ($credentials['remember'] ?? false);
        unset($credentials['remember']);

        if (!Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales no son correctas.'],
            ]);
        }

        $request->session()->regenerate();

        return response()->json([
            'message' => 'Sesión iniciada correctamente.',
            'data' => $request->user(),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Sesión cerrada correctamente.',
        ]);
    }
}
```

## 5. Rutas de sesión

En `routes/web.php`:

```php
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('guest');

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth');
```

En `routes/api.php`:

```php
use Illuminate\Http\Request;

Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
    return response()->json([
        'data' => $request->user(),
    ]);
});
```

## 6. Proteger el CRM

Todas las rutas comerciales deben estar dentro de `auth:sanctum`. Ejemplo:

```php
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // prospects
    // contacts
    // opportunities
    // followups
    // services
    // trash / restore
});
```

No dejes el dashboard o los prospectos fuera de este grupo.

## 7. Crear el primer usuario

No habrá registro público. Crea el usuario administrador desde Tinker:

```bash
php artisan tinker
```

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::updateOrCreate(
    ['email' => 'TU_CORREO'],
    [
        'name' => 'Administrador MAVIKO',
        'password' => Hash::make('TU_CONTRASENA_SEGURA'),
    ]
);
```

No compartas la contraseña en el chat ni la subas a Git.
