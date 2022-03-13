<?php
declare(strict_types=1);

namespace App\Resources;

use App\Controllers\EducationalInstitutionsController;
use App\Model\Articles;
use App\Transformers\EducationalInstitutionsTransformer;
use PhalconApi\Constants\PostedDataMethods;
use PhalconRest\Api\ApiResource;
use App\Api\Endpoint as ApiEndpoint;
use App\Constants\AclRoles;

/**
 * Class ArticlesResource
 * @package App\Resources
 */
class EducationalInstitutionsResource extends ApiResource
{

    public function initialize()
    {
        $this
            ->name('EducationalInstitutions')
            ->model(Articles::class)
            ->expectsJsonData()
            ->transformer(EducationalInstitutionsTransformer::class)
            ->handler(EducationalInstitutionsController::class)
            ->itemKey('institution')
            ->collectionKey('institutions')
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
                ApiEndpoint::get('/list', 'list')
                    ->allow(AclRoles::UNAUTHORIZED)
                    ->description('get all institutions')
            )
        ;
    }
}
