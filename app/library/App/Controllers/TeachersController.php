<?php
declare(strict_types=1);

namespace App\Controllers;

/**
 * Class TeachersController
 * @package App\Controllers
 */
class TeachersController extends ControllerBase
{
    public static $availableIncludes = [
        'Schedule',
        'Users'
    ];

    public static $encodedFields = [
        'text',
        'title',
        'description',
        'skills'
    ];

}
