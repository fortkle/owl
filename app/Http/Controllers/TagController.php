<?php namespace Owl\Http\Controllers;

use Owl\Repositories\Models\Tag;
use Owl\Repositories\Models\Item;
use Owl\Repositories\Models\Stock;
use Owl\Services\TagService;
use Owl\Services\UserService;

class TagController extends Controller {

    private $_perPage = 10;

    protected $tagService;
    protected $userService;

    public function __construct(TagService $tagService, UserService $userService)
    {
        $this->tagService = $tagService;
        parent::__construct($userService);
    }

    public function index()
    {
        $tags = $this->tagService->getAllTags();
        $recent_ranking = Stock::getRecentRankingWithCache(5, 7);
        $all_ranking = Stock::getRankingWithCache(5);
        return view('tags.index', compact('tags', 'recent_ranking', 'all_ranking'));
    }

    public function show($tagName){
        $tagName = mb_strtolower($tagName);
        $tag = Tag::where('name', $tagName)->first();

        if (empty($tag)){
            \App::abort(404);
        }

        $items = Item::getRecentItemsByTagId($tag->id);

        return \View::make('tags.show', compact('tag', 'items'));
    }

    public function suggest(){
        $tags = Tag::all();

        $json = array();
        foreach($tags as $tag){
            $json[] = $tag->name;
        }
        return \Response::json($json);
    }
}
