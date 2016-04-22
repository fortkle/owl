<?php namespace Owl\Http\Controllers;

/**
 * @copyright (c) owl
 */

use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Events\Dispatcher;
use Owl\Services\ItemService;
use Owl\Services\StockService;
use Owl\Services\TemplateService;
use Owl\Events\Item\FavoriteEvent;

/**
 * Class StockController
 */
class StockController extends Controller
{
    /** @var ItemService */
    protected $itemService;

    /** @var StockService */
    protected $stockService;

    /** @var TemplateService */
    protected $templateService;

    /**
     * @param ItemService      $itemService
     * @param StockService     $stockService
     * @param TemplateService  $templateService
     */
    public function __construct(
        ItemService $itemService,
        StockService $stockService,
        TemplateService $templateService
    ) {
        $this->itemService = $itemService;
        $this->stockService = $stockService;
        $this->templateService = $templateService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param AuthManager  $auth
     *
     * @return \Illuminate\View\View
     */
    public function index(AuthManager $auth)
    {
        $stocks = $this->stockService->getStockList($auth->user()->getAuthIdentifier());
        $templates = $this->templateService->getAll();

        return view('stocks.index', compact('stocks', 'templates'));
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

        $this->stockService->firstOrCreate($loginUserId, $item->id);

        // fire FavoriteEvent
        // TODO: do not generate instance in controller method
        $event->fire(new FavoriteEvent($openItemId, (int) $loginUserId));

        return response()->json();
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

        $this->stockService->delete($auth->user()->getAuthIdentifier, $item->id);
    }
}
