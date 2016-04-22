<?php namespace Owl\Http\ViewComposers;

/**
 * @copyright (c) owl
 */

use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\View\View;

/**
 * Class UserComposer
 */
class UserComposer
{
    /** @var AuthManager */
    protected $auth;

    /**
     * Create a UserComposer
     *
     * @param AuthManager  $auth
     */
    public function __construct(AuthManager $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Share the user information to View
     *
     * @param View  $view
     */
    public function compose(View $view)
    {
        if ($loginUser = $this->auth->user()) {
            $view->with('User', $loginUser);
        }
    }
}
