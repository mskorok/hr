<?php
declare(strict_types=1);

namespace App\Resources;

use App\Controllers\CompanySubscriptionController;
use App\Model\CompanySubscription;
use App\Transformers\CompanySubscriptionTransformer;
use PhalconApi\Constants\PostedDataMethods;
use App\Api\Endpoint as ApiEndpoint;
use PhalconRest\Api\ApiResource;
use App\Constants\AclRoles;

/**
 * Class CompanySubscriptionResource
 * @package App\Resources
 */
class CompanySubscriptionResource extends ApiResource
{

    public function initialize()
    {
        $this
            ->name('CompanySubscription')
            ->model(CompanySubscription::class)
            ->expectsJsonData()
            ->transformer(CompanySubscriptionTransformer::class)
            ->itemKey('user_subscription')
            ->collectionKey('user_subscriptions')
            ->allow(AclRoles::UNAUTHORIZED)
//            ->deny(AclRoles::UNAUTHORIZED)
            ->handler(CompanySubscriptionController::class)

            ->endpoint(ApiEndpoint::all())
            ->endpoint(ApiEndpoint::create()->postedDataMethod(PostedDataMethods::POST))
            ->endpoint(ApiEndpoint::find())
            ->endpoint(ApiEndpoint::update()->postedDataMethod(PostedDataMethods::POST))
            ->endpoint(ApiEndpoint::remove())
        ;
    }
}
