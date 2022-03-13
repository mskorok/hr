<?php
declare(strict_types=1);

namespace App\Resources;

use App\Controllers\ImagesController;
use App\Api\Endpoint as ApiEndpoint;
use App\Model\Images;
use App\Transformers\ImagesTransformer;
use PhalconRest\Api\ApiResource;
use App\Constants\AclRoles;

/**
 * Class ImagesResource
 * @package App\Resources
 */
class ImagesResource extends ApiResource
{

    public function initialize()
    {
        $this
            ->name('Images')
            ->model(Images::class)
            ->expectsPostData()
            ->transformer(ImagesTransformer::class)
            ->itemKey('image')
            ->collectionKey('images')
            ->allow(AclRoles::UNAUTHORIZED)
            ->handler(ImagesController::class)

            ->endpoint(ApiEndpoint::all())
            ->endpoint(ApiEndpoint::create())
            ->endpoint(ApiEndpoint::find())
            ->endpoint(ApiEndpoint::remove());
    }
}
