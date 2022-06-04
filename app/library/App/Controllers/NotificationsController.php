<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 08.05.19
 * Time: 11:58
 */

namespace App\Controllers;

use App\Constants\Notification;
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
            ],
            'limit' => 50,
            'order' => 'creationDate DESC'
        ]);

        return $this->createArrayResponse($notifications, 'data');

    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function createNotification()
    {
        /** @var Users $user */
        $user = $this->userService->getDetails();
        if (!$user) {
            return $this->createErrorResponse('User not found');
        }

        $params = $this->request->getJsonRawBody();

        $params = json_decode(json_encode($params), true);

        if (isset($params['category'])) {
            $categories = explode(',', $params['category']);

            if (count($categories) > 0) {
                $messages = [];
                foreach ($categories as $category) {
                    $notification = new Notifications();
                    $params['creator_id'] = $user->getId();
                    if (!in_array($category, Notification::CATEGORIES, true)) {
                        $messages[] = $category . ' not in category list';
                        continue;
                    }
                    $params['category'] = $category;
                    if (!$notification->save($params)) {
                        $messages[] = implode(',', $notification->getMessages());
                    }
                }

                if (count($messages) > 0) {
                    return $this->createErrorResponse(implode(',', $messages));
                }

                return  $this->createOkResponse();
            }
        }
        $notification = new Notifications();
        $params['creator_id'] = $user->getId();
        if ($notification->save($params)) {
            return  $this->createOkResponse();
        }

        return $this->createErrorResponse(implode(',', $notification->getMessages()));
    }

}