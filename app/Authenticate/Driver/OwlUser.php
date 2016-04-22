<?php namespace Owl\Authenticate\Driver;

/**
 * @copyright (c) owl
 */

use Illuminate\Auth\GenericUser;
use Owl\Authenticate\Driver\OwlAuthenticatable;

/**
 * Class OwlUser
 *
 * @package Owl\Authenticate\Driver
 */
class OwlUser extends GenericUser implements OwlAuthenticatable
{
    /**
     * Get e-mail address.
     *
     * @return string
     */
    public function email()
    {
        return array_get($this->attributes, 'email', null);
    }

    /**
     * Get user name.
     *
     * @return string
     */
    public function name()
    {
        return array_get($this->attributes, 'username', null);
    }

    /**
     * Get role.
     *
     * @return string|null
     */
    public function role()
    {
        return array_get($this->attributes, 'role', null);
    }
}
