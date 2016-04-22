<?php namespace Owl\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * @var array
     */
    protected $middleware = [
        'Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode',
        'Illuminate\Cookie\Middleware\EncryptCookies',
        'Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse',
        'Illuminate\Session\Middleware\StartSession',
        'Illuminate\View\Middleware\ShareErrorsFromSession',
        'Owl\Http\Middleware\VerifyCsrfToken',
        'Owl\Http\Middleware\CheckRoleColumn',
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'login' => 'Owl\Http\Middleware\LoginCheckMiddleware',
        'notLogin' => 'Owl\Http\Middleware\NotLoginCheckMiddleware',
        'owner' => 'Owl\Http\Middleware\OwnerCheckMiddleware',
    ];
}
