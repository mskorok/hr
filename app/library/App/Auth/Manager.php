<?php
declare(strict_types=1);

namespace App\Auth;

use PhalconApi\Auth\Session;

/**
 * Class Manager
 * @package App\Auth
 */
class Manager extends \PhalconApi\Auth\Manager
{
    public const LOGIN_DATA_EMAIL = 'email';

    /**
     * @param string $accountTypeName
     * @param string $email
     * @param string $password
     *
     * @return Session Created session
     * @throws \PhalconApi\Exception
     *
     *
     * Helper to login with email & password
     */
    public function loginWithEmailPassword($accountTypeName, $email, $password): Session
    {
        return $this->login($accountTypeName, [

            self::LOGIN_DATA_EMAIL => $email,
            self::LOGIN_DATA_PASSWORD => $password
        ]);
    }
}