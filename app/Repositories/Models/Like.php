<?php namespace Owl\Repositories\Models;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $fillable = array('user_id', 'item_id');

    public function user() {
        return $this->belongsTo('Owl\Repositories\Models\User');
    }

    public function item() {
        return $this->belongsTo('Owl\Repositories\Models\Item');
    }
}
