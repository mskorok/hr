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
            '/messages/get/parent',
            [$messengerController, 'getActiveParentMessages']
        );

        $api->post(
            '/messages/send/{id}',
            [$messengerController, 'sendMessage']
        );

        $api->post(
            '/messages/employer/send/{id}',
            [$messengerController, 'sendMessagesToEmployer']
        );

        $api->post(
            '/messages/employers/send',
            [$messengerController, 'sendMessagesToEmployers']
        );

        $api->post(
            '/messages/support/send/{id}',
            [$messengerController, 'sendMessagesToSupport']
        );

        $api->get(
            '/messages/applicant/send/{id}',
            [$messengerController, 'sendMessagesToApplicant']
        );

        $api->post(
            '/messages/applicants/send',
            [$messengerController, 'sendMessagesToApplicants']
        );
    }
}
