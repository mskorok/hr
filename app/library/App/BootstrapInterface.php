<?php
declare(strict_types=1);

namespace App;

use Phalcon\Config;
use Phalcon\DiInterface;
use PhalconRest\Api;

/**
 * Interface BootstrapInterface
 * @package App
 */
interface BootstrapInterface
{

    /**
     * @param Api $api
     * @param DiInterface $di
     * @param Config $config
     * @return mixed
     */
    public function run(Api $api, DiInterface $di, Config $config);

}