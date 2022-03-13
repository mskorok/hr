<?php
declare(strict_types=1);

namespace App\Auth;

use App\Constants\Services;
use App\Model\Users;
use Phalcon\Di;
use PhalconApi\Auth\AccountType;

/**
 * Class UsernameAccountType
 * @package App\Auth
 */
class UsernameAccountType implements AccountType
{
    public const NAME = 'username';

    /**
     * @param array $data
     * @return null|string
     */
    public function login($data): ?string
    {
        /** @var \Phalcon\Security $security */
        $security = Di::getDefault()->get(Services::SECURITY);

        $username = $data[Manager::LOGIN_DATA_USERNAME];
        $password = $data[Manager::LOGIN_DATA_PASSWORD];

        /** @var Users $user */
        $user = Users::findFirst([
            'conditions' => 'username = :username:',
            'bind' => ['username' => $username]
        ]);

        if (!$user) {
            return null;
        }

        if (!$security->checkHash($password, $user->getPassword())) {
            return null;
        }

        return (string)$user->getId();
    }

    /**
     * @param string $identity
     * @return bool
     */
    public function authenticate($identity): bool
    {
        return Users::count([
            'conditions' => 'id = :id:',
            'bind' => ['id' => (int)$identity]
        ]) > 0;
    }
}
