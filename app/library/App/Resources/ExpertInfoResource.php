<?php
declare(strict_types=1);

namespace App\Resources;

use App\Controllers\ExpertInfoController;
use App\Model\ExpertInfo;
use App\Transformers\ExpertInfoTransformer;
use PhalconApi\Constants\PostedDataMethods;
use App\Api\Endpoint as ApiEndpoint;
use PhalconRest\Api\ApiResource;
use App\Constants\AclRoles;

/**
 * Class ExpertInfoResource
 * @package App\Resources
 */
class ExpertInfoResource extends ApiResource
{

    public function initialize()
    {
        $this
            ->name('ExpertInfo')
            ->model(ExpertInfo::class)
            ->expectsJsonData()
            ->transformer(ExpertInfoTransformer::class)
            ->itemKey('expert_info')
            ->collectionKey('experts_info')
            ->allow(AclRoles::UNAUTHORIZED)
//            ->deny(AclRoles::UNAUTHORIZED)
            ->handler(ExpertInfoController::class)

            ->endpoint(ApiEndpoint::all())
            ->endpoint(ApiEndpoint::create()->postedDataMethod(PostedDataMethods::POST))
            ->endpoint(ApiEndpoint::find())
            ->endpoint(ApiEndpoint::update()->postedDataMethod(PostedDataMethods::POST))
            ->endpoint(ApiEndpoint::remove())
        ;
    }
}
