<?php namespace Owl\Repositories\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model {
    protected $table = 'templates';

    protected $fillable = ['title','display_title','body','tags'];
}
