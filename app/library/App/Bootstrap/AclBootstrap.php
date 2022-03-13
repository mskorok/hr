<?php
declare(strict_types=1);

namespace App\Bootstrap;

use App\BootstrapInterface;
use App\Constants\Services;
use Phalcon\Acl;
use Phalcon\Config;
use Phalcon\DiInterface;
use PhalconRest\Api;
use App\Constants\AclRoles;

/**
 * Class AclBootstrap
 * @package App\Bootstrap
 */
class AclBootstrap implements BootstrapInterface
{
    /**
     * @param Api $api
     * @param DiInterface $di
     * @param Config $config
     */
    public function run(Api $api, DiInterface $di, Config $config): void
    {
        /** @var \PhalconApi\Acl\MountingEnabledAdapterInterface $acl */
        $acl = $di->get(Services::ACL);

        $unauthorizedRole = new Acl\Role(AclRoles::UNAUTHORIZED);
        $authorizedRole = new Acl\Role(AclRoles::AUTHORIZED);

        $acl->addRole($unauthorizedRole);
        $acl->addRole($authorizedRole);

        $acl->addRole(new Acl\Role(AclRoles::SUPERADMIN), $authorizedRole);
        $acl->addRole(new Acl\Role(AclRoles::ADMIN), $authorizedRole);
        $acl->addRole(new Acl\Role(AclRoles::MANAGER), $authorizedRole);
        $acl->addRole(new Acl\Role(AclRoles::EMPLOYER), $authorizedRole);
        $acl->addRole(new Acl\Role(AclRoles::APPLICANT), $authorizedRole);
        $acl->addRole(new Acl\Role(AclRoles::PARTNER), $authorizedRole);

        $acl->mountMany($api->getCollections());
    }
}