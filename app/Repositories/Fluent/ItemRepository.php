<?php namespace Owl\Repositories\Fluent;

use Owl\Repositories\ItemRepositoryInterface;

class ItemRepository extends AbstractFluent implements ItemRepositoryInterface
{
    protected $table = 'items';

    /**
     * Get a table name.
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->table;
    }

    /**
     * Get a item by item id.
     *
     * @param int $item_id
     * @return stdClass
     */
    public function getById($item_id)
    {
        return \DB::table($this->getTableName())
            ->where('id', $item_id)
            ->first();
    }

    /**
     * Get a item by open item id.
     *
     * @param int $open_item_id
     * @return stdClass
     */
    public function getByOpenItemId($open_item_id)
    {
        return \DB::table($this->getTableName())
            ->where('open_item_id', $open_item_id)
            ->first();
    }

    /**
     * Get a item by open item id with comments.
     *
     * @param int $open_item_id
     * @return \stdClass|null
     */
    public function getByOpenItemIdWithComment($open_item_id)
    {
        // @FIXME
        $item = \DB::table('items')->where('open_item_id', $open_item_id)->first();

        if (is_null($item)) {
            return null;
        }

        $item->user = \DB::table('users')->where('id', $item->user_id)->first();
        $comments = \DB::table('comments')->where('item_id', $item->id)->get();
        $i = 0;
        foreach ($comments as $comment) {
            $comments[$i]->user = \DB::table('users')->where('id', $comment->user_id)->first();
            $i++;
        }
        $item->comment = $comments;
        return $item;
    }

    /**
     * Get all items.
     *
     * @return stdClass
     */
    public function getAll()
    {
        return \DB::table('items')
                    ->select('items.*', 'users.email', 'users.username')
                    ->join('users', 'items.user_id', '=', 'users.id')
                    ->orderBy('updated_at', 'desc')
                    ->get();
    }

    /**
     * Get all published items.
     *
     * @return stdClass
     */
    public function getAllPublished()
    {
        return \DB::table('items')
                    ->select('items.*', 'users.email', 'users.username')
                    ->join('users', 'items.user_id', '=', 'users.id')
                    ->where('published', '2')
                    ->orderBy('updated_at', 'desc')
                    ->paginate(10);
    }

    /**
     * Get flow published items.
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function getAllFlowPublished()
    {
        $pageNumber = \Input::get("page", 1);
        $perPage = 10;
        $offset = $perPage * ($pageNumber - 1);

        $joinSubquery = \DB::table('item_tag')
            ->select('item_tag.item_id')
            ->distinct('item_tag.item_id')
            ->join('tags', 'item_tag.tag_id', '=', 'tags.id')
            ->whereRaw('tags.flow_flag = 1');
        $ret = \DB::table('items as i')
            ->select('i.*', 'u.username', 'u.email')
            ->leftJoin('users as u', 'i.user_id', '=', 'u.id')
            ->leftJoin(\DB::raw('(' . $joinSubquery->toSql() .') as tmp2'), function ($join) {
                $join->on('tmp2.item_id', '=', 'i.id');
            })
            ->whereNotNull('tmp2.item_id')
            ->where('i.published', '2')
            ->orderBy('i.updated_at', 'desc')
            ->skip($offset)
            ->take($perPage)
            ->get();

        $allCount = \DB::table('items')->where('published', '2')->count();
        $noTaggedCount = $allCount - (\DB::table('items')
            ->join('item_tag', 'items.id', '=', 'item_tag.item_id')
            ->where('items.published', '2')
            ->distinct('items.id')
            ->count('items.id'));
        $flowCount = (\DB::table('items')
            ->join('item_tag', 'items.id', '=', 'item_tag.item_id')
            ->join('tags', 'item_tag.tag_id', '=', 'tags.id')
            ->where('tags.flow_flag', '1')
            ->where('items.published', '2')
            ->distinct('items.id')->count('items.id'));
        $stockCount = $allCount - $flowCount;

        $ret = \App::make('Illuminate\Pagination\LengthAwarePaginator', [$ret, $flowCount, $perPage]);
        return $ret;
    }

    /**
     * Get stock published items.
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function getAllStockPublished()
    {
        $pageNumber = \Input::get("page", 1);
        $perPage = 10;
        $offset = $perPage * ($pageNumber - 1);

        $joinSubquery = \DB::table('item_tag')
            ->select('item_tag.item_id')
            ->distinct('item_tag.item_id')
            ->join('tags', 'item_tag.tag_id', '=', 'tags.id')
            ->whereRaw('tags.flow_flag = 1');
        $ret = \DB::table('items as i')
            ->select('i.*', 'u.username', 'u.email')
            ->leftJoin('users as u', 'i.user_id', '=', 'u.id')
            ->leftJoin(\DB::raw('(' . $joinSubquery->toSql() .') as tmp2'), function ($join) {
                $join->on('tmp2.item_id', '=', 'i.id');
            })
            ->whereNull('tmp2.item_id')
            ->where('i.published', '2')
            ->orderBy('i.updated_at', 'desc')
            ->skip($offset)
            ->take($perPage)
            ->get();

        $allCount = \DB::table('items')->where('published', '2')->count();
        $noTaggedCount = $allCount - (\DB::table('items')
            ->join('item_tag', 'items.id', '=', 'item_tag.item_id')
            ->where('items.published', '2')
            ->distinct('items.id')
            ->count('items.id'));
        $flowCount = (\DB::table('items')
            ->join('item_tag', 'items.id', '=', 'item_tag.item_id')
            ->join('tags', 'item_tag.tag_id', '=', 'tags.id')
            ->where('tags.flow_flag', '1')
            ->where('items.published', '2')
            ->distinct('items.id')->count('items.id'));
        $stockCount = $allCount - $flowCount;

        $ret = \App::make('Illuminate\Pagination\LengthAwarePaginator', [$ret, $stockCount, $perPage]);
        return $ret;
    }

    /**
     * Get recent items by user id.
     *
     * @param int $user_id
     * @return stdClass
     */
    public function getRecentsByUserId($user_id)
    {
        $items = \DB::table('items')
                    ->select('items.*', 'users.email as user_email', 'users.username as user_username')
                    ->join('users', 'items.user_id', '=', 'users.id')
                    ->where('items.published', '2')
                    ->where('items.user_id', $user_id)
                    ->orderBy('items.id', 'desc')
                    ->take(5)->get();
        $i = 0;
        foreach($items as $item) {
            $object = app('stdClass');
            $object->email = $item->user_email;
            $object->username = $item->user_username;
            $items[$i]->user = $object;
            $i++;
        }

        return $items;
    }

    /**
     * Get recent items by user id with paginate.
     *
     * @param int $user_id
     * @return stdClass
     */
    public function getRecentsByUserIdWithPaginate($user_id)
    {
        $items = \DB::table('items')
            ->select('items.*', 'users.username as user_username', 'users.email as user_email')
            ->join('users', 'items.user_id', '=', 'users.id')
            ->where('items.published', '2')
            ->where('items.user_id', $user_id)
            ->orderBy('items.id', 'desc')
            ->paginate(10);
        $i = 0;
        foreach($items as $item) {
            $object = app('stdClass');
            $object->email = $item->user_email;
            $object->username = $item->user_username;
            $items[$i]->user = $object;
            $i++;
        }
        return $items;

    }

    /**
     * Get recent items by login user id with paginate.
     *
     * @param int $user_id
     * @return stdClass
     */
    public function getRecentsByLoginUserIdWithPaginate($user_id)
    {
        $items = \DB::table('items')
            ->select('items.*', 'users.username as user_username', 'users.email as user_email')
            ->join('users', 'items.user_id', '=', 'users.id')
            ->where('items.user_id', $user_id)
            ->orderBy('items.id', 'desc')
            ->paginate(10);
        $i = 0;
        foreach($items as $item) {
            $object = app('stdClass');
            $object->email = $item->user_email;
            $object->username = $item->user_username;
            $items[$i]->user = $object;
            $i++;
        }
        return $items;
    }

    /**
     * Get recent items by tag id.
     *
     * @param int $item_id
     * @return stdClass
     */
    public function getRecentsByTagId($tag_id)
    {
        return \DB::table('items')
                    ->join('users', 'items.user_id', '=', 'users.id')
                    ->join('tags', 'tags.id', '=', 'item_tag.tag_id')
                    ->join('item_tag', 'items.id', '=', 'item_tag.item_id')
                    ->where('tags.id', $tag_id)
                    ->where('published', '2')
                    ->select('users.email', 'users.username', 'items.open_item_id', 'items.updated_at', 'items.title')
                    ->orderBy('items.id', 'desc')
                    ->paginate(10);
    }

    /**
     * Get like users by item id.
     *
     * @param int $item_id
     * @return Illuminate\Database\Eloquent\Model
     */
    public function getLikeUsersById($item_id)
    {
        $items = \DB::table('items')
                    ->select('items.*', 'users.email as user_email', 'users.username as user_username')
                    ->join('likes', 'items.id', '=', 'likes.item_id')
                    ->join('users', 'likes.user_id', '=', 'users.id')
                    ->where('items.id', $item_id)
                    ->get();
        $i = 0;
        foreach($items as $item) {
            $object = app('stdClass');
            $object->email = $item->user_email;
            $object->username = $item->user_username;
            $items[$i]->user = $object;
            $i++;
        }

        $item_likes = app('stdClass');
        $item_likes->like = $items;

        return $item_likes;
    }

    /**
     * Create a new item.
     *
     * @param $obj user_id, open_item_id, title, body, published
     * @return stdClass
     */
    public function createItem($obj)
    {
        $object = array();
        $object["user_id"] = $obj->user_id;
        $object["open_item_id"] = $obj->open_item_id;
        $object["title"] = $obj->title;
        $object["body"] = $obj->body;
        $object["published"] = $obj->published;
        $item_id = $this->insert($object);

        $ret = $this->getById($item_id);
        return $ret;
    }

    /**
     * Update a item.
     *
     * @param $item_id
     * @param $obj user_id, open_item_id, title, body, published
     * @return stdClass
     */
    public function updateItem($item_id, $obj)
    {
        $object = array();
        $object["user_id"] = $obj->user_id;
        $object["title"] = $obj->title;
        $object["body"] = $obj->body;
        $object["published"] = $obj->published;
        $object["body"] = $obj->body;
        $wkey["id"] = $item_id;
        $ret = $this->update($object, $wkey);

        $item = $this->getById($item_id);
        return $item;
    }

    /**
     * Delete a item.
     *
     * @param $item_id int
     * @return boolean
     */
    public function deleteItem($item_id)
    {
        $object = array();
        $wkey["id"] = $item_id;
        $ret = $this->delete($wkey);
        return $ret;
    }

    /**
     * get item tags array
     *
     * @param object $item
     * @return array
     */
    public function getTagsToArray($item)
    {
        $items = \DB::table('item_tag')
                    ->select('item_tag.tag_id', 'tags.name')
                    ->join('tags', 'item_tag.tag_id', '=', 'tags.id')
                    ->where('item_tag.item_id', $item->id)
                    ->get();
        $array = array();
        $i = 0;
        foreach ($items as $item) {
            $array[$i]["tag_id"] = $item->tag_id;
            $array[$i]["name"] = $item->name;
            $i++;
        }
        return $array;
    }
}
