<?php
declare(strict_types=1);

namespace App\User;

use App\Constants\AclRoles;
use App\Model\Users;

/**
 * Class Service
 * @package App\User
 */
class Service extends \PhalconApi\User\Service
{
    protected $detailsCache = [];

    /**
     * @return null|string
     * @throws \PhalconApi\Exception
     */
    public function getRole()
    {
        /** @var Users $userModel */
        $userModel = $this->getDetails();

        $role = AclRoles::UNAUTHORIZED;

        if ($userModel && \in_array($userModel->getRole(), AclRoles::ALL_ROLES, true)) {
            $role = $userModel->getRole();
        }

        return $role;
    }

    /**
     * @param mixed $identity
     * @return mixed
     */
    protected function getDetailsForIdentity($identity)
    {
        if (array_key_exists($identity, $this->detailsCache)) {
            return $this->detailsCache[$identity];
        }

        $details = Users::findFirst((int)$identity);
        $this->detailsCache[$identity] = $details;

        return $details;
    }

    /**
     * @param $role
     * @return bool
     * @throws \PhalconApi\Exception
     */
    public function hasRole($role) :bool
    {
        return $role === $this->getRole();
    }

    /**
     * @param array $roles
     * @return bool
     * @throws \PhalconApi\Exception
     */
    public function hasRoles(array $roles) :bool
    {
        return \in_array($this->getRole(), $roles, false);
    }
}
