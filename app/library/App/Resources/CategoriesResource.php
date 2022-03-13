<?php
declare(strict_types=1);

namespace App\Resources;

use App\Controllers\CategoriesController;
use App\Model\Categories;
use App\Transformers\CategoriesTransformer;
use PhalconApi\Constants\PostedDataMethods;
use PhalconRest\Api\ApiResource;
use App\Api\Endpoint as ApiEndpoint;
use App\Constants\AclRoles;

/**
 * Class CategoriesResource
 * @package App\Resources
 */
class CategoriesResource extends ApiResource
{

    public function initialize()
    {
        $this
            ->name('Categories')
            ->model(Categories::class)
            ->expectsJsonData()
            ->transformer(CategoriesTransformer::class)
            ->handler(CategoriesController::class)
            ->itemKey('category')
            ->collectionKey('categories')
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
            ->endpoint(
                ApiEndpoint::get('/sub/{name}', 'getSubcategories')
                    ->allow(AclRoles::UNAUTHORIZED)
                    ->description('get all subcategories of category')
            )
        ;
    }
}
