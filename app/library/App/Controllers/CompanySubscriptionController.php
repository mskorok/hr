<?php
declare(strict_types=1);

namespace App\Controllers;

/**
 * Class CompanySubscriptionController
 * @package App\Controllers
 */
class CompanySubscriptionController extends ControllerBase
{
    public static $availableIncludes = [
        'Companies',
        'Payments',
        'Subscriptions'
    ];

}
