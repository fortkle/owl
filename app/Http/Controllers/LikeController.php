<?php namespace Owl\Http\Controllers;

use Owl\Repositories\Models\Like;
use Owl\Repositories\Models\Item;

class LikeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return \Redirect::to('/'); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $user = $this->currentUser;

        $openItemId = \Input::get('open_item_id');
        $item = Item::where('open_item_id',$openItemId)->first();

        Like::firstOrCreate(array('user_id'=> $user->id, 'item_id' => $item->id));

        return \Response::json();
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($openItemId)
    {
        $user = $this->currentUser;
        $item = Item::where('open_item_id',$openItemId)->first();

        Like::whereRaw('user_id = ? and item_id = ?', array($user->id, $item->id))->delete();
    }
}
