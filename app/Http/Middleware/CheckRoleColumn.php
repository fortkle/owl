<?php namespace Owl\Http\Middleware;

/**
 * @copyright (c) owl
 */

use Closure;
use Illuminate\Auth\AuthManager;

/**
 * Class CheckRoleColumn
 */
class CheckRoleColumn
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
     * roleカラムのないログインユーザはリログイン処理を行う
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // TODO: delete after release.
        $loginUser = $this->auth->user();

        if (!is_null($loginUser) && !isset($loginUser->role)) {
            $user = $this->userService->getById($loginUser->id);
            $this->auth->login($user);
        }

        return $next($request);
    }
}
