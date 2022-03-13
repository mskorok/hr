<?php
declare(strict_types=1);

namespace App\Resources;

use App\Controllers\PaymentsController;
use App\Model\Payments;
use App\Transformers\PaymentsTransformer;
use PhalconApi\Constants\PostedDataMethods;
use App\Api\Endpoint as ApiEndpoint;
use PhalconRest\Api\ApiResource;
use App\Constants\AclRoles;

/**
 * Class PaymentsResource
 * @package App\Resources
 */
class PaymentsResource extends ApiResource
{

    public function initialize()
    {
        $this
            ->name('Payments')
            ->model(Payments::class)
            ->expectsJsonData()
            ->transformer(PaymentsTransformer::class)
            ->itemKey('payment')
            ->collectionKey('payments')
            ->allow(AclRoles::UNAUTHORIZED)
//            ->deny(AclRoles::UNAUTHORIZED)
            ->handler(PaymentsController::class)

            ->endpoint(ApiEndpoint::all())
            ->endpoint(ApiEndpoint::create()->postedDataMethod(PostedDataMethods::POST))
            ->endpoint(ApiEndpoint::find())
            ->endpoint(ApiEndpoint::update()->postedDataMethod(PostedDataMethods::POST))
            ->endpoint(ApiEndpoint::remove())
            ->endpoint(
                ApiEndpoint::post('/payment', 'setPayment')
                    ->allow(AclRoles::UNAUTHORIZED)
                    //->allow(AclRoles::AUTHORIZED)
                    ->description('Create Payment')
            )
        ;
    }
}
