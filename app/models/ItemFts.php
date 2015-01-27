<?php

class ItemFts extends Eloquent{
    protected $table = 'items_fts';

    public $timestamps = false;

    private static function stockSrchQuery(){
        return 'INNER JOIN stocks st ON it.id = st.item_id AND st.user_id= :user_id ';
    }

    public static function match($vals, $limit=10, $offset=0) {
        $stock_srch = isset($vals['user_id']) ? self::stockSrchQuery() : "";
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
            $stock_srch 
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
        return DB::select( DB::raw($query), $vals);
    }

    public static function matchCount($vals){
        $stock_srch = isset($vals['user_id']) ? self::stockSrchQuery() : "";
        $query = <<<__SQL__
            SELECT
              COUNT(*) as count
            FROM
              items_fts fts 
            INNER JOIN
              items it ON it.id = fts.item_id AND it.published = 2
            $stock_srch 
            WHERE
              fts.words MATCH :match
__SQL__;
        return DB::select( DB::raw($query),  $vals);
    }

}
