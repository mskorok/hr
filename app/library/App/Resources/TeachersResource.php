<?php
declare(strict_types=1);

namespace App\Resources;

use App\Controllers\TeachersController;
use App\Model\Teachers;
use App\Transformers\TeacherScheduleTransformer;
use PhalconApi\Constants\PostedDataMethods;
use App\Api\Endpoint as ApiEndpoint;
use PhalconRest\Api\ApiResource;
use App\Constants\AclRoles;

/**
 * Class TeachersResource
 * @package App\Resources
 */
class TeachersResource extends ApiResource
{

    public function initialize()
    {
        $this
            ->name('Teachers')
            ->model(Teachers::class)
            ->expectsJsonData()
            ->transformer(TeacherScheduleTransformer::class)
            ->itemKey('teacher')
            ->collectionKey('teachers')
            ->allow(AclRoles::UNAUTHORIZED)
//            ->deny(AclRoles::UNAUTHORIZED)
            ->handler(TeachersController::class)

            ->endpoint(ApiEndpoint::all())
            ->endpoint(ApiEndpoint::create()->postedDataMethod(PostedDataMethods::POST))
            ->endpoint(ApiEndpoint::find())
            ->endpoint(ApiEndpoint::update()->postedDataMethod(PostedDataMethods::POST))
            ->endpoint(ApiEndpoint::remove())
        ;
    }
}
