<?php
declare(strict_types=1);

namespace App\Resources;

use App\Controllers\CompanyManagerController;
use App\Model\Companies;
use App\Transformers\CompanyManagerTransformer;
use PhalconApi\Constants\PostedDataMethods;
use PhalconRest\Api\ApiResource;
use App\Api\Endpoint as ApiEndpoint;
use App\Constants\AclRoles;

/**
 * Class CompanyManagerResource
 * @package App\Resources
 */
class CompanyManagerResource extends ApiResource
{

    public function initialize()
    {
        $this
            ->name('CompanyManager')
            ->model(Companies::class)
            ->expectsJsonData()
            ->transformer(CompanyManagerTransformer::class)
            ->handler(CompanyManagerController::class)
            ->itemKey('company_manager')
            ->collectionKey('company_managers')
//            ->allow([AclRoles::ADMIN, AclRoles::SUPERADMIN, AclRoles::EMPLOYER, AclRoles::MANAGER])
//            ->deny(AclRoles::UNAUTHORIZED)
            ->allow(AclRoles::UNAUTHORIZED)

            ->endpoint(
                ApiEndpoint::all()
                    ->allow([AclRoles::ADMIN, AclRoles::SUPERADMIN, AclRoles::EMPLOYER, AclRoles::MANAGER])
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
