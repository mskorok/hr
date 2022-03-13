<?php
declare(strict_types=1);

namespace App\Resources;

use App\Controllers\SubcategoryController;
use App\Model\Subcategory;
use App\Transformers\SubcategoryTransformer;
use PhalconApi\Constants\PostedDataMethods;
use PhalconRest\Api\ApiResource;
use App\Api\Endpoint as ApiEndpoint;
use App\Constants\AclRoles;

/**
 * Class SubcategoryResource
 * @package App\Resources
 */
class SubcategoryResource extends ApiResource
{

    public function initialize()
    {
        $this
            ->name('Subcategory')
            ->model(Subcategory::class)
            ->expectsJsonData()
            ->transformer(SubcategoryTransformer::class)
            ->handler(SubcategoryController::class)
            ->itemKey('subcategory')
            ->collectionKey('subcategories')
//            ->allow([AclRoles::ADMIN, AclRoles::SUPERADMIN, AclRoles::EMPLOYER, AclRoles::MANAGER])
//            ->deny(AclRoles::UNAUTHORIZED)
            ->allow(AclRoles::UNAUTHORIZED)

            ->endpoint(
                ApiEndpoint::all()
                    ->allow([AclRoles::ADMIN, AclRoles::SUPERADMIN, AclRoles::EMPLOYER, AclRoles::MANAGER])
                    ->description('Returns all Subcategory')
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
            ->endpoint(
                ApiEndpoint::get('/articles/{id}', 'getSubcategoryArticles')
                    ->allow(AclRoles::UNAUTHORIZED)
                    ->description('get all articles of subcategories')
            )
        ;
    }
}
