<?php namespace Owl\Http\Controllers;

/**
 * @copyright (c) owl
 */

use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Events\Dispatcher;
use Owl\Services\ItemService;
use Owl\Services\CommentService;
use Owl\Events\Item\CommentEvent;

/**
 * Class CommentController
 */
class CommentController extends Controller
{
    /** @var ItemService */
    protected $itemService;

    /** @var CommentService */
    protected $commentService;

    /** @var int */
    private $status = 400;

    /**
     * @param ItemService     $itemService
     * @param CommentService  $commentService
     */
    public function __construct(
        ItemService $itemService,
        CommentService $commentService
    ) {
        $this->itemService = $itemService;
        $this->commentService = $commentService;
    }

    /**
     * @param AuthManager  $auth
     * @param Dispatcher   $event
     *
     * @return \Illuminate\View\View | string
     */
    public function create(Dispatcher $event, AuthManager $auth)
    {
        $item = $this->itemService->getByOpenItemId(\Input::get('open_item_id'));
        $user = $auth->user();
        if (preg_match("/^[\s　\t\r\n]*$/s", \Input::get('body') || !$user || !$item)) {
            return '';
        }

        $object = (object) array(
            'item_id'  => $item->id,
            'user_id'  => $user->getAuthIdentifier(),
            'body'     => \Input::get('body'),
            'username' => $user->name(),
            'email'    => $user->email(),
        );
        $comment = $this->commentService->createComment($object);

        // fire event
        // TODO: do not create instance in controller method
        $event->fire(new CommentEvent(
            $item->open_item_id,
            (int) $user->getAuthIdentifier(),
            \Input::get('body')
        ));

        return view('comment.body', compact('comment'));
    }

    /**
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function update()
    {
        if (!$comment = $this->commentService->getCommentById(\Input::get('id'))) {
            return  \Response::make("", $this->status);
        }
        $comment = $this->commentService->updateComment($comment->id, \Input::get('body'));

        $needContainerDiv = false; //remove outer div for update js div replace
        return \View::make('comment.body', compact('comment', 'needContainerDiv'));

    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        if ($comment = $this->commentService->getCommentById(\Input::get('id'))) {
            $this->commentService->deleteComment($comment->id);
            $this->status = 200;
        }
        return  \Response::make("", $this->status);
    }
}
