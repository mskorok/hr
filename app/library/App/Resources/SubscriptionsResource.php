<?php
declare(strict_types=1);

namespace App\Resources;

use App\Controllers\SubscriptionsController;
use App\Model\Subscriptions;
use App\Transformers\SubscriptionsTransformer;
use PhalconApi\Constants\PostedDataMethods;
use App\Api\Endpoint as ApiEndpoint;
use PhalconRest\Api\ApiResource;
use App\Constants\AclRoles;

/**
 * Class SubscriptionsResource
 * @package App\Resources
 */
class SubscriptionsResource extends ApiResource
{

    public function initialize()
    {
        $this
            ->name('Subscriptions')
            ->model(Subscriptions::class)
            ->expectsJsonData()
            ->transformer(SubscriptionsTransformer::class)
            ->itemKey('subscription')
            ->collectionKey('subscriptions')
            ->allow(AclRoles::UNAUTHORIZED)
//            ->deny(AclRoles::UNAUTHORIZED)
            ->handler(SubscriptionsController::class)

            ->endpoint(ApiEndpoint::all())
            ->endpoint(ApiEndpoint::create()->postedDataMethod(PostedDataMethods::POST))
            ->endpoint(ApiEndpoint::find())
            ->endpoint(ApiEndpoint::update()->postedDataMethod(PostedDataMethods::POST))
            ->endpoint(ApiEndpoint::remove())
        ;
    }
}
