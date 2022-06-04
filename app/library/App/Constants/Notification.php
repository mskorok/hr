<?php
declare(strict_types=1);

namespace App\Constants;

/**
 * Class Notification
 * @package App\Constants
 */
class Notification
{
    public const APPLICANT   = 'applicant';
    public const APPLIED     = 'applied';
    public const GENERAL     = 'general';
    public const INVITATIONS = 'invitations';
    public const INVITED     = 'invited';
    public const SUPERADMIN  = 'superadmin';
    public const SUPPORT     = 'support';


    public const APPLICANT_CATEGORIES = [
        self::APPLICANT,
        self::INVITED
    ];

    public const MANAGER_CATEGORIES = [
        self::APPLIED,
        self::INVITATIONS
    ];
}