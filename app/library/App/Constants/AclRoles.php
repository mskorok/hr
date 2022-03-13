<?php
declare(strict_types=1);

namespace App\Constants;

/**
 * Class AclRoles
 * @package App\Constants
 */
class AclRoles
{
    public const UNAUTHORIZED = 'Unauthorized';
    public const AUTHORIZED = 'Authorized';
    public const SUPERADMIN = 'Superadmin';
    public const ADMIN = 'Admin';
    public const MANAGER = 'Manager';
    public const EMPLOYER = 'Employer';
    public const APPLICANT = 'Applicant';
    public const PARTNER = 'Partner';
    public const EXPERT = 'Expert';
    public const AUTHOR = 'Author';

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
        self::AUTHOR
    ];

    public const ALL_AUTHORIZED = [
        self::SUPERADMIN,
        self::ADMIN,
        self::MANAGER,
        self::APPLICANT,
        self::EMPLOYER,
        self::PARTNER,
        self::EXPERT,
        self::AUTHOR
    ];

    public const ADMIN_ROLES = [
        self::SUPERADMIN,
        self::ADMIN
    ];
}
