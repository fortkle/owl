<?php namespace Owl\Http\Controllers;

/**
 * @copyright (c) owl
 */

use Illuminate\Auth\AuthManager;
use Owl\Services\UserService;
use Owl\Services\UserRoleService;

/**
 * Class UserRoleController
 */
class UserRoleController extends Controller
{
    /** @var AuthManager */
    protected $auth;

    /** @var UserService */
    protected $userService;

    /** @var UserRoleService */
    protected $userRoleService;

    /**
     * @param AuthManager      $auth
     * @param UserService      $userService
     * @param UserRoleService  $userRoleService
     */
    public function __construct(
        AuthManager     $auth,
        UserService     $userService,
        UserRoleService $userRoleService
    ) {
        $this->auth            = $auth;
        $this->userService     = $userService;
        $this->userRoleService = $userRoleService;
    }

    /**
     * オーナー初期登録画面
     *
     * @return \Illuminate\View\View
     * @throw \Exception
     */
    public function initial()
    {
        $owners = $this->userService->getOwners();
        if (!empty($owners)) {
            abort(500);
        }

        $user = $this->auth->user();
        if (is_null($user)) {
            abort(404);
        }

        return view('user.role.initial', compact('user'));
    }

    /**
     * オーナー登録処理
     *
     * @return \Illuminate\View\View
     * @throw \Exception
     */
    public function initialRegister()
    {
        $owners = $this->userService->getOwners();
        if (!empty($owners)) {
            abort(500);
        }

        $user = $this->auth->user();
        if (is_null($user)) {
            abort(404);
        }

        $user->role = UserRoleService::ROLE_ID_OWNER;
        $updateUser = $this->userService->update($user->getAuthIdentifier(), $user->name(), $user->email(), $user->role());
        $this->auth->login($updateUser);

        return view('user.role.initialComplete');
    }
}
