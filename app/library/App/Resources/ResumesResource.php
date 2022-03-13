<?php
declare(strict_types=1);

namespace App\Resources;

use App\Controllers\ResumesController;
use App\Model\Resumes;
use App\Transformers\ResumesTransformer;
use PhalconApi\Constants\PostedDataMethods;
use App\Api\Endpoint as ApiEndpoint;
use PhalconRest\Api\ApiResource;
use App\Constants\AclRoles;

/**
 * Class ResumesResource
 * @package App\Resources
 */
class ResumesResource extends ApiResource
{

    public function initialize()
    {
        $this
            ->name('Resumes')
            ->model(Resumes::class)
            ->expectsJsonData()
            ->transformer(ResumesTransformer::class)
            ->itemKey('resume')
            ->collectionKey('resumes')
            ->allow(AclRoles::UNAUTHORIZED)
//            ->deny(AclRoles::UNAUTHORIZED)
            ->handler(ResumesController::class)

            ->endpoint(ApiEndpoint::all())
            ->endpoint(ApiEndpoint::create()->postedDataMethod(PostedDataMethods::POST))
            ->endpoint(ApiEndpoint::find())
            ->endpoint(ApiEndpoint::update()->postedDataMethod(PostedDataMethods::POST))
            ->endpoint(ApiEndpoint::remove())
        ;
    }
}
