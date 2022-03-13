<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: mike
 * Date: 28.10.17
 * Time: 15:58
 */

use App\Constants\Services as Constants ;
use App\Constants\Services;
use App\Fractal\CustomSerializer;
use App\Mail\MailService;
use Phalcon\Di\FactoryDefault\Cli as CliDI;
use Phalcon\Cli\Console as ConsoleApp;
use Phalcon\Loader;
use League\Fractal\Manager as FractalManager;
use Phalcon\Mvc\Model\Manager as ModelsManager;
use Phalcon\Mailer\Manager;
use Phalcon\Queue\Beanstalk;
use App\User\Service as UserService;
use Phalcon\Mvc\View\Simple as View;

define('ROOT_DIR', __DIR__ . '/..');
define('APP_DIR', ROOT_DIR . '/app');
define('VENDOR_DIR', ROOT_DIR . '/vendor');
define('CONFIG_DIR', APP_DIR . '/configs');

define('APPLICATION_ENV', getenv('APPLICATION_ENV') ?: 'development');

if (!function_exists('is_iterable')) {
    /**
     * @param $var
     * @return bool
     */
    function is_iterable($var)
    {
        return is_array($var) || $var instanceof \Traversable;
    }
}

// Using the CLI factory default services container
$di = new CliDI();

// Autoload dependencies
require VENDOR_DIR . '/autoload.php';

/**
 * Register the autoloader and tell it to register the tasks directory
 */
$loader = new Loader();

$loader->registerDirs(
    [
        __DIR__ . '/tasks',
    ]
);

$loader->registerNamespaces([
    'App' => APP_DIR . '/library/App',
    'Task' =>  APP_DIR . '/tasks',
]);

$loader->register();

// Load the configuration file (if any)

$configFile = __DIR__ . '/configs/default.php';

$serverConfigFile = __DIR__ . '/configs/server.'.APPLICATION_ENV.'.php';

if (is_readable($configFile) && is_readable($serverConfigFile)) {
    $config = include $configFile;
    $serverConfig = include $serverConfigFile;
    $config = array_merge($config, $serverConfig);
    /** @var \Phalcon\Config $config */
    $config = new \Phalcon\Config($config);

    /**
     * @description Config - \Phalcon\Config
     */
    $di->setShared(Constants::CONFIG, $config);

    /**
     * @description Phalcon - \Phalcon\Db\Adapter\Pdo\Mysql
     */
    $di->set(Constants::DB, function () use ($config, $di) {

        $config = $config->get('database')->toArray();
        $adapter = $config['adapter'];
        unset($config['adapter']);
        $class = 'Phalcon\Db\Adapter\Pdo\\' . $adapter;

        $connection = new $class($config);

        // Assign the eventsManager to the db adapter instance
        $connection->setEventsManager($di->get(Constants::EVENTS_MANAGER));

        return $connection;
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
     * @description Phalcon - \Phalcon\Mvc\Model\Manager
     */
    $di->setShared(Constants::MODELS_MANAGER, function () use ($di) {

        $modelsManager = new ModelsManager;
        return $modelsManager->setEventsManager($di->get(Constants::EVENTS_MANAGER));
    });

    /**
     * @description PhalconRest - \League\Fractal\Manager
     */
    $di->setShared(Constants::FRACTAL_MANAGER, function () {

        $fractal = new FractalManager;
        $fractal->setSerializer(new CustomSerializer);

        return $fractal;
    });

    /**
     * @description PhalconRest - \PhalconRest\User\Service
     */
    $di->setShared(Constants::USER_SERVICE, new UserService);

    /**
     * @description PhalconRest - \App\MailService
     */
    $di->setShared(Constants::MAIL, function () use ($config, $di) {
        $mailer = new Manager($config->get('mail')->toArray());
        $mailService = new MailService($mailer, $di, $di->get(Constants::CONFIG));
        $mailService->setConfig($config);
        return $mailService;
    });

    $di->setShared(
        Constants::QUEUE,
        function () use ($config) {
            if (!isset($config['beanstalk']['host'])) {
                throw new \RuntimeException('Beanstalk is not configured');
            }
            return new Beanstalk(
                [
                    'host' => $config['beanstalk']['host'],
                    'port' => $config['beanstalk']['port']
                ]
            );
        }
    );
}

// Create a console application
$console = new ConsoleApp();

$di->setShared('console', $console);

$console->setDI($di);



/**
 * Process the console arguments
 */
$arguments = [];

foreach ($argv as $k => $arg) {
    if ($k === 1) {
        $arguments['task'] = $arg;
    } elseif ($k === 2) {
        $arguments['action'] = $arg;
    } elseif ($k >= 3) {
        $arguments['params'][] = $arg;
    }
}

try {
    // Handle incoming arguments
    $console->handle($arguments);
} catch (\Phalcon\Exception $e) {
    echo $e->getMessage();

    die(255);
}
