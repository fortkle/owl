<?php namespace Owl\Repositories\Models;

use Illuminate\Database\Eloquent\Model;
use Owl\Libraries\FtsUtils;

class Tag extends Model {
    protected $guarded = array();

    public function item() {
        return $this->belongsToMany('Owl\Repositories\Models\Item');
    }

    public function getAllTags()
    {
        $query = <<<__SQL__
            SELECT
                count(t.name) as count, t.id, t.name, it.item_id, i.title, i.open_item_id
            FROM
                tags as t
                LEFT JOIN
                    item_tag as it ON t.id = it.tag_id
                LEFT JOIN
                    items as i ON it.item_id = i.id
            WHERE
                i.published = 2
            GROUP BY
                t.id
            ORDER BY count DESC
__SQL__;
        return \DB::select($query);
    }

    public static function getTagIdsByTagNames($tag_names) {
        $tag_ids = array();

        foreach($tag_names as $tag_name) {
            $tag_name = trim(mb_convert_kana( $tag_name, "&quot;s&quot;"));
            $tag_name = str_replace(" ","", $tag_name);
            if (empty($tag_name)) {
                continue;
            }
            $tag_name = mb_strtolower($tag_name);
            $tag = Tag::firstOrCreate(array('name' => $tag_name))->toArray();
            TagFts::firstOrCreate(array('tag_id' => $tag['id'], 'words' => FtsUtils::toNgram($tag_name)));
            $tag_ids[] = (string)$tag['id'];
        }
        return $tag_ids;
    }
}
