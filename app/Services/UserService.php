<?php namespace Owl\Services;

/**
 * @copyright (c) owl
 */

use Owl\Services\UserRoleService;
use Owl\Repositories\UserRepositoryInterface;
use Owl\Repositories\LoginTokenRepositoryInterface;
use Owl\Repositories\UserMailNotificationRepositoryInterface;
use Carbon\Carbon;

/**
 * Class UserService
 *
 * @package Owl\Services
 */
class UserService extends Service
{
    /** @var UserRepositoryInterface */
    protected $userRepo;

    /** @var LoginTokenRepositoryInterface */
    protected $loginTokenRepo;

    /** @var UserMailNotificationRepositoryInterface */
    protected $mailNotifyRepo;

    /**
     * @param UserRepositoryInterface                  $userRepo
     * @param LoginTokenRepositoryInterface            $loginTokenRepo
     * @param UserMailNotificationRepositoryInterface  $mailNotifyRepo
     */
    public function __construct(
        UserRepositoryInterface $userRepo,
        LoginTokenRepositoryInterface $loginTokenRepo,
        UserMailNotificationRepositoryInterface $mailNotifyRepo
    ) {
        $this->userRepo       = $userRepo;
        $this->loginTokenRepo = $loginTokenRepo;
        $this->mailNotifyRepo = $mailNotifyRepo;
    }

    /**
     * @return bool
     */
    public function isOwner()
    {
        if (\Session::has('User')) {
            $user = \Session::get('User');
            if ($user->role === UserRoleService::ROLE_ID_OWNER) {
                return true;
            }
        }
        return false;
    }

    /**
     * Create a new user.
     *
     * @param mixed  $credentials (email, username, password)
     * @return \stdclass
     */
    public function create(array $credentials = [])
    {
        $object = app('stdClass');
        $object->username = $credentials['username'];
        $object->email = $credentials['email'];
        $object->password = $credentials['password'];
        $object->role = UserRoleService::ROLE_ID_MEMBER;
        $user = $this->userRepo->create($object);

        // user_mail_notifictionsテーブルにレコード挿入
        $this->mailNotifyRepo->insert([
            'user_id'                    => $user->id,
            'comment_notification_flag'  => 0,
            'favorite_notification_flag' => 0,
            'like_notification_flag'     => 0,
            'edit_notification_flag'     => 0,
        ]);

        return $user;
    }

    /**
     * Update a user information(username, email).
     *
     * @param int     $id
     * @param string  $username
     * @param string  $email
     * @param int     $role$
     * @return \stdclass
     */
    public function update($id, $username, $email, $role)
    {
        return $this->userRepo->updateUser($id, $username, $email, $role);
    }

    /**
     * Get a user by specified keys.
     *
     * @param array  $wkey
     * @return \stdClass | null
     */
    public function getUser(array $wkey)
    {
        return $this->userRepo->find($wkey);
    }

    /**
     * Get a user by user id.
     *
     * @param int  $id
     * @return \stdclass
     */
    public function getById($id)
    {
        return $this->userRepo->getById($id);
    }

    /**
     * Get a user by username.
     *
     * @param string  $username
     * @return \stdclass
     */
    public function getByUsername($username)
    {
        return $this->userRepo->getByUsername($username);
    }

    /**
     * Get a user by email.
     *
     * @param string  $email
     * @return \stdclass
     */
    public function getByEmail($email)
    {
        return $this->userRepo->getByEmail($email);
    }

    /**
     * Get users by username like search.
     *
     * @param string  $username
     * @return \stdclass
     */
    public function getLikeUsername($username)
    {
        return $this->userRepo->getLikeUsername($username);
    }

    /**
     * Get a user by login token.
     *
     * @param string  $token
     * @return \stdclass | bool
     */
    public function getByToken($token)
    {
        $TWO_WEEKS = 14;
        $limit = Carbon::now()->subDays($TWO_WEEKS);

        $tokenResult = $this->loginTokenRepo->getValidLoginToken($token, $limit);
        if (isset($tokenResult)) {
            $user = $this->getById($tokenResult->user_id);
            return $user;
        } else {
            return false;
        }
    }

    /**
     * Update the "remember me" token by user ID.
     *
     * @param int     $userId
     * @param string  $token
     *
     * @return bool
     */
    public function updateToken($userId, $token)
    {
        $params = compact('token');
        $wkey   = ['user_id' => $userId];

        return (bool) $this->loginTokenRepo->update($params, $wkey);
    }

    /**
     * @return array
     */
    public function getOwners()
    {
        return $this->userRepo->getOwners();
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->userRepo->getAll();
    }

    /**
     * get users array
     *
     * @param object  $user
     * @return array
     */
    public function getUsersToArray($users)
    {
        return $this->userRepo->getUsersToArray($users);
    }
}
