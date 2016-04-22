<?php

use Mockery as m;
use Mockery\MockInterface as i;
use Owl\Authenticate\Driver\OwlUser;

class OwlUserTest extends \TestCase
{
    public function testGetColumns()
    {
        $expected = array(
            'email'    => 'test@test.com',
            'username' => 'username',
            'role'     => 'admin',
        );
        $owlUser = new OwlUser($expected);

        $this->assertEquals($owlUser->email(), $expected['email']);
        $this->assertEquals($owlUser->name(), $expected['username']);
        $this->assertEquals($owlUser->role(), $expected['role']);
    }

    public function testGetNullColumns()
    {
        $owlUser = new OwlUser([]);

        $this->assertNull($owlUser->email());
        $this->assertNull($owlUser->name());
        $this->assertNull($owlUser->role());
    }
}
