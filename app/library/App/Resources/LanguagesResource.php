<?php
declare(strict_types=1);

namespace App\Resources;

use App\Controllers\LanguagesController;
use App\Model\Languages;
use App\Transformers\LanguagesTransformer;
use PhalconApi\Constants\PostedDataMethods;
use App\Api\Endpoint as ApiEndpoint;
use PhalconRest\Api\ApiResource;
use App\Constants\AclRoles;

/**
 * Class LanguagesResource
 * @package App\Resources
 */
class LanguagesResource extends ApiResource
{

    public function initialize()
    {
        $this
            ->name('Languages')
            ->model(Languages::class)
            ->expectsJsonData()
            ->transformer(LanguagesTransformer::class)
            ->itemKey('language')
            ->collectionKey('languages')
            ->allow(AclRoles::UNAUTHORIZED)
//            ->deny(AclRoles::UNAUTHORIZED)
            ->handler(LanguagesController::class)

            ->endpoint(ApiEndpoint::all())
            ->endpoint(ApiEndpoint::create()->postedDataMethod(PostedDataMethods::POST))
            ->endpoint(ApiEndpoint::find())
            ->endpoint(ApiEndpoint::update()->postedDataMethod(PostedDataMethods::POST))
            ->endpoint(ApiEndpoint::remove())
        ;
    }
}
