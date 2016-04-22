<?php namespace Owl\Http\Controllers;

/**
 * @copyright (c) owl
 */

use Illuminate\Auth\AuthManager;
use Owl\Http\Requests\AuthAttemptRequest;

/**
 * Class AuthController
 */
class AuthController extends Controller
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

    /*
     * ログイン画面
     *
     * @return \Illuminate\View\View
     */
    public function login()
    {
        return view('login.index');
    }

    /*
     * ログイン認証
     *
     * @param AuthAttemptRequest  $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function attempt(AuthAttemptRequest $request)
    {
        $credentials = $request->only('username', 'password');

        if ($user = $this->auth->attempt($credentials, $request->has('remember'))) {
            return redirect()->route('index');
        } else {
            return redirect()->back()
                ->withErrors(array('warning' => 'ユーザ名又はパスワードが正しくありません'))
                ->withInput();
        }
    }

    /*
     * ログアウト処理
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        $this->auth->logout();

        return redirect()->route('login.index');
    }
}
