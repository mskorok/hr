<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Constants\Services;
use PhalconRest\Export\Documentation;
use PhalconRest\Export\Postman\ApiCollection;
use PhalconRest\Mvc\Controllers\CollectionController;
use PhalconRest\Transformers\DocumentationTransformer;
use PhalconRest\Transformers\Postman\ApiCollectionTransformer;

/**
 * Class ExportController
 * @package App\Controllers
 */
class ExportController extends CollectionController
{
    /**
     * @return mixed
     */
    public function documentation()
    {
        /** @var \Phalcon\Config $config */
        $config = $this->di->get(Services::CONFIG);

        $documentation = new Documentation($config->application->title, $config->hostName);
        $documentation->addManyCollections($this->application->getCollections());
        $documentation->addManyRoutes($this->application->getRouter()->getRoutes());

        return $this->createItemResponse($documentation, new DocumentationTransformer(), 'documentation');
    }

    /**
     * @return mixed
     */
    public function postman()
    {
        /** @var \Phalcon\Config $config */
        $config = $this->di->get(Services::CONFIG);

        $postmanCollection = new ApiCollection($config->application->title, $config->hostName);
        $postmanCollection->addManyCollections($this->application->getCollections());
        $postmanCollection->addManyRoutes($this->application->getRouter()->getRoutes());

        return $this->createItemResponse($postmanCollection, new ApiCollectionTransformer());
    }
}
