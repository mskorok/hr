<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Constants\AclRoles;
use App\Model\Messages;
use App\Model\Users;
use App\Traits\RelatedSearch;
use App\Validators\MessageValidator;
use Phalcon\Db\Column;
use Phalcon\Mvc\Model\Query\Builder as QueryBuilder;
use App\Traits\RenderView;
use Phalcon\Mvc\Model;
use PhalconApi\Auth\Session;

/**
 * Class MessagesController
 * @package App\Controllers
 */
class MessagesController extends ControllerBase
{
    use RenderView, RelatedSearch;

    public static $availableIncludes = [
        'Receiver', 'Addresser', 'MessengerCategory', 'Children', 'ParentMessage'
    ];

    public static $encodedFields = [
        'title',
        'content'
    ];

    /**
     * @return mixed
     */
    public function averageResponseTime()
    {
        $userId = $this->request->get('userId');
        $role = $this->request->get('role', null, AclRoles::MANAGER);
        $sql = 'Call GetTime(:user, :role);';
        /** @var array $results */
        $results = $this->db->query(
            $sql,
            [
                'user' => $userId,
                'role' => $role
            ],
            [
                'user' => Column::BIND_PARAM_INT,
                'role' => Column::BIND_PARAM_INT,
            ]
        )->fetchAll();
        $timeSum = 0;
        foreach ($results as $result) {
            $timeSum += (int) $result['tdiff'];
        }
        $time = $timeSum/\count($results);
        return $this->createArrayResponse(compact('time'), 'averageResponseTime');
    }

    /**
     * @return mixed
     */
    public function totalMessageAnswersPerDay()
    {
        $userId = $this->request->get('userId');
        $sql = 'Call MessagesPerDay(:sender);';
        /** @var array $results */
        $result = $this->db->query(
            $sql,
            [
                'sender' => $userId
            ],
            [
                'sender' => Column::BIND_PARAM_INT
            ]
        )->fetch();
        $result = array_key_exists('rslt', $result) ? (float) $result['rslt'] : 0;

        return $this->createArrayResponse(compact('result'), 'totalMessageAnswersPerDay');
    }

    /**
     * @return mixed
     */
    public function todayMessageAnswers()
    {
        $userId = $this->request->get('userId');
        $sql = 'Call TodayMessages(:sender);';
        /** @var array $results */
        $result = $this->db->query(
            $sql,
            [
                'sender' => $userId
            ],
            [
                'sender' => Column::BIND_PARAM_INT,
            ]
        )->fetch();
        $result = array_key_exists('cnt', $result) ? (float) $result['cnt'] : 0;

        return $this->createArrayResponse(compact('result'), 'todayMessageAnswers');
    }

    /**
     * @param QueryBuilder $query
     * @throws \PhalconApi\Exception
     */
    protected function modifyAllQuery(QueryBuilder $query): void
    {
        $limit = $this->request->getQuery('limit');
        if (!$limit || $limit > $this->limit) {
            $query->limit($this->limit);
        }

        $role = $this->userService->getRole();

        if (\in_array($role, [AclRoles::SUPERADMIN, AclRoles::ADMIN], true)) {
            //
        } elseif ($role === AclRoles::UNAUTHORIZED) {
            $query->andWhere('0');
        } else {
            $id = null;
            $session = $this->authManager->getSession();
            if ($session instanceof Session) {
                $id = $session->getIdentity();
            }
            if ($id) {
                $query->andWhere(function (QueryBuilder $query) use ($id) {
                    $query->andWhere('sender = :ids:', ['ids' => $id]);
                    $query->orWhere('recipient = :idr:', ['idr' => $id]);
                });
            } else {
                $query->andWhere('0');
            }
        }
    }

    /**
     * @param Model $createdItem
     * @param $data
     * @param $response
     * @throws \RuntimeException
     */
    protected function afterHandleCreate(Model $createdItem, $data, $response)
    {
        /** @var Messages $createdItem */
        $userId =  $createdItem->getRecipient();
        $user = Users::findFirst($userId);
        if (!$user) {
            throw new \RuntimeException('User not found');
        }

        /** @var Messages $createdItem */
        $senderId = $createdItem->getSender();
        $sender = Users::findFirst($senderId);
        if (!$sender) {
            throw new \RuntimeException('User not found');
        }
    }

    /**
     * @param $data
     * @param $isUpdate
     * @return bool
     */
    protected function postDataValid($data, $isUpdate): bool
    {
        $validator = new MessageValidator();
        $res = $validator->validate($data);
        $this->messages = $validator->getMessages();
        return $res->count() === 0;
    }

    /**
     * @param Model $item
     * @param $data
     */
    protected function beforeAssignData(Model $item, $data)
    {
        /** @var Messages $item */
        if ($item->getId()) {
            $item->beforeUpdate();
        } else {
            $item->beforeCreate();
        }
    }

    /**
     * @param $id
     * @throws \RuntimeException
     * @throws \PhalconApi\Exception
     */
    protected function beforeHandleRemove($id)
    {
        $admin = $this->isAdminUser();
        $service =  $this->userService;
        /** @var Users $user */
        $user = $service->getDetails();
        $model = Messages::findFirst((int) $id);
        if (!($admin || ($model && $model->getSender() === $user->getId()))) {
            throw new \RuntimeException('Only admin or owner has permission to remove Message');
        }
    }
}
