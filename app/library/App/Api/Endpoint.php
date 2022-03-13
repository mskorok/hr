<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 27.09.17
 * Time: 15:50
 */

namespace App\Api;

use PhalconApi\Constants\HttpMethods;
use PhalconRest\Api\ApiEndpoint;

/**
 * Class Endpoint
 * @package App\Api
 */
class Endpoint extends ApiEndpoint
{

    /**
     * Returns pre-configured update endpoint
     *
     * @return static
     */
    public static function update()
    {
        return static::factory('/{id:[0-9]+}', HttpMethods::POST, 'update')
            ->name(self::UPDATE)
            ->description('Updates an existing item identified by {id}, using the posted data');
    }

    /**
     * Returns pre-configured find endpoint
     *
     * @return static
     */
    public static function find()
    {
        return static::factory('/{id:[0-9]+}', HttpMethods::GET, 'find')
            ->name(self::FIND)
            ->description('Returns the item identified by {id}');
    }


    /**
     * Returns pre-configured remove endpoint
     *
     * @return static
     */
    public static function remove()
    {
        return static::factory('/{id:[0-9]+}', HttpMethods::DELETE, 'remove')
            ->name(self::REMOVE)
            ->description('Removes the item identified by {id}');
    }
}
