<?php
declare(strict_types=1);

namespace App\Bootstrap;

use App\BootstrapInterface;
use App\Controllers\FormController;
use Phalcon\Config;
use Phalcon\DiInterface;
use PhalconRest\Api;

/**
 * Class TestRouteBootstrap
 * @package App\Bootstrap
 */
class ApiRouteBootstrap implements BootstrapInterface
{
    /**
     * @param Api $api
     * @param DiInterface $di
     * @param Config $config
     */
    public function run(Api $api, DiInterface $di, Config $config): void
    {

        $formController = new FormController();
        $formController->setDI($api->di);

        $api->get('/api/form/get/{class}/{counter}/{show}', [$formController, 'getForm']);
        $api->post('/api/form/get/{class}/{counter}/{show}', [$formController, 'getForm']);
        $api->post('/api/form/create', [$formController, 'createForm']);
        $api->post('/api/form/create/main', [$formController, 'createMainForm']);
        $api->post('/api/form/create/image', [$formController, 'createWithImageForm']);
        $api->post('/api/form/create/related', [$formController, 'createRelatedForm']);
        $api->post('/api/form/create/related/image', [$formController, 'createRelatedImageForm']);
        $api->post('/api/form/delete/related', [$formController, 'deleteRelated']);
    }
}
