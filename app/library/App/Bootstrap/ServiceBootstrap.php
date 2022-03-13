<?php
declare(strict_types=1);

namespace App\Bootstrap;

use App\Mail\MailService;
use App\Payments\DummyPaymentService;
use App\Services\Geo\IPGeoBase;
use Phalcon\Config;
use Phalcon\Mailer\Manager;
use Phalcon\Queue\Beanstalk;
use PhalconRest\Api;
use Phalcon\DiInterface;
use App\BootstrapInterface;
use App\Constants\Services;
use App\Auth\UsernameAccountType;
use App\Fractal\CustomSerializer;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Mvc\View\Simple as View;
use App\Services\UsersService;
use App\Auth\Manager as AuthManager;
use Phalcon\Events\Manager as EventsManager;
use League\Fractal\Manager as FractalManager;
use Phalcon\Mvc\Model\Manager as ModelsManager;
use PhalconApi\Auth\TokenParsers\JWTTokenParser;
use Phalcon\Logger\Adapter\File as FileAdapter;

/**
 * Class ServiceBootstrap
 * @package App\Bootstrap
 */
class ServiceBootstrap implements BootstrapInterface
{
    public function run(Api $api, DiInterface $di, Config $config)
    {
        /**
         * @description Config - \Phalcon\Config
         */
        $di->setShared(Services::CONFIG, $config);

        /**
         * @description Phalcon - \Phalcon\Db\Adapter\Pdo\Mysql
         */
        $di->set(Services::DB, function () use ($config, $di) {

            $config = $config->get('database')->toArray();
            $adapter = $config['adapter'];
            unset($config['adapter']);
            $class = 'Phalcon\Db\Adapter\Pdo\\' . $adapter;

            $connection = new $class($config);

            // Assign the eventsManager to the db adapter instance
            $connection->setEventsManager($di->get(Services::EVENTS_MANAGER));

            return $connection;
        });

        /**
         * @description Phalcon - \Phalcon\Mvc\Url
         */
        $di->set(Services::URL, function () use ($config) {

            $url = new UrlResolver;
            $url->setBaseUri($config->get('application')->baseUri);
            return $url;
        });

        /**
         * @description Phalcon - \Phalcon\Mvc\View\Simple
         */
        $di->set(Services::VIEW, function () use ($config) {

            $view = new View;
            $view->setViewsDir($config->get('application')->viewsDir);

            return $view;
        });

        /**
         * @description Phalcon - EventsManager
         */
        $di->setShared(Services::EVENTS_MANAGER, function () use ($di, $config) {

            return new EventsManager;
        });

        /**
         * @description Phalcon - TokenParsers
         */
        $di->setShared(Services::TOKEN_PARSER, function () use ($di, $config) {

            return new JWTTokenParser($config->get('authentication')->secret, JWTTokenParser::ALGORITHM_HS256);
        });

        /**
         * @description Phalcon - AuthManager
         */
        $di->setShared(Services::AUTH_MANAGER, function () use ($di, $config) {

            $authManager = new AuthManager($config->get('authentication')->expirationTime);
            $authManager->registerAccountType(UsernameAccountType::NAME, new UsernameAccountType);

            return $authManager;
        });

        /**
         * @description Phalcon - \Phalcon\Mvc\Model\Manager
         */
        $di->setShared(Services::MODELS_MANAGER, function () use ($di) {

            $modelsManager = new ModelsManager;
            return $modelsManager->setEventsManager($di->get(Services::EVENTS_MANAGER));
        });

        /**
         * @description PhalconRest - \League\Fractal\Manager
         */
        $di->setShared(Services::FRACTAL_MANAGER, function () {

            $fractal = new FractalManager;
            $fractal->setSerializer(new CustomSerializer);

            return $fractal;
        });

        /**
         * @description PhalconRest - \PhalconRest\User\Service
         */
        $di->setShared(Services::USER_SERVICE, new UsersService);

        /**
         * @description PhalconRest - \App\MailService
         */
        $di->setShared(Services::MAIL, function () use ($config, $di) {
            $mailer = new Manager($config->get('mail')->toArray());
            return new MailService($mailer, $di, $config);
        });


        /**
         * @description logger  Phalcon\Logger\Adapter\File
         */
        $di->setShared(Services::LOG, function () use ($config) {
            return new FileAdapter($config->get('log')->path);
        });

        /**
         * @description PhalconRest - \App\Payments\DummyPaymentService
         */
        $di->setShared(Services::PAYMENT_SERVICE, new DummyPaymentService);

        /**
         * @description Geo IP Service
         */
        $di->setShared(Services::GEO, new IPGeoBase());


        $di->setShared(
            Services::QUEUE,
            function () use ($config) {
                if (!isset($config->beanstalk->host)) {
                    throw new \RuntimeException('Beanstalk is not configured');
                }
                return new Beanstalk(['host' => $config->get('beanstalk')->host]);
            }
        );
    }
}
