<?php namespace Owl\Http\Controllers;

use Owl\Repositories\Models\User;
use Owl\Repositories\Models\Item;
use Owl\Repositories\Models\Template;
use Owl\Services\UserService;
use Owl\Services\AuthService;
use Owl\Http\Requests\UserRegisterRequest;

class UserController extends Controller
{
    protected $userService;
    protected $authService;

    public function __construct(UserService $userService, AuthService $authService)
    {
        $this->userService = $userService;
        $this->authService = $authService;
        parent::__construct($userService);
    }

    /*
     * 新規会員登録：入力画面
     */
    public function signup()
    {
        if ($this->userService->getCurrentUser()) {
            return redirect('/');
        }
        return view('signup.index');
    }

    /*
     * 新規会員登録：登録処理
     */
    public function register(UserRegisterRequest $request){

        $credentials = $request->only('username', 'email', 'password');
        try {
            $user = $this->userService->createUser($credentials);
            return \Redirect::to('login')->with('status', '登録が完了しました。');
        } catch (\Exception $e) {
            return \Redirect::back()
                ->withErrors(array('warning' => 'システムエラーが発生したため登録に失敗しました。'))
                ->withInput();
        }
    }

    public function show($username){
        $loginUser = $this->currentUser;
        $user = User::where('username', '=', $username)->first();
        if ($user == null){
            \App::abort(404);
        }

        if ($loginUser->id === $user->id){
            $items = Item::with('user')
                        ->where('user_id', $user->id)
                        ->orderBy('id','desc')
                        ->paginate(10);
        } else {
            $items = Item::with('user')
                        ->where('published', '2')
                        ->where('user_id', $user->id)
                        ->orderBy('id','desc')
                        ->paginate(10);
        }

        $templates = Template::all();
        return \View::make('user.show', compact('user', 'items', 'templates'));
    }

    public function edit(){
        $templates = Template::all();
        return \View::make('user.edit', compact('templates'));
    }

    public function update(){
        $loginUser = $this->currentUser;

        // バリデーションルールの作成
        $valid_rule = array(
            "username" => "required|alpha_num|reserved_word|max:30|unique:users,username,$loginUser->id",
            "email" => "required|email|unique:users,email,$loginUser->id",
        );

        // バリデーション実行
        $validator = \Validator::make(\Input::all(), $valid_rule);

        // 失敗の場合
        if ($validator->fails()) {
            return \Redirect::back()->withErrors($validator)->withInput();
        }

        try {
            $user = $this->userService->getUserById($loginUser->id);

            $user->username = \Input::get('username');
            $user->email = \Input::get('email');

            if($user->save()){
                $this->authService->setUser($user);
                return \Redirect::to('user/edit')->with('status', '編集が完了しました。');
            }else{
                \App::abort(500);
            }
        } catch (\Exception $e) {
            return \Redirect::back()
                ->withErrors(array('warning' => 'システムエラーが発生したため編集に失敗しました。'))
                ->withInput();
        }
    }

    public function reset(){
        $loginUser = $this->currentUser;

        // バリデーションルールの作成
        $valid_rule = array(
            "password" => "required|alpha_num|min:4",
            "new_password" => "required|alpha_num|min:4",
        );

        // バリデーション実行
        $validator = \Validator::make(\Input::all(), $valid_rule);

        // 失敗の場合
        if ($validator->fails()) {
            return \Redirect::back()->withErrors($validator)->withInput();
        }

        try {
            $user = $this->userService->getUserById($loginUser->id);

            if(!$this->authService->checkPassword($user->username, \Input::get('password'))){
                return \Redirect::back()
                    ->withErrors(array('warning' => 'パスワードに誤りがあります。'))
                    ->withInput();
            }

            if ($this->authService->attemptResetPassword($user->username, \Input::get('new_password'))){
                return \Redirect::to('user/edit')->with('status', 'パスワード変更が完了しました。');
            }else{
                return \Redirect::back()
                    ->withErrors(array('warning' => 'パスワードリセットに失敗しました。'))
                    ->withInput();
            }

        } catch (\Exception $e) {
            return \Redirect::back()
                ->withErrors(array('warning' => 'システムエラーが発生したためパスワードリセットに失敗しました。'))
                ->withInput();
        }
    }

}
