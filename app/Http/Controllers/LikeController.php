<?php namespace Owl\Http\Controllers;

/**
 * @copyright (c) owl
 */

use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Events\Dispatcher;
use Owl\Services\ItemService;
use Owl\Services\LikeService;
use Owl\Events\Item\LikeEvent;

/**
 * Class LikeController
 */
class LikeController extends Controller
{
    /** @var ItemService */
    protected $itemService;

    /** @var LikeService */
    protected $likeService;

    /**
     * @param ItemService  $itemService
     * @param LikeService  $likeService
     */
    public function __construct(
        ItemService $itemService,
        LikeService $likeService
    ) {
        $this->itemService = $itemService;
        $this->likeService = $likeService;
    }

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
     * @param AuthManager  $auth
     * @param Dispatcher   $event
     *
     * @return \Illuminate\Http\Response
     */
    public function store(AuthManager $auth, Dispatcher $event)
    {
        $loginUserId = $auth->user()->getAuthIdentifier();

        $openItemId = \Input::get('open_item_id');
        $item = $this->itemService->getByOpenItemId($openItemId);

        $this->likeService->firstOrCreate($loginUserId, $item->id);

        // fire Like Event
        // TODO: do not generate instance in controller method
        $event->fire(new LikeEvent($openItemId, (int) $loginUserId));

        return \Response::json();
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param int          $openItemId
     * @param AuthManager  $auth
     */
    public function destroy($openItemId, AuthManager $auth)
    {
        $item = $this->itemService->getByOpenItemId($openItemId);

        $this->likeService->delete($auth->user()->getAuthIdentifier(), $item->id);
    }
}
