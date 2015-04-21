<?php namespace Owl\Repositories\Models;

use Illuminate\Database\Eloquent\Model;
use Owl\Libraries\FtsUtils;

class ItemFts extends Model 
{
    protected $table = 'items_fts';

    public $timestamps = false;

    public static function match($str, $limit=10, $offset=0) {
        $query = <<<__SQL__
            SELECT
              it.title,
              it.updated_at,
              it.open_item_id,
              us.email,
              us.username
            FROM
              items_fts fts 
            INNER JOIN
              items it ON it.id = fts.item_id AND it.published = 2
            INNER JOIN
              users us ON it.user_id = us.id
            WHERE
              fts.words MATCH :match
            ORDER BY
              it.updated_at DESC, it.id DESC
            LIMIT 
              $limit 
            OFFSET
              $offset 
__SQL__;
        return \DB::select( \DB::raw($query), array( 'match' => FtsUtils::createMatchWord($str) ));
    }

    public static function matchCount($str){
        $query = <<<__SQL__
            SELECT
              COUNT(*) as count
            FROM
              items_fts fts 
            INNER JOIN
              items it ON it.id = fts.item_id AND it.published = 2
            WHERE
              fts.words MATCH :match
__SQL__;
        return \DB::select( \DB::raw($query), array( 'match' => FtsUtils::createMatchWord($str) ));
    }

}
