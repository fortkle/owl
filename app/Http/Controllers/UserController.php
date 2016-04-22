<?php namespace Owl\Http\Controllers;

/**
 * @copyright (c) owl
 */

use Illuminate\Auth\AuthManager;
use Owl\Services\UserService;
use Owl\Services\UserRoleService;
use Owl\Services\AuthService;
use Owl\Services\ItemService;
use Owl\Services\TemplateService;
use Owl\Services\MailNotifyService;
use Owl\Http\Requests\UserRegisterRequest;
use Owl\Http\Requests\UserRoleUpdateRequest;
use Owl\Http\Requests\UserPasswordRequest;
use Owl\Http\Requests\UserUpdateRequest;

/**
 * Class UserController
 */
class UserController extends Controller
{
    /** @var UserService */
    protected $userService;

    /** @var UserRoleService */
    protected $userRoleService;

    /** @var AuthService */
    protected $authService;

    /** @var ItemService */
    protected $itemService;

    /** @var TemplateService */
    protected $templateService;

    /**
     * @param UserService      $userService
     * @param UserRoleService  $userRoleService
     * @param AuthService      $authService
     * @param ItemService      $itemService
     * @param TemplateService  $templateService
     */
    public function __construct(
        UserService $userService,
        UserRoleService $userRoleService,
        AuthService $authService,
        ItemService $itemService,
        TemplateService $templateService
    ) {
        $this->userService = $userService;
        $this->userRoleService = $userRoleService;
        $this->authService = $authService;
        $this->itemService = $itemService;
        $this->templateService = $templateService;
    }

    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $users = $this->userService->getAll();
        $ret = $this->userRoleService->getAll();
        $roles = [];
        foreach ($ret as $role) {
            $roles[$role->id] = $role->name;
        }
        return view('user.index', compact('users', 'roles'));
    }

    /**
     * @param UserRoleUpdateRequest $request
     * @param int                   $user_id
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throw \Exception
     */
    public function roleUpdate(UserRoleUpdateRequest $request, $user_id)
    {
        $user = $this->userService->getById($user_id);
        if (empty($user)) {
            abort(500);
        }

        $role_id = $request->get('role_id');
        $roles = $this->userRoleService->getAll();
        if (!isset($roles[$role_id - 1])) {
            abort(500);
        }
        $updateUser = $this->userService->update($user->id, $user->username, $user->email, $role_id);

        $users = $this->userService->getAll();
        $ret = $this->userRoleService->getAll();
        $roles = [];
        foreach ($ret as $role) {
            $roles[$role->id] = $role->name;
        }

        $mes = '権限を変更しました。変更を有効にするためには '
            . $user->username . ' がログインし直す必要があります。';
        return redirect('manage/user/index')->with('message', $mes);
    }

    /*
     * 新規会員登録：入力画面
     *
     * @return \Illuminate\View\View
     */
    public function signup()
    {
        return view('signup.index');
    }

    /*
     * 新規会員登録：登録処理
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(UserRegisterRequest $request)
    {
        $credentials = $request->only('username', 'email', 'password');
        try {
            $user = $this->userService->create($credentials);
            return \Redirect::to('login')->with('status', '登録が完了しました。');
        } catch (\Exception $e) {
            return \Redirect::back()
                ->withErrors(['warning' => 'システムエラーが発生したため登録に失敗しました。'])
                ->withInput();
        }
    }

    /**
     * @param string       $username
     * @param AuthManager  $auth
     *
     * @return \Illuminate\View\View
     * @throw \Exception
     */
    public function show($username, AuthManager $auth)
    {
        if ($auth->guest()) {
            abort(404);
        }

        $loginUser = $auth->user();
        $user      = $this->userService->getByUsername($username);

        if ($loginUser->getAuthIdentifier() === $user->id) {
            $items = $this->itemService->getRecentsByLoginUserIdWithPaginate($user->id);
        } else {
            $items = $this->itemService->getRecentsByUserIdWithPaginate($user->id);
        }

        $templates = $this->templateService->getAll();
        return view('user.show', compact('user', 'items', 'templates'));
    }

    /**
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        $templates = $this->templateService->getAll();
        return \View::make('user.edit', compact('templates'));
    }

    /**
     * @param UserUpdateRequest  $request
     * @param AuthManager        $auth
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throw \Exception
     */
    public function update(UserUpdateRequest $request)
    {
        $loginUser = $auth->user();

        try {
            $user = $this->userService->update(
                $loginUser->getAuthIdentifier(),
                $request->get('username'),
                $request->get('email'),
                $loginUser->role()
            );

            if ($user) {
                $auth->login($user);
                return redirect()->to('user/edit')->with('status', '編集が完了しました。');
            }

            abort(500);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['warning' => 'システムエラーが発生したため編集に失敗しました。'])
                ->withInput();
        }
    }

    /**
     * @param UserPasswordRequest  $request
     * @param AuthManager          $auth
     */
    public function password(UserPasswordRequest $request, AuthManager $auth)
    {
        $loginUser = $auth->user();

        try {
            $user = $this->userService->getById($loginUser->getAuthIdentifier());

            if (!$this->authService->checkPassword($user->username, $request->get('password'))) {
                return redirect()->back()
                    ->withErrors(array('warning' => 'パスワードに誤りがあります。'))
                    ->withInput();
            }

            if ($this->authService->attemptResetPassword($user->username, \Input::get('new_password'))) {
                return redirect()->to('user/edit')->with('status', 'パスワード変更が完了しました。');
            }

            return redirect()->back()
                ->withErrors(array('warning' => 'パスワードリセットに失敗しました。'))
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['warning' => 'システムエラーが発生したためパスワードリセットに失敗しました。'])
                ->withInput();
        }
    }
}
