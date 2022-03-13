<?php
declare(strict_types=1);

namespace App\Controllers;

/**
 * Class CandidatesController
 * @package App\Controllers
 */
class CandidatesController extends ControllerBase
{
    public static $availableIncludes = [
        'Companies'
    ];

    public static $encodedFields = [
        'professional_area',
        'work_place',
        'key_skills',
        'language',
        'location',
        'certification',
        ''
    ];
}
