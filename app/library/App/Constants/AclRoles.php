<?php
declare(strict_types=1);

namespace App\Constants;

/**
 * Class AclRoles
 * @package App\Constants
 */
class AclRoles
{
    public const UNAUTHORIZED = 'unauthorized';
    public const AUTHORIZED = 'authorized';
    public const SUPERADMIN = 'superadmin';
    public const ADMIN = 'admin';
    public const COMPANY_ADMIN = 'companyAdmin';
    public const MANAGER = 'manager';
    public const EMPLOYER = 'employer';
    public const APPLICANT = 'applicant';
    public const PARTNER = 'partner';
    public const EXPERT = 'expert';
    public const AUTHOR = 'author';
    public const TEACHER = 'teacher';

    public const ALL_ROLES = [
        self::UNAUTHORIZED,
        self::AUTHORIZED,
        self::SUPERADMIN,
        self::ADMIN,
        self::MANAGER,
        self::EMPLOYER,
        self::APPLICANT,
        self::PARTNER,
        self::EXPERT,
        self::AUTHOR,
        self::COMPANY_ADMIN,
    ];

    public const ALL_AUTHORIZED = [
        self::SUPERADMIN,
        self::ADMIN,
        self::MANAGER,
        self::APPLICANT,
        self::EMPLOYER,
        self::PARTNER,
        self::EXPERT,
        self::AUTHOR,
        self::TEACHER,
        self::COMPANY_ADMIN,
    ];

    public const ADMIN_ROLES = [
        self::SUPERADMIN,
        self::ADMIN
    ];
}
