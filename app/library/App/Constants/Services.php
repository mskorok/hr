<?php
declare(strict_types=1);

namespace App\Constants;

/**
 * Class Services
 * @package App\Constants
 */
class Services extends \PhalconRest\Constants\Services
{
    public const CONFIG = 'config';
    public const VIEW = 'view';
    public const API_SERVICE = 'api';
    public const MAIL = 'mail';
    public const QUEUE = 'queue';
    public const CONSOLE = 'console';
    public const PAYMENT_SERVICE = 'payment';
    public const PAYPAL = 'paypal';
    public const RECAPTCHA = 'recaptcha';
    public const LOG = 'log';
    public const GEO = 'geo';
}
