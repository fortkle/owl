<?php namespace Owl\Repositories\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class LoginToken extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'login_tokens';

    protected $fillable = ['user_id', 'token'];

}
