<?php
declare(strict_types=1);

namespace App\Resources;

use App\Controllers\SkillsController;
use App\Model\Skills;
use App\Transformers\SkillsTransformer;
use PhalconApi\Constants\PostedDataMethods;
use App\Api\Endpoint as ApiEndpoint;
use PhalconRest\Api\ApiResource;
use App\Constants\AclRoles;

/**
 * Class SkillsResource
 * @package App\Resources
 */
class SkillsResource extends ApiResource
{

    public function initialize()
    {
        $this
            ->name('Skills')
            ->model(Skills::class)
            ->expectsJsonData()
            ->transformer(SkillsTransformer::class)
            ->itemKey('skill')
            ->collectionKey('skills')
//            ->allow(AclRoles::ADMIN)
//            ->allow(AclRoles::SUPERADMIN)
            ->allow(AclRoles::UNAUTHORIZED)
            ->handler(SkillsController::class)

            ->endpoint(ApiEndpoint::all())
            ->endpoint(ApiEndpoint::create()->postedDataMethod(PostedDataMethods::POST))
            ->endpoint(ApiEndpoint::find())
            ->endpoint(ApiEndpoint::update()->postedDataMethod(PostedDataMethods::POST))
            ->endpoint(ApiEndpoint::remove())
        ;
    }
}
