<?php namespace Owl\Http\Controllers;

use Owl\Repositories\Models\User;
use Illuminate\Http\Request;

class SignUpController extends Controller {


    /*
     * 新規会員登録：登録処理
     */
    public function register(Request $request){

        $data = $request->all();

        // バリデーションルールの作成
        $valid_rule = array(
            'username' => 'required|alpha_num|reserved_word|max:30|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|alpha_num|min:4'
        );

        // バリデーション実行
        $validator = \Validator::make(\Input::all(), $valid_rule);

        // 失敗の場合
        if ($validator->fails()) {
            return \Redirect::back()->withErrors($validator)->withInput();
        }

        // 成功の場合
        try {
            // ユーザーの作成
            $user = new User;
            $user->fill(array(
                'username' => \Input::get('username'),
                'email' => \Input::get('email'),
                'password' => password_hash(\Input::get('password'), PASSWORD_DEFAULT)
            ));
            $user->save();

            return \Redirect::to('login')->with('status', '登録が完了しました。');

        } catch (\Exception $e) {
            return \Redirect::back()
                ->withErrors(array('warning' => 'システムエラーが発生したため登録に失敗しました。'))
                ->withInput();
        }
    }
}
