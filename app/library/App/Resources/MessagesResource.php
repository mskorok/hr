<?php
declare(strict_types=1);

namespace App\Resources;

use App\Controllers\MessagesController;
use App\Model\Messages;
use App\Transformers\MessagesTransformer;
use PhalconApi\Constants\PostedDataMethods;
use App\Api\Endpoint as ApiEndpoint;
use PhalconRest\Api\ApiResource;
use App\Constants\AclRoles;

/**
 * Class MessagesResource
 * @package App\Resources
 */
class MessagesResource extends ApiResource
{

    public function initialize()
    {
        $this
            ->name('Message')
            ->model(Messages::class)
            ->expectsJsonData()
            ->transformer(MessagesTransformer::class)
            ->itemKey('message')
            ->collectionKey('messages')
            ->allow(AclRoles::UNAUTHORIZED)
//            ->deny(AclRoles::UNAUTHORIZED)
            ->handler(MessagesController::class)

            ->endpoint(ApiEndpoint::all())
            ->endpoint(ApiEndpoint::create()->postedDataMethod(PostedDataMethods::POST))
            ->endpoint(ApiEndpoint::find())
            ->endpoint(ApiEndpoint::update()->postedDataMethod(PostedDataMethods::POST))
            ->endpoint(ApiEndpoint::remove())
            ->endpoint(
                ApiEndpoint::get('/averageResponseTime', 'averageResponseTime')
                    ->allow(AclRoles::UNAUTHORIZED)
                    //->allow(AclRoles::AUTHORIZED)
                    ->description(
                        'The average time is between a USER has send a message AND a SALES RESPONSIBLE has answered'
                    )
            )->endpoint(
                ApiEndpoint::get('/totalMessageAnswersPerDay', 'totalMessageAnswersPerDay')
                    ->allow(AclRoles::UNAUTHORIZED)
                    //->allow(AclRoles::AUTHORIZED)
                    ->description(
                        'Total message answers per day for SALES RESPONSIBLE'
                    )
            )->endpoint(
                ApiEndpoint::get('/todayMessageAnswers', 'todayMessageAnswers')
                    ->allow(AclRoles::UNAUTHORIZED)
                    //->allow(AclRoles::AUTHORIZED)
                    ->description(
                        'Today message answers for SALES RESPONSIBLE'
                    )
            )->endpoint(
                ApiEndpoint::post('/owner/{id}', 'isOwner')
                    ->allow(AclRoles::UNAUTHORIZED)
                    //->allow(AclRoles::AUTHORIZED)
                    ->description('Get user is message owner')
            );
    }
}
