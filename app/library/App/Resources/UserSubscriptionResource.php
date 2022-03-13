<?php
declare(strict_types=1);

namespace App\Resources;

use App\Controllers\UserSubscriptionController;
use App\Model\UserSubscription;
use App\Transformers\UserSubscriptionTransformer;
use PhalconApi\Constants\PostedDataMethods;
use App\Api\Endpoint as ApiEndpoint;
use PhalconRest\Api\ApiResource;
use App\Constants\AclRoles;

/**
 * Class UserSubscriptionResource
 * @package App\Resources
 */
class UserSubscriptionResource extends ApiResource
{

    public function initialize()
    {
        $this
            ->name('UserSubscription')
            ->model(UserSubscription::class)
            ->expectsJsonData()
            ->transformer(UserSubscriptionTransformer::class)
            ->itemKey('user_subscription')
            ->collectionKey('user_subscriptions')
            ->allow(AclRoles::UNAUTHORIZED)
//            ->deny(AclRoles::UNAUTHORIZED)
            ->handler(UserSubscriptionController::class)

            ->endpoint(ApiEndpoint::all())
            ->endpoint(ApiEndpoint::create()->postedDataMethod(PostedDataMethods::POST))
            ->endpoint(ApiEndpoint::find())
            ->endpoint(ApiEndpoint::update()->postedDataMethod(PostedDataMethods::POST))
            ->endpoint(ApiEndpoint::remove())
        ;
    }
}
