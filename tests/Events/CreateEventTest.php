<?php

use Owl\Events\Item\CreateEvent;

class CreateEventTest extends \TestCase
{
    public function testValidInstance()
    {
        $event = new CreateEvent('itemId', 'userId');
        $this->assertInstanceOf('Owl\Events\Item\CreateEvent', $event);
    }
}
