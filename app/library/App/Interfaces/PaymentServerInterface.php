<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 14.11.17
 * Time: 20:33
 */

namespace App\Interfaces;

/**
 * Interface PaymentServerInterface
 * @package App\Interfaces
 */
interface PaymentServerInterface
{
    /**
     * @param array $params
     * @return mixed
     */
    public function processPayment(array $params);
}
