<?php
declare(strict_types=1);

namespace App\Controllers;

/**
 * Class ScheduleController
 * @package App\Controllers
 */
class ScheduleController extends ControllerBase
{
    public static $availableIncludes = [
        'Teachers'
    ];

    public static $encodedFields = [
        'description'
    ];
}
