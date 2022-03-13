<?php
declare(strict_types=1);

namespace App\Resources;

use App\Controllers\CandidatesController;
use App\Model\Candidates;
use App\Transformers\CandidatesTransformer;
use PhalconApi\Constants\PostedDataMethods;
use PhalconRest\Api\ApiResource;
use App\Api\Endpoint as ApiEndpoint;
use App\Constants\AclRoles;

/**
 * Class ArticlesResource
 * @package App\Resources
 */
class CandidatesResource extends ApiResource
{

    public function initialize()
    {
        $this
            ->name('Candidates')
            ->model(Candidates::class)
            ->expectsJsonData()
            ->transformer(CandidatesTransformer::class)
            ->handler(CandidatesController::class)
            ->itemKey('candidate')
            ->collectionKey('candidates')
//            ->allow([AclRoles::ADMIN, AclRoles::SUPERADMIN, AclRoles::EMPLOYER, AclRoles::MANAGER])
//            ->deny(AclRoles::UNAUTHORIZED)
            ->allow(AclRoles::UNAUTHORIZED)

            ->endpoint(
                ApiEndpoint::all()
                    ->allow([AclRoles::ADMIN, AclRoles::SUPERADMIN, AclRoles::EMPLOYER, AclRoles::MANAGER])
                    ->description('Returns all candidates')
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
