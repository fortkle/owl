<?php namespace Owl\Repositories\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model {
    public function item() {
        return $this->belongsTo('Owl\Repositories\Models\Item');
    }

    public function user() {
        return $this->belongsTo('Owl\Repositories\Models\User');
    }
}
