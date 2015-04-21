<?php namespace Owl\Services;

use Owl\Repositories\Models\Tag;

class TagService extends Service
{
    protected $tagRepo;

    public function __construct(Tag $tagRepo)
    {
        $this->tagRepo = $tagRepo;
    }

    /*
     * タグの全取得（記事数で降順ソート）
     *
     * @param void
     * @return object
     */
    public function getAllTags()
    {
        return $this->tagRepo->getAllTags();
    }
}
