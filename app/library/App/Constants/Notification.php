<?php
declare(strict_types=1);

namespace App\Constants;

/**
 * Class Notification
 * @package App\Constants
 */
class Notification
{
    public const ADMIN = 'admin';
    public const APPLICANT   = 'applicant';
    public const MANAGER = 'manager';

    public const CATEGORIES = [
        self::ADMIN,
        self::APPLICANT,
        self::MANAGER
    ];
}