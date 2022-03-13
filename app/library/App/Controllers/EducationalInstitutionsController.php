<?php
declare(strict_types=1);

namespace App\Controllers;


/**
 * Class EducationalInstitutionsController
 * @package App\Controllers
 */
class EducationalInstitutionsController extends BaseApiController
{
    /**
     * @var array
     */
    public static $availableIncludes = [
        'Countries',
        'EducationInstitutionLevel',
        'EducationLevel'
    ];
}