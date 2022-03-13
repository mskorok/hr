<?php
declare(strict_types=1);

namespace App\Resources;

use App\Controllers\TagController;
use App\Model\Tag;
use App\Transformers\TagTransformer;
use PhalconApi\Constants\PostedDataMethods;
use PhalconRest\Api\ApiResource;
use App\Api\Endpoint as ApiEndpoint;
use App\Constants\AclRoles;

/**
 * Class TagResource
 * @package App\Resources
 */
class TagResource extends ApiResource
{

    public function initialize()
    {
        $this
            ->name('Tag')
            ->model(Tag::class)
            ->expectsJsonData()
            ->transformer(TagTransformer::class)
            ->handler(TagController::class)
            ->itemKey('tag')
            ->collectionKey('tags')
//            ->allow([AclRoles::ADMIN, AclRoles::SUPERADMIN, AclRoles::EMPLOYER, AclRoles::MANAGER])
//            ->deny(AclRoles::UNAUTHORIZED)
            ->allow(AclRoles::UNAUTHORIZED)

            ->endpoint(
                ApiEndpoint::all()
                    ->allow([AclRoles::ADMIN, AclRoles::SUPERADMIN, AclRoles::EMPLOYER, AclRoles::MANAGER])
                    ->description('Returns all articles')
            )
            ->endpoint(
                ApiEndpoint::create()
                    ->allow([AclRoles::ADMIN, AclRoles::SUPERADMIN, AclRoles::APPLICANT])
                    ->postedDataMethod(PostedDataMethods::POST)
            )
            ->endpoint(
                ApiEndpoint::find()
                    ->allow([AclRoles::ADMIN, AclRoles::SUPERADMIN, AclRoles::EMPLOYER, AclRoles::MANAGER])
            )
            ->endpoint(
                ApiEndpoint::update()
                    ->allow([AclRoles::ADMIN, AclRoles::SUPERADMIN, AclRoles::APPLICANT])
                    ->postedDataMethod(PostedDataMethods::POST)
            )
            ->endpoint(
                ApiEndpoint::remove()
                    ->allow([AclRoles::ADMIN, AclRoles::SUPERADMIN, AclRoles::APPLICANT])
            )
        ;
    }
}
