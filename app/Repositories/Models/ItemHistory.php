<?php namespace Owl\Repositories\Models;

use Illuminate\Database\Eloquent\Model;

class ItemHistory extends Model {
    protected $table = 'items_history';

    protected $fillable = ['item_id','user_id','title','body','published', 'open_item_id', 'created_at', 'updated_at'];

    public function user() {
        return $this->belongsTo('Owl\Repositories\Models\User');
    }

    public static function insertHistory($item, $user){
        if ($item->published === '0') {
            return;
        }

        $his = new ItemHistory;
        $his->fill(array(
            'item_id'=> $item->id,
            'user_id'=> $user->id,
            'open_item_id' => $item->open_item_id,
            'title'=> $item->title,
            'body'=> $item->body,
            'published'=> $item->published
        ));
        $his->save();

        return $his;
    }

    public static function insertPastHistory($item){
        $his = new ItemHistory;
        $his->fill(array(
            'item_id'=> $item->id,
            'user_id'=> $item->user_id,
            'open_item_id' => $item->open_item_id,
            'title'=> $item->title,
            'body'=> $item->body,
            'published'=> $item->published,
            'created_at'=> $item->created_at,
            'updated_at'=> $item->updated_at
        ));
        $his->save();

        return $his;
    }

    public static function createOpenItemId(){
        return substr(md5(uniqid(rand(),1)),0,20);
    }
}
