<?php
declare(strict_types=1);

namespace App\Resources;

use App\Controllers\ScheduleController;
use App\Model\Schedule;
use App\Transformers\ScheduleTransformer;
use PhalconApi\Constants\PostedDataMethods;
use App\Api\Endpoint as ApiEndpoint;
use PhalconRest\Api\ApiResource;
use App\Constants\AclRoles;

/**
 * Class ScheduleResource
 * @package App\Resources
 */
class ScheduleResource extends ApiResource
{

    public function initialize()
    {
        $this
            ->name('Schedule')
            ->model(Schedule::class)
            ->expectsJsonData()
            ->transformer(ScheduleTransformer::class)
            ->itemKey('schedule')
            ->collectionKey('schedules')
            ->allow(AclRoles::UNAUTHORIZED)
//            ->deny(AclRoles::UNAUTHORIZED)
            ->handler(ScheduleController::class)

            ->endpoint(ApiEndpoint::all())
            ->endpoint(ApiEndpoint::create()->postedDataMethod(PostedDataMethods::POST))
            ->endpoint(ApiEndpoint::find())
            ->endpoint(ApiEndpoint::update()->postedDataMethod(PostedDataMethods::POST))
            ->endpoint(ApiEndpoint::remove())
        ;
    }
}
