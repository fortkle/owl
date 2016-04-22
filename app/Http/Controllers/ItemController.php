<?php namespace Owl\Http\Controllers;

/**
 * @copyright (c) owl
 */

use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Events\Dispatcher;
use Owl\Services\TagService;
use Owl\Services\ItemService;
use Owl\Services\LikeService;
use Owl\Services\StockService;
use Owl\Services\TemplateService;
use Owl\Http\Requests\ItemStoreRequest;
use Owl\Http\Requests\ItemUpdateRequest;
use Owl\Events\Item\CreateEvent;
use Owl\Events\Item\EditEvent;

/**
 * Class ItemController
 */
class ItemController extends Controller
{
    /** @var TagService */
    protected $tagService;

    /** @var ItemService */
    protected $itemService;

    /** @var LikeService */
    protected $likeService;

    /** @var StockService */
    protected $stockService;

    /** @var TemplateService */
    protected $templateService;

    /**
     * @param TagService       $tagService
     * @param ItemService      $itemService
     * @param LikeService      $likeService
     * @param StockService     $stockService
     * @param TemplateService  $templateService
     */
    public function __construct(
        TagService $tagService,
        ItemService $itemService,
        LikeService $likeService,
        StockService $stockService,
        TemplateService $templateService
    ) {
        $this->tagService = $tagService;
        $this->itemService = $itemService;
        $this->likeService = $likeService;
        $this->stockService = $stockService;
        $this->templateService = $templateService;
    }

    /**
     * @param int|null     $templateId
     * @param AuthManager  $auth
     *
     * @return \Illuminate\View\View
     */
    public function create($templateId = null, AuthManager $auth)
    {
        $user_items = $this->itemService->getRecentsByUserId($auth->user()->getAuthIdentifier());
        $template = null;
        if (\Input::get('t')) {
            $templateId = \Input::get('t');
            $template = $this->templateService->getById($templateId);
        }

        return view('items.create', compact('template', 'user_items'));
    }

    /**
     * @param ItemStoreRequest  $request
     * @param AuthManager       $auth
     * @param Dispatcher        $event
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ItemStoreRequest $request, AuthManager $auth, Dispatcher $event)
    {
        $user = $auth->user();

        $object = (object) array(
            'user_id'      => $user->getAuthIdentifier(),
            'open_item_id' => $this->itemService->createOpenItemId(),
            'title'        => $request->get('title'),
            'body'         => $request->get('body'),
            'published'    => $request->get('published'),
        );
        $item = $this->itemService->create($object);

        // HACK: \Owl\Repositories\Fluent\ItemHistoryRepository@create対応用データ
        $userData = (object) array(
            'id' => $user->getAuthIdentifier(),
        );

        $result = $this->itemService->createHistory($item, $userData);

        $tags = $request->get('tags', []);
        if ($tags) {
            $tag_names = explode(",", $tags);
            $tag_ids = $this->tagService->getTagIdsByTagNames($tag_names);
            $item = $this->itemService->getById($item->id);
            $this->tagService->syncTags($item, $tag_ids);
        }

        // fire CreateEvent
        // TODO: do not create instance in controller method
        $event->fire(new CreateEvent(
            $object->open_item_id,
            (int) $user->getAuthIdentifier()
        ));

        return redirect()->route('items.show', [$item->open_item_id]);
    }

    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $items = $this->itemService->getAllPublished();
        $templates = $this->templateService->getAll();
        return view('items.index', compact('items', 'templates'));
    }

    /**
     * @param int          $openItemId
     * @param AuthManager  $auth
     *
     * @return \Illuminate\View\View
     */
    public function show($openItemId, AuthManager $auth)
    {
        $loginUserId = $auth->user()->getAuthIdentifier();

        $item = $this->itemService->getByOpenItemIdWithComment($openItemId);
        if (empty($item)) {
            abort(404);
        }
        $item_tags = $this->itemService->getTagsToArray($item);

        $user = $auth->user();

        if ($item->published === '0') {
            if (is_null($user)) {
                abort(404);
            } elseif ($item->user_id !== $loginUserId) {
                abort(404);
            }
        }

        $stock = null;
        $like  = null;

        if ($loginUserId) {
            $stock = $this->stockService->getByUserIdAndItemId($loginUserId, $item->id);
            $like = $this->likeService->get($loginUserId, $item->id);
        }

        $stocks        = $this->stockService->getByItemId($item->id);
        $recent_stocks = $this->stockService->getRecentRankingWithCache(5, 7);
        $user_items    = $this->itemService->getRecentsByUserId($item->user_id);
        $like_users    = $this->itemService->getLikeUsersById($item->id);

        return view(
            'items.show',
            compact('item', 'item_tags', 'user_items', 'stock', 'like', 'like_users', 'stocks', 'recent_stocks')
        );
    }

    /**
     * @param int          $openItemId
     * @param AuthManager  $auth
     */
    public function edit($openItemId, AuthManager $auth)
    {
        $loginUserId = $auth->user()->getAuthIdentifier();
        $item        = $this->itemService->getByOpenItemId($openItemId);

        // 記事データが取得できない場合、404
        if ($item === null) {
            abort(404);
        }

        $item_tags  = $this->itemService->getTagsToArray($item);
        $templates  = $this->templateService->getAll();
        $user_items = $this->itemService->getRecentsByUserId($loginUserId);

        return view('items.edit', compact('item', 'item_tags', 'templates', 'user_items'));
    }

    /**
     * @param ItemUpdateRequest  $request
     * @param string             $openItemId
     * @param AuthManager        $auth
     * @param Dispatcher         $event
     *
     * @return mixed
     */
    public function update(ItemUpdateRequest $request, $openItemId, AuthManager $auth, Dispatcher $event)
    {
        $loginUserId = $auth->user()->getAuthIdentifier();
        $item = $this->itemService->getByOpenItemId($openItemId);

        if ($item->updated_at != $request->get('updated_at')) {
            return redirect()->back()->with(
                "updated_at", "コンフリクトの可能性があるため更新できませんでした。"
            )->withInput();
        }

        if ($item == null) {
            abort(404);
        }

        $user_id = $loginUserId;
        if ($item->user_id !== $user_id) {
            $user_id = $item->user_id;
        }

        $object = (object) array(
            'user_id'   => $user_id,
            'title'     => $request->get('title'),
            'body'      => $request->get('body'),
            'published' => $request->get('published'),
        );
        $item = $this->itemService->update($item->id, $object);

        // HACK: \Owl\Repositories\Fluent\ItemHistoryRepository@create対応用データ
        $userData = (object) array('id' => $loginUserId);
        $result   = $this->itemService->createHistory($item, $userData);

        $tags = $request->get('tags', []);
        if ($tags) {
            $tag_names = explode(",", $tags);
            $tag_ids   = $this->tagService->getTagIdsByTagNames($tag_names);
            $item      = $this->itemService->getById($item->id);
            $this->tagService->syncTags($item, $tag_ids);
        }

        // fire EditEvent
        // TODO: do not create instance in controller method
        $event->fire(new EditEvent($openItemId, (int) $loginUserId));

        return redirect()->route('items.show', [$openItemId]);
    }

    /**
     * @param int          $openItemId
     * @param AuthManager  $auth
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($openItemId, AuthManager $auth)
    {
        $loginUserId = $auth->user()->getAuthIdentifier();
        $item = $this->itemService->getByOpenItemId($openItemId);
        if ($item == null || $item->user_id !== $loginUserId) {
            abort(404);
        }

        $this->itemService->delete($item->id);
        $no_tag = array();
        $this->tagService->syncTags($item, $no_tag);

        return redirect()->route('items.index');
    }

    /**
     * @param int  $openItemId
     *
     * @return \Illuminate\View\View
     */
    public function history($openItemId)
    {
        $histories = $this->itemService->getHistoryByOpenItemId($openItemId);
        return view('items.history', compact('histories'));
    }

    /**
     * POSTされたMarkdownをレンダリングし、json形式でreturn
     *
     * @return \Illuminate\Http\Response
     */
    public function parse()
    {
        $parsedMd = '';
        if (\Input::get('md')) {
            $parsedMd= \HTML::markdown(\Input::get('md'));
        }
        return response()->json(['html' => $parsedMd]);
    }
}
