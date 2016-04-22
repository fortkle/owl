<?php namespace Owl\Authenticate\Driver;

/**
 * @copyright (c) owl
 */

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * interface OwlAuthenticatable
 */
interface OwlAuthenticatable extends Authenticatable
{
    /**
     * Get e-mail address.
     *
     * @return string
     */
    public function email();

    /**
     * Get user name.
     *
     * @return string
     */
    public function name();

    /**
     * Get role.
     *
     * @return string
     */
    public function role();
}
