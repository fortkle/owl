<?php namespace Owl\Repositories;

interface ItemRepositoryInterface
{
    /**
     * Get a item by item id.
     *
     * @param int $item_id
     * @return Illuminate\Database\Eloquent\Model
     */
    public function getById($item_id);

    /**
     * Get a item by open item id.
     *
     * @param int $open_item_id
     * @return Illuminate\Database\Eloquent\Model
     */
    public function getByOpenItemId($open_item_id);

    /**
     * Get a item by open item id with comments.
     *
     * @param int $open_item_id
     * @return \stdClass|null
     */
    public function getByOpenItemIdWithComment($open_item_id);

    /**
     * Get all items.
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function getAll();

    /**
     * Get all published items.
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function getAllPublished();

    /**
     * Get recent items by user id.
     *
     * @param int $user_id
     * @return Illuminate\Database\Eloquent\Model
     */
    public function getRecentsByUserId($user_id);

    /**
     * Get recent items by user id with paginate.
     *
     * @param int $user_id
     * @return Illuminate\Database\Eloquent\Model
     */
    public function getRecentsByUserIdWithPaginate($user_id);

    /**
     * Get recent items by login user id with paginate.
     *
     * @param int $user_id
     * @return Illuminate\Database\Eloquent\Model
     */
    public function getRecentsByLoginUserIdWithPaginate($user_id);

    /**
     * Get recent items by tag id.
     *
     * @param int $item_id
     * @return Illuminate\Database\Eloquent\Model
     */
    public function getRecentsByTagId($tag_id);

    /**
     * Get like users by item id.
     *
     * @param int $item_id
     * @return Illuminate\Database\Eloquent\Model
     */
    public function getLikeUsersById($item_id);

    /**
     * Create a new item.
     *
     * @param $obj user_id, open_item_id, title, body, published
     * @return Illuminate\Database\Eloquent\Model
     */
    public function createItem($obj);

    /**
     * Update a item.
     *
     * @param $item_id
     * @param $obj user_id, open_item_id, title, body, published
     * @return Illuminate\Database\Eloquent\Model
     */
    public function updateItem($item_id, $obj);

    /**
     * Delete a item.
     *
     * @param $item_id int
     * @return boolean
     */
    public function deleteItem($item_id);

    /**
     * get item tags array
     *
     * @param object $item
     * @return array
     */
    public function getTagsToArray($item);
}
