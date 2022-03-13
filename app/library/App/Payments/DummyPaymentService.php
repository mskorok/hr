<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 14.11.17
 * Time: 20:04
 */

namespace App\Payments;

use App\Constants\Services;
use App\Interfaces\PaymentServerInterface;
use App\Model\Payments;
use App\Model\Users;
use App\User\Service;
use App\Validators\PaymentsValidator;
use PhalconApi\Mvc\Plugin;

/**
 * Class DummyPaymentService
 * @package App\Payments
 */
class DummyPaymentService extends Plugin implements PaymentServerInterface
{
    /**
     * @param array $params
     * @return bool
     * @throws \PhalconApi\Exception
     */
    public function processPayment(array $params) :bool
    {
        if (!isset($params['booking_id'])) {
            return false;
        }
        /** @var Service $userService */
        $userService = $this->getDI()->get(Services::USER_SERVICE);
        $user = $userService->getDetails();

        if (!($user instanceof Users)) {
            return false;
        }
        $params['user_id'] = $user->getId();
        $params['type'] = 'transfer';
        $params['status'] = 'pending';
        $params['date'] = date('Y-m-d H:i:s');
        $validator = new PaymentsValidator();
        $res = $validator->validate($params);
        if ($res->count() === 0) {
            $payment = new Payments();
            $payment->assign($params);
            if ($payment->save()) {
                return true;
            }
        }
        return false;
    }
}
