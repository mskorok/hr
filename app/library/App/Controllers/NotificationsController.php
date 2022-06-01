<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 08.05.19
 * Time: 11:58
 */

namespace App\Controllers;

use App\Model\Notifications;
use App\Model\Users;
use PhalconApi\Exception;

/**
 * Class NotificationsController
 * @package App\Controllers
 */
class NotificationsController  extends ControllerBase
{
    /**
     * @param string|null $category
     * @return mixed
     * @throws Exception
     */
    public function getNotifications(string $category = null)
    {
        /** @var Users $user */
        $user = $this->userService->getDetails();
        if (!$user) {
            return $this->createErrorResponse('User not found');
        }
        if (!$category) {
            $category = $user->getRole();
        }
        $date = (new \DateTime())->modify('-1 month')->format('Y-m-d H:i:s');
        $notifications = Notifications::find([
            'conditions' => ' category = :cat: AND creationDate > :date: ',
            'bind' => [
                'cat' =>  $category,
                'date' =>  $date,
            ]
        ]);

        return $this->createArrayResponse($notifications, 'data');

    }

}