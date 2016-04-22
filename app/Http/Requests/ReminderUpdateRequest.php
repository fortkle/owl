<?php namespace Owl\Http\Requests;

use Owl\Http\Requests\Request;

class ReminderUpdateRequest extends Request
{

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
        return [
            'new_password' => 'required|alpha_num|min:4',
            'token'        => 'required'
        ];
    }
}
