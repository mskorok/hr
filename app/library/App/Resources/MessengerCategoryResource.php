<?php

namespace App\Resources;

use App\Controllers\MessengerCategoryController;
use App\Model\MessengerCategory;
use App\Transformers\MessengerCategoryTransformer;
use PhalconApi\Constants\PostedDataMethods;
use App\Api\Endpoint as ApiEndpoint;
use PhalconRest\Api\ApiResource;
use App\Constants\AclRoles;

/**
 * Class MessengerCategoryResource
 * @package App\Resources
 */
class MessengerCategoryResource extends ApiResource
{

    public function initialize()
    {
        $this
            ->name('MessengerCategory')
            ->model(MessengerCategory::class)
            ->expectsJsonData()
            ->transformer(MessengerCategoryTransformer::class)
            ->itemKey('messengerCategory')
            ->collectionKey('messengerCategories')
            ->allow(AclRoles::UNAUTHORIZED)
//            ->deny(AclRoles::UNAUTHORIZED)
            ->handler(MessengerCategoryController::class)

            ->endpoint(ApiEndpoint::all())
            ->endpoint(ApiEndpoint::create()->postedDataMethod(PostedDataMethods::POST))
            ->endpoint(ApiEndpoint::find())
            ->endpoint(ApiEndpoint::update()->postedDataMethod(PostedDataMethods::POST))
            ->endpoint(ApiEndpoint::remove());
    }
}
