<?php namespace Owl\Services;

use Owl\Repositories\Models\User;
use Owl\Repositories\Models\LoginToken;
use Carbon\Carbon;

class AuthService extends Service
{
    /*
     * ログイン情報を使ってログインを試行する
     *
     * @param array ログイン情報（username, password）
     * @return array
     */
	public function attempt(array $credentials = [], $remember = false)
    {
        // パスワードのチェック
        if ($this->checkPassword($credentials['username'], $credentials['password'])) {
            $user_db = User::where('username', $credentials['username'])->first();
            $this->login($user_db, $remember);
            return true;
        }
        return false;
    }

    /*
     * 認証済みのユーザー情報を使ってログイン処理を行う
     *
     * @param object ユーザー情報（id, username, email, password）
     * @return array
     */
    public function login(User $user, $remember = false)
    {
        $this->setUser($user);
        if ($remember) {
            $this->setRememberToken($user->id);
        }
    }

    /*
     * ログインユーザーの情報をセッションに保存する
     *
     * @param object ユーザー情報（username, email, password）
     * @return void
     */
    public function setUser(User $user)
    {
        // Userのセッションを削除
        \Session::forget("User");

        // ログインに成功したのでセッションIDを再生成
        \Session::regenerate();

        // ログインユーザーの情報を保存
        \Session::push('User.id', $user->id);
        \Session::push('User.username', $user->username);
        \Session::push('User.email', $user->email);
        \Session::save();
    }

    /*
     * ログインユーザーの情報をセッションから削除する
     *
     * @return void
     */
    public function unsetUser()
    {
        if (\Session::has("User")) {
            \Session::forget("User");
            \Session::save();
        }
    }


    /*
     * オートログインのチェックを行う
     *
     * @return void
     */
    public function autoLoginCheck()
    {
        $token = \Request::cookie('remember_token');
        if ($user = $this->getUserByToken($token)) {
            $this->deleteOldRememberToken($token);
            $remember = true;
            $this->login($user, $remember);
            return true;
        }
        return false;
    }

    /*
     * RememberTokenを元にユーザー情報を取得する
     *
     * @return Object User
     */
    public function getUserByToken($token)
    {
        $TWO_WEEKS = 14;
        $limit = Carbon::now()->subDays($TWO_WEEKS);

        $tokenResult = LoginToken::where('token', $token)->where('updated_at', '>', $limit)->first();
        if (isset($tokenResult)) {
            $user = User::where('id', $tokenResult->user_id)->first();
            return $user;
        } else {
            return false;
        }
    }

    /*
     * RememberTokenをセットする
     *
     * @return void
     */
    public function setRememberToken($userId)
    {
        // Tokenの生成
        $TOKEN_LENGTH = 16; //16*2=32桁
        $token = bin2hex(openssl_random_pseudo_bytes($TOKEN_LENGTH));

        // TokenをDBに登録
        $loginToken = new LoginToken();
        $loginToken->token = $token;
        $loginToken->user_id = $userId;
        $loginToken->save();

        // TokenをCookieに登録
        $TWO_WEEKS = 14;
        $limit = Carbon::now()->addDays($TWO_WEEKS);
        \Cookie::queue('remember_token', $token, $limit->diffInMinutes(Carbon::now()));
    }

    /*
     * 古くなったRememberTokenをDBから削除する
     *
     * @return void
     */
    public function deleteOldRememberToken($token)
    {
        LoginToken::where('token', '=', $token)->delete();
    }

    /*
     * RememberTokenCookieを削除する
     *
     * @return cookie
     */
    public function deleteRememberTokenCookie()
    {
        \Cookie::queue('remember_token', '', -1);
    }

    /*
     * 指定されたユーザーのパスワードが、渡されたパスワードと等しいかチェックする
     *
     * @return boolean
     */
    public function checkPassword($username, $password)
    {
        // DBからハッシュされているパスワードを取得
        $user_db = User::where('username', $username)->first();
        if (!isset($user_db->id)) {
            return false; // そんな名前のユーザーはいません
        }

        if (password_verify($password, $user_db->password)) {
            return true;
        }
        return false;
    }

    /*
     * パスワードを再設定する
     *
     * @return boolean
     */
    public function attemptResetPassword($username, $password)
    {
        $user = User::where('username', $username)->first();
        $user->password = password_hash($password, PASSWORD_DEFAULT);

        if($user->save()){
            return true;
        }else{
            return false;
        }
    }
}
