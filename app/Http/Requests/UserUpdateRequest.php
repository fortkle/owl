<?php namespace Owl\Http\Requests;

/**
 * @copyright (c) owl
 */

use Illuminate\Auth\AuthManager;
use Owl\Http\Requests\Request;

class UserUpdateRequest extends Request
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
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $loginUserId = $this->auth->user()->getAuthIdentifier();

        return [
            "username" => "required|alpha_num|reserved_word|max:30|unique:users,username,$loginUserId",
            "email"    => "required|email|unique:users,email,$loginUserId",
        ];
    }
}
