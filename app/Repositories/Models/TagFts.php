<?php namespace Owl\Repositories\Models;

use Illuminate\Database\Eloquent\Model;
use Owl\Libraries\FtsUtils;

class TagFts extends Model {
    protected $table = 'tags_fts';

    public $timestamps = false;
    protected $fillable = ['tag_id','words'];

    public static function match($str, $limit=10, $offset=0) {
        $query = <<<__SQL__
            SELECT
              ta.name
            FROM
              tags_fts fts 
            INNER JOIN
              tags ta ON ta.id = fts.tag_id
            WHERE
              fts.words MATCH :match
            ORDER BY
              ta.updated_at DESC
            LIMIT 
              $limit 
            OFFSET
              $offset 
__SQL__;
        return \DB::select( \DB::raw($query), array( 'match' => FtsUtils::createMatchWord($str) ));
    }



}
