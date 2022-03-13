<?php
defined('BASE_PATH') || define('BASE_PATH', getenv('BASE_PATH') ?: realpath(dirname(__FILE__) . '/../..'));
defined('APP_PATH') || define('APP_PATH', BASE_PATH . '/app');

return new \Phalcon\Config([
    'database' => [
        'adapter'     => 'Mysql',
        'host'        => 'localhost',
        'username'    => 'root',
        'password'    => 'mike999',
        'dbname'      => 'hr',
        'charset'     => 'utf8'
    ],
    'application' => [
        'appDir'         => APP_PATH . '/',
        'controllersDir' => APP_PATH . '/library/App/Controllers/',
        'modelsDir'      => APP_PATH . '/library/App/Model/',
        'migrationsDir'  => APP_PATH . '/migrations/',
        'baseUri'        => '/home/mike/projects/PhpstormProjects/hr/',
        'viewsDir'       => APP_PATH . '/views/',
        'pluginsDir'     => APP_PATH . '/plugins/',
        'libraryDir'     => APP_PATH . '/library/',
        'cacheDir'       => BASE_PATH . '/cache/',

        // This allows the baseUri to be understand project paths that are not in the root directory
        // of the webpspace.  This will break if the public/index.php entry point is moved or
        // possibly if the web server rewrite rules are changed. This can also be set to a static path.
        //'baseUri'        => preg_replace('/public([\/\\])index.php$/', '', $_SERVER["PHP_SELF"]),
    ]
]);
