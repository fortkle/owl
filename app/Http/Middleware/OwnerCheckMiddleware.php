<?php namespace Owl\Http\Middleware;

/**
 * @copyright (c) owl
 */

use Closure;
use Illuminate\Auth\AuthManager;
use Owl\Services\UserRoleService;

/**
 * Class OwnerCheckMiddleware
 */
class OwnerCheckMiddleware
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
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // 未ログインユーザはlogin画面へリダイレクト
        if ($this->auth->guest()) {
            return redirect()->route('login.index');
        }

        // オーナーでないログインユーザはメインページへリダイレクト
        if ($this->auth->role() !== UserRoleService::ROLE_ID_OWNER) {
            return redirect()->route('index');
        }

        return $next($request);
    }
}
