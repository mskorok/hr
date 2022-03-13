<?php
declare(strict_types=1);

namespace App\Resources;

use App\Controllers\SettingsController;
use App\Model\Settings;
use App\Transformers\SettingsTransformer;
use PhalconApi\Constants\PostedDataMethods;
use App\Api\Endpoint as ApiEndpoint;
use PhalconRest\Api\ApiResource;
use App\Constants\AclRoles;

/**
 * Class SettingsResource
 * @package App\Resources
 */
class SettingsResource extends ApiResource
{

    public function initialize()
    {
        $this
            ->name('Settings')
            ->model(Settings::class)
            ->expectsJsonData()
            ->transformer(SettingsTransformer::class)
            ->itemKey('setting')
            ->collectionKey('settings')
//            ->allow(AclRoles::ADMIN)
//            ->allow(AclRoles::SUPERADMIN)
            ->allow(AclRoles::UNAUTHORIZED)
            ->handler(SettingsController::class)

            ->endpoint(ApiEndpoint::all())
            ->endpoint(ApiEndpoint::create()->postedDataMethod(PostedDataMethods::POST))
            ->endpoint(ApiEndpoint::find())
            ->endpoint(ApiEndpoint::update()->postedDataMethod(PostedDataMethods::POST))
            ->endpoint(ApiEndpoint::remove())
        ;
    }
}
