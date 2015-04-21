<?php namespace Owl\Http\Controllers;

use Owl\Repositories\Models\Item;
use Owl\Repositories\Models\Template;
use Owl\Repositories\Models\Stock;

class IndexController extends Controller
{
    public function index()
    {
        $items = Item::getAllItems();
        $templates = Template::all();
        $ranking_stock = Stock::getRankingWithCache(5);
        return \View::make('index.index', compact('items', 'templates', 'ranking_stock'));
    }
}
