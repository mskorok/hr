<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 29.11.17
 * Time: 15:36
 */

namespace App\Traits;

use App\Constants\AclRoles;
use App\Model\Users;
use App\User\Service;
use Phalcon\Mvc\Model\Query\Builder;
use PhalconApi\Exception;

/**
 * Trait SearchByRoles
 * @package App\Traits
 */
trait SearchByRoles
{
    /**
     * @param Builder $query
     * @return Builder
     * @throws Exception
     * @throws \RuntimeException
     */
    protected function _getEmployerQuery(Builder $query): Builder
    {
        /** @var Service $service */
        $service =  $this->userService;
        /** @var Users $user */
        try {
            $user = $service->getDetails();
        } catch (Exception $exception) {
            throw new \RuntimeException($exception->getMessage());
        }
        if (!$user) {
            throw new \RuntimeException('Not Authorized!');
        }

        $query->andWhere('[' . Users::class . '].[role]  = :role:', ['role' => AclRoles::ADMIN]);
        $query->orWhere('[' . Users::class . '].[role1]  = :role1:', ['role1' => AclRoles::SUPERADMIN]);
        $query->orWhere('[' . Users::class . '].[role1]  = :role1:', ['role1' => AclRoles::APPLICANT]);
        $role = $service->getRole();
        $query->columns('DISTINCT [' . Users::class . '].*');
        if (!\in_array($role, [AclRoles::SUPERADMIN, AclRoles::ADMIN, AclRoles::MANAGER, AclRoles::EMPLOYER], true)) {
            throw new \RuntimeException('User is not allowed');
        }
        return $query;
    }

    /**
     * @param Builder $query
     * @return Builder
     * @throws Exception
     * @throws \RuntimeException
     */
    protected function _getApplicantQuery(Builder $query): Builder
    {
        /** @var Service $service */
        $service =  $this->userService;
        /** @var Users $user */
        try {
            $user = $service->getDetails();
        } catch (Exception $exception) {
            throw new \RuntimeException($exception->getMessage());
        }
        if (!$user) {
            throw new \RuntimeException('Not Authorized!');
        }

        $query->andWhere('[' . Users::class . '].[role]  = :role:', ['role' => AclRoles::ADMIN]);
        $query->orWhere('[' . Users::class . '].[role1]  = :role1:', ['role1' => AclRoles::SUPERADMIN]);
        $query->orWhere('[' . Users::class . '].[role1]  = :role1:', ['role1' => AclRoles::MANAGER]);
        $query->orWhere('[' . Users::class . '].[role1]  = :role1:', ['role1' => AclRoles::EMPLOYER]);
        $role = $service->getRole();
        $query->columns('DISTINCT [' . Users::class . '].*');
        if (!\in_array($role, [AclRoles::SUPERADMIN, AclRoles::ADMIN, AclRoles::APPLICANT], true)) {
            throw new \RuntimeException('User is not allowed');
        }
        return $query;
    }
}
