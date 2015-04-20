<?php namespace Owl\Http\Controllers;

use Owl\Services\UserService;
use Owl\Repositories\Models\Item;
use Owl\Repositories\Models\ItemHistory;
use Owl\Repositories\Models\Template;
use Owl\Repositories\Models\Tag;
use Owl\Repositories\Models\Stock;
use Owl\Repositories\Models\Like;

class ItemController extends Controller {

    public function create($templateId = null){
        $user = $this->currentUser;
        $user_items = Item::getRecentItemsByUserId($user->id);
        $template = null;
        if(\Input::get('t')) {
            $templateId = \Input::get('t');
            $template = Template::where('id',$templateId)->first();
        }
        return \View::make('items.create', compact('template', 'user_items'));
    }

    public function store(){
        $valid_rule = array(
            'title' => 'required|max:255',
            'tags' => 'alpha_comma|max:64',
            'body' => 'required',
            'published' => 'required|numeric'
        );
        $validator = \Validator::make(\Input::all(), $valid_rule);
        if ($validator->fails()) {
            return \Redirect::back()->withErrors($validator)->withInput();
        }

        $user = $this->currentUser;
        $openItemId = Item::createOpenItemId();
        $item = new Item;
        $item->fill(array(
            'user_id' => $user->id,
            'open_item_id' => $openItemId,
            'title' => \Input::get('title'),
            'body' => \Input::get('body'),
            'published' => \Input::get('published')
        ));
        $item->save();

        $result = ItemHistory::insertHistory($item, $user);

        $tags = \Input::get('tags');
        if (!empty($tags)) {
            $tag_names = explode(",", $tags);
            $tag_ids = Tag::getTagIdsByTagNames($tag_names);
            $item = Item::find($item->id);
            $item->tag()->sync($tag_ids);
        }

        return \Redirect::route('items.show',[$openItemId]);
    }

    public function index(){
        $items = Item::getAllItems();
        $templates = Template::all();
        return \View::make('items.index', compact('items', 'templates'));
    }

    public function show($openItemId){
        $item = Item::with('comment.user')->where('open_item_id',$openItemId)->first();
        if (empty($item)){
            \App::abort(404);
        }

        $user = $this->currentUser;
        if ($item->published === "0"){
            if (empty($user)){
                \App::abort(404);
            } elseif ($item->user_id !== $user->id) {
                \App::abort(404);
            }
        }

        $stock = null;
        $like = null;
        if(!empty($user)){
            $stock = Stock::whereRaw('user_id = ? and item_id = ?', array($user->id, $item->id))->get();
            $like = Like::whereRaw('user_id = ? and item_id = ?', array($user->id, $item->id))->get();
        }
        $stocks = Stock::where('item_id', $item->id)->get();
        $recent_stocks = Stock::getRecentRankingWithCache(5, 7);
        $user_items = Item::getRecentItemsByUserId($item->user_id);
        $like_users = Item::with('like.user')->where('id', $item->id)->first();
        return \View::make('items.show', compact('item', 'user_items', 'stock', 'like', 'like_users', 'stocks', 'recent_stocks'));
    }


    public function edit($openItemId){
        $user = $this->currentUser;
        $item = Item::where('open_item_id',$openItemId)->first();
        if ($item === null){
            \App::abort(404);
        }

        $templates = Template::all();
        $user_items = Item::getRecentItemsByUserId($user->id);
        return \View::make('items.edit', compact('item', 'templates', 'user_items'));
    }

    public function update($openItemId){
        $valid_rule = array(
            'title' => 'required|max:255',
            'tags' => 'alpha_comma|max:64',
            'body' => 'required',
            'published' => 'required|numeric'
        );
        $validator = \Validator::make(\Input::all(), $valid_rule);
        if ($validator->fails()) {
            return \Redirect::back()->withErrors($validator)->withInput();
        }

        $user = $this->currentUser;
        $item = Item::where('open_item_id',$openItemId)->first();
        if ($item == null){
            \App::abort(404);
        }

        $user_id = $user->id;
        if ($item->user_id !== $user->id){
            $user_id = $item->user_id;
        }

        $item->fill(array(
            'user_id' => $user_id,
            'title' => \Input::get('title'),
            'body' => \Input::get('body'),
            'published' => \Input::get('published')
        ));
        $item->save();

        $result = ItemHistory::insertHistory($item, $user);

        $tags = \Input::get('tags');
        if (!empty($tags)) {
            $tag_names = explode(",", $tags);
            $tag_ids = Tag::getTagIdsByTagNames($tag_names);
            $item = Item::find($item->id);
            $item->tag()->sync($tag_ids);
        }

        return \Redirect::route('items.show',[$openItemId]);
    }

    public function destroy($openItemId){
        $user = $this->currentUser;
        $item = Item::where('open_item_id',$openItemId)->first();
        if ($item == null || $item->user_id !== $user->id){
            \App::abort(404);
        }
        Item::where('open_item_id',$openItemId)->delete();
        $no_tag = array();
        $item->tag()->sync($no_tag);

        return \Redirect::route('items.index');
    }

    public function history($openItemId){
        $histories = ItemHistory::with('user')
            ->where('open_item_id', $openItemId)
            ->orderBy('updated_at', 'DESC')
            ->get();

        return \View::make('items.history', compact('histories'));
    }
}
