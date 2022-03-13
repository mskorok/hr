<?php
declare(strict_types=1);

namespace App\Bootstrap;

use App\BootstrapInterface;
use App\Controllers\MessengerController;
use Phalcon\Config;
use Phalcon\DiInterface;
use PhalconRest\Api;

/**
 * Class MessagesRouteBootstrap
 * @package App\Bootstrap
 */
class MessagesRouteBootstrap implements BootstrapInterface
{
    /**
     * @param Api $api
     * @param DiInterface $di
     * @param Config $config
     */
    public function run(Api $api, DiInterface $di, Config $config): void
    {

        /********************************     PRODUCTION ROUTES   ****************************/

        /***************  MESSAGES ****************/

        $messengerController = new MessengerController();
        $messengerController->setDI($api->di);


        $api->post(
            '/messages/send/all',
            [$messengerController, 'sendAllMessages']
        );
        $api->post(
            '/messages/send/simple',
            [$messengerController, 'sendSimpleMessage']
        );

        $api->post(
            '/messages/get/all',
            [$messengerController, 'getMessages']
        );
        $api->get(
            '/messages/get/all',
            [$messengerController, 'getMessages']
        );

        $api->get(
            '/messages/employer/send/{id}',
            [$messengerController, 'sendMessagesToEmployer']
        );

        $api->get(
            '/messages/employers/send',
            [$messengerController, 'sendMessagesToEmployers']
        );

        $api->get(
            '/messages/applicant/send/{id}',
            [$messengerController, 'sendMessagesToApplicant']
        );

        $api->get(
            '/messages/applicants/send',
            [$messengerController, 'sendMessagesToApplicants']
        );
    }
}
