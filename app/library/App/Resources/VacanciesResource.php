<?php
declare(strict_types=1);

namespace App\Resources;

use App\Controllers\VacanciesController;
use App\Model\Vacancies;
use App\Transformers\VacanciesTransformer;
use PhalconApi\Constants\PostedDataMethods;
use App\Api\Endpoint as ApiEndpoint;
use PhalconRest\Api\ApiResource;
use App\Constants\AclRoles;

/**
 * Class VacanciesResource
 * @package App\Resources
 */
class VacanciesResource extends ApiResource
{

    public function initialize()
    {
        $this
            ->name('Vacancies')
            ->model(Vacancies::class)
            ->expectsJsonData()
            ->transformer(VacanciesTransformer::class)
            ->itemKey('vacancy')
            ->collectionKey('vacancies')
//            ->allow(AclRoles::ADMIN)
//            ->allow(AclRoles::SUPERADMIN)
            ->allow(AclRoles::UNAUTHORIZED)
            ->handler(VacanciesController::class)

            ->endpoint(ApiEndpoint::all())
            ->endpoint(ApiEndpoint::create()->postedDataMethod(PostedDataMethods::POST))
            ->endpoint(ApiEndpoint::find())
            ->endpoint(ApiEndpoint::update()->postedDataMethod(PostedDataMethods::POST))
            ->endpoint(ApiEndpoint::remove())
        ;
    }
}
