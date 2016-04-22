<?php namespace Owl\Http\Middleware;

/**
 * @copyright (c) owl
 */

use Illuminate\Auth\AuthManager;
use Closure;
use Owl\Services\UserService;

/**
 * Class NotLoginCheckMiddleware
 */
class NotLoginCheckMiddleware
{
    /** @var AuthManager */
    protected $auth;

    /**
     * @param AuthManager  $auth
     */
    public function __construct(AuthManager $auth)
    {
        $this->auth = $auth;
    }

    /**
     * ログイン済みユーザはメインページへリダイレクト
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->check()) {
            return redirect()->route('index');
        }

        return $next($request);
    }
}
