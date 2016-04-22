<?php namespace Owl\Http\Middleware;

/**
 * @copyright (c) owl
 */

use Closure;
use Illuminate\Auth\AuthManager;

/**
 * Class LoginCheckMiddleware
 */
class LoginCheckMiddleware
{
    /** @var AuthManager */
    protected $auth;

    /**
     * Create LoginCheckMiddleware.
     *
     * @param AuthManager  $auth
     */
    public function __construct(AuthManager $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->guest()) {
            return redirect('login');
        }

        return $next($request);
    }
}
