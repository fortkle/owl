<?php namespace Owl\Handlers\Events;

/**
 * @copyright (c) owl
 */

use Owl\Events\Item\CreateEvent;
use Owl\Events\Item\EditEvent;
use Owl\Services\ItemService;
use Owl\Services\UserService;
use Owl\Libraries\SlackUtils;

/**
 * Class SlackNotification
 * Slack通知関連のイベントハンドラークラス
 *
 * @package Owl\Handlers\Events
 */
class SlackNotification
{
    /** @var ItemService */
    protected $itemService;

    /** @var UserService */
    protected $userService;

    /** @var SlackUtils */
    protected $slackUtils;

    /**
     * @param ItemService $itemService
     * @param UserService $userService
     * @param SlackUtils  $slackUtils
     */
    public function __construct(
        ItemService $itemService,
        UserService $userService,
        SlackUtils $slackUtils
    ) {
        $this->itemService = $itemService;
        $this->userService = $userService;
        $this->slackUtils  = $slackUtils;
    }

    /**
     * 各イベントにハンドラーメソッドを登録
     *
     * @param \Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen('Owl\Events\Item\CreateEvent', '\Owl\Handlers\Events\SlackNotification@onItemCreated');
        $events->listen('Owl\Events\Item\EditEvent', '\Owl\Handlers\Events\SlackNotification@onItemEdited');
    }

    /**
     * 記事が新規作成された時
     *
     * @param CreateEvent $event
     */
    public function onItemCreated(CreateEvent $event)
    {
        $item = $this->itemService->getByOpenItemId($event->getId());
        $user = $this->userService->getById($event->getUserId());
        $this->slackUtils->postCreateMessage($item, $user);
    }

    /**
     * 記事が編集された時
     *
     * @param EditEvent $event
     */
    public function onItemEdited(EditEvent $event)
    {
        $item = $this->itemService->getByOpenItemId($event->getId());
        $user = $this->userService->getById($event->getUserId());
        $this->slackUtils->postEditMessage($item, $user);
    }
}
