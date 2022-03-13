<?php
declare(strict_types=1);

namespace App\Resources;

use App\Controllers\PaymentController;
use App\Controllers\PaymentsController;
use App\Model\Payment;
use App\Model\Payments;
use App\Model\ProfessionalExperience;
use App\Transformers\PaymentsTransformer;
use App\Transformers\PaymentTransformer;
use App\Transformers\ProfessionalExperienceTransformer;
use PhalconApi\Constants\PostedDataMethods;
use App\Api\Endpoint as ApiEndpoint;
use PhalconRest\Api\ApiResource;
use App\Constants\AclRoles;

/**
 * Class ProfessionalExperienceResource
 * @package App\Resources
 */
class ProfessionalExperienceResource extends ApiResource
{

    public function initialize()
    {
        $this
            ->name('ProfessionalExperience')
            ->model(ProfessionalExperience::class)
            ->expectsJsonData()
            ->transformer(ProfessionalExperienceTransformer::class)
            ->itemKey('experience')
            ->collectionKey('experiences')
            ->allow(AclRoles::UNAUTHORIZED)
//            ->deny(AclRoles::UNAUTHORIZED)
            ->handler(ProfessionalExperience::class)

            ->endpoint(ApiEndpoint::all())
            ->endpoint(ApiEndpoint::create()->postedDataMethod(PostedDataMethods::POST))
            ->endpoint(ApiEndpoint::find())
            ->endpoint(ApiEndpoint::update()->postedDataMethod(PostedDataMethods::POST))
            ->endpoint(ApiEndpoint::remove())
        ;
    }
}
