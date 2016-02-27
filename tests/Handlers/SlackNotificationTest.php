<?php

use Mockery as m;
use MockeryInterface as i;
use Owl\Handlers\Events\SlackNotification;
use Owl\Events\Item\CreateEvent;
use Owl\Events\Item\EditEvent;
use Owl\Libraries\SlackUtils;

class SlackNotificationTest extends \TestCase
{
    /** @var string */
    protected $handlerName = 'Owl\Handlers\Events\SlackNotification';

    public function setUp()
    {
        parent::setUp();

        // mock data
        $this->dummyItem = (object) [
            'open_item_id' => 'open_item_id',
            'title'        => 'title',
            'user_id'      => 'user_id',
        ];
        $this->dummyUser = (object) [
            'id'       => 'sender_id',
            'username' => 'sender',
        ];
        // mock class
        $this->itemRepo     = m::mock($this->itemRepoName);
        $this->userRepo     = m::mock($this->userRepoName);
    }

    public function testValidInstance()
    {
        $handler = $this->app->make($this->handlerName);
        $this->assertInstanceOf($this->handlerName, $handler);
    }

    public function testShouldItemCreateNotify()
    {
        $this->itemRepo->shouldReceive('getByOpenItemId')->andReturn($this->dummyItem);
        $this->userRepo->shouldReceive('getById')->andReturn($this->dummyUser);

        $slackUtilMock = m::mock('Owl\Libraries\SlackUtils');
        $slackUtilMock->shouldReceive('postCreateMessage')->andReturn(true);
        $this->app->bind('Owl\Libraries\SlackUtils', function ($app) use ($slackUtilMock) {
            return $slackUtilMock;
        });
        $this->bindReposHelper($this->itemRepo, $this->userRepo);
        $handler = $this->app->make($this->handlerName);

        $createEvent = new CreateEvent('itemId', 'userId');
        $handler->onItemCreated($createEvent);
    }

    public function testShouldItemEditNotify()
    {
        $this->itemRepo->shouldReceive('getByOpenItemId')->andReturn($this->dummyItem);
        $this->userRepo->shouldReceive('getById')->andReturn($this->dummyUser);

        $slackUtilMock = m::mock('Owl\Libraries\SlackUtils');
        $slackUtilMock->shouldReceive('postEditMessage')->andReturn(true);
        $this->app->bind('Owl\Libraries\SlackUtils', function ($app) use ($slackUtilMock) {
            return $slackUtilMock;
        });
        $this->bindReposHelper($this->itemRepo, $this->userRepo);
        $handler = $this->app->make($this->handlerName);

        $editEvent = new EditEvent('itemId', 'userId');
        $handler->onItemEdited($editEvent);
    }

    protected function bindReposHelper($itemRepo, $userRepo)
    {
        $this->app->bind($this->itemRepoName, function ($app) use ($itemRepo) {
            return $itemRepo;
        });
        $this->app->bind($this->userRepoName, function ($app) use ($userRepo) {
            return $userRepo;
        });
    }
}
