<?php

class SearchController extends BaseController{

    private $_perPage = 10;

    public function index(){
        $q = Input::get('q');
        $params = array('match' => FtsUtils::createMatchWord($q));

        $offset = $this->calcOffset(Input::get('page'), $this->_perPage);
        $results = ItemFts::match($params, $this->_perPage, $offset);
        if(count($results) > 0){
            $res = ItemFts::matchCount($params);
            $pagination = Paginator::make($results, $res[0]->count, $this->_perPage);
        }
        $users = User::where('username', 'like', "$q%")->get();
        $templates = Template::all();
        $tags = $this->searchTags($q);
        return View::make('search.index', compact('results', 'q', 'templates', 'pagination', 'tags', 'users'));
    }

    public function json(){
        return Response::json(array(
            'list' => $this->jsonResults(Input::get('q')),
            200
        ));
    }

    public function jsonp(){
        return Response::json(array(
            'list' => $this->jsonResults(Input::get('q')),
            200
        ))->setCallback(Input::get('callback'));;
    }


    private function searchTags($q){
        $tagName = mb_strtolower($q);
        $tags = TagFts::match($q);
        foreach($tags as &$tag){
            $tag = (array)$tag;
        }
        return $tags;
    }

    private function jsonResults($q){
        $items = ItemFts::match($q, $this->_perPage);
        
        $json = array();
        foreach($items as $item){
            $json[] = array('title' => $item->title, 'url' => '://'.$_SERVER['HTTP_HOST'].'/items/'.$item->open_item_id);
        }
        return $json;
    }

}
