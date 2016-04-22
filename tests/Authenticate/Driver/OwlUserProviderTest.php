<?php

use Mockery as m;
use Mockery\MockInterface as i;
use Owl\Authenticate\Driver\OwlUserProvider;
use Owl\Authenticate\Driver\OwlUser;
use Owl\Services\UserService;

class OwlUserProviderTest extends \TestCase
{
    /** @var i */
    protected $userService;

    /** @var string */
    protected $className = 'Owl\Authenticate\Driver\OwlUserProvider';

    public function setUp()
    {
        parent::setUp();

        $this->userService = m::mock('Owl\Services\UserService');
        $this->app->bind('Owl\Services\UserService', function () {
            return $this->userService;
        });
    }

    public function testValidInstance()
    {
        $service = $this->app->make($this->className);
        $this->assertInstanceOf(
            $this->className, $service
        );
    }

    public function testShouldReturnOwlUser()
    {
        $this->userService->shouldReceive('getById')->andReturn([]);
        $this->userService->shouldReceive('getByToken')->andReturn(['name' => 'dummy']);
        $owlUserProvider = $this->app->make($this->className);

        $this->assertInstanceOf(
            'Owl\Authenticate\Driver\OwlUser',
            $owlUserProvider->retrieveById(9999)
        );
        $this->assertInstanceOf(
            'Owl\Authenticate\Driver\OwlUser',
            $owlUserProvider->retrieveByToken(9999, 'token')
        );
    }

    public function testShouldReturnNull()
    {
        $this->userService->shouldReceive('getById')->andReturn(null);
        $this->userService->shouldReceive('getByToken')->andReturn(false);
        $owlUserProvider = $this->app->make($this->className);

        $this->assertNull($owlUserProvider->retrieveById(9999));
        $this->assertNull($owlUserProvider->retrieveByToken(9999, 'token'));
    }

    public function testShouldUpdateRememberToken()
    {
        $this->userService->shouldReceive('updateToken')->andReturn(null);
        $owlUser = new OwlUser(['id' => 9999]);
        $owlUserProvider = $this->app->make($this->className);

        $this->assertNull($owlUserProvider->updateRememberToken($owlUser, 'token'));
    }

    public function testRetrieveByCredentialsReturnsNullWithOutPassword()
    {
        $owlUserProvider = $this->app->make($this->className);

        $this->assertNull($owlUserProvider->retrieveByCredentials([]));
    }

    public function testRetrieveByCredentialsReturnsOwlUser()
    {
        $credentials = [
            'password' => 'password',
            'username' => 'username',
        ];
        $this->userService->shouldReceive('getUser')->andReturn('user_data');
        $owlUserProvider = $this->app->make($this->className);

        $owlUser = $owlUserProvider->retrieveByCredentials($credentials);

        $this->assertInstanceOf('Owl\Authenticate\Driver\OwlUser', $owlUser);
        $this->assertEquals(new OwlUser((array) 'user_data'), $owlUser);
    }

    public function testValidateCredentialsReturnTrue()
    {
        $originalPass = 'test_password';
        $password = password_hash($originalPass, PASSWORD_DEFAULT);
        $owlUser = new OwlUser(['password' => $password]);
        $owlUserProvider = $this->app->make($this->className);

        $this->assertTrue($owlUserProvider->validateCredentials($owlUser, ['password' => $originalPass]));
    }

    public function testValidateCredentialsReturnFalse()
    {
        $originalPass = 'test_password';
        $password = password_hash($originalPass, PASSWORD_DEFAULT);
        $owlUser = new OwlUser(['password' => $password]);
        $owlUserProvider = $this->app->make($this->className);

        $this->assertFalse($owlUserProvider->validateCredentials($owlUser, ['password' => 'wrong_password']));
    }
}
