<?php
declare(strict_types=1);

namespace App\Resources;

use App\Controllers\MailController;
use App\Model\Mail;
use App\Transformers\MailTransformer;
use PhalconApi\Constants\PostedDataMethods;
use App\Api\Endpoint as ApiEndpoint;
use PhalconRest\Api\ApiResource;
use App\Constants\AclRoles;

/**
 * Class MailResource
 * @package App\Resources
 */
class MailResource extends ApiResource
{

    public function initialize()
    {
        $this
            ->name('Mail')
            ->model(Mail::class)
            ->expectsJsonData()
            ->transformer(MailTransformer::class)
            ->itemKey('mail')
            ->collectionKey('mails')
//            ->allow(AclRoles::ADMIN)
//            ->allow(AclRoles::SUPERADMIN)
            ->allow(AclRoles::UNAUTHORIZED)
            ->handler(MailController::class)

            ->endpoint(ApiEndpoint::all())
            ->endpoint(ApiEndpoint::create()->postedDataMethod(PostedDataMethods::POST))
            ->endpoint(ApiEndpoint::find())
            ->endpoint(ApiEndpoint::update()->postedDataMethod(PostedDataMethods::POST))
            ->endpoint(ApiEndpoint::remove())
        ;
    }
}
