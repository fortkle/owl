<?php

class StockController extends BaseController {

    private $_perPage = 10;
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$user = Sentry::getUser();
		$stocks = Stock::getStockList($user->id);
		$templates = Template::all();
		return View::make('stocks.index', compact('stocks', 'templates'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$user = Sentry::getUser();

		$openItemId = Input::get('open_item_id');
        $item = Item::where('open_item_id',$openItemId)->first();

        Stock::firstOrCreate(array('user_id'=> $user->id, 'item_id' => $item->id));

		return Response::json();
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($openItemId)
	{
		$user = Sentry::getUser();
        $item = Item::where('open_item_id',$openItemId)->first();

		Stock::whereRaw('user_id = ? and item_id = ?', array($user->id, $item->id))->delete();
        }

    /**
     * Search Stock 
     *
     * @param  int  $id
     * @return Response
     */
    public function search()
    {
        $q = Input::get('q');
        $user = Sentry::getUser();
        $params = array('user_id' => $user->id, 'match' => FtsUtils::createMatchWord($q));

        $offset = $this->calcOffset(Input::get('page'), $this->_perPage);
        $stocks = ItemFts::match($params, $this->_perPage, $offset );
        if(count($stocks) > 0){
            $res = ItemFts::matchCount($params);
            $pagination = Paginator::make($stocks, $res[0]->count, $this->_perPage);
        }
		$templates = Template::all();
		return View::make('stocks.search', compact('stocks', 'templates', 'q', 'pagination'));
    }
}
