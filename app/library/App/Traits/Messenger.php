<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 27.11.17
 * Time: 15:25
 */

namespace App\Traits;

use App\Auth\Manager;
use App\Constants\AclRoles;
use App\Constants\Message as Status;
use App\Model\Messages;
use App\Model\Users;
use App\Transformers\MessagesTransformer;
use App\User\Service;
use App\Validators\MessageValidator;
use ArrayObject;
use Phalcon\Http\Request;
use Phalcon\Mvc\Model\Query\Builder;
use Phalcon\Mvc\Model\Resultset\Simple;
use Phalcon\Validation\Message\Group;
use PhalconApi\Auth\Session;
use PhalconApi\Exception;

/**
 * Trait Messenger
 * @package App\Traits
 */
trait Messenger
{
    /**
     * @return mixed
     * @throws \Exception
     *
     */
    public function sendAllMessages()
    {
        $users = $this->getUsers();
        $users = new ArrayObject($users);
        $errors = $this->sendMessages($users);
        if (\count($errors)) {
            return $this->createErrorResponse($errors);
        }
        return $this->createOkResponse();
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function sendSimpleMessage()
    {
        /** @var Request $request */
        $request = $this->request;
        /** @var Manager $authManager */
        $recipient = $request->getPost('recipient');
        if (!$recipient) {
            return $this->createErrorResponse('Recipient not exist');
        }
        /** @var [] $users */
        $users = $this->getUsers();
        if (!array_key_exists('users', $users) || !\is_array($users['users'])) {
            return $this->createErrorResponse('Users not exist');
        }
        $allowed = false;
        foreach ($users['users'] as $user) {
            if ($user['id'] === (int) $recipient) {
                $allowed = true;
            }
        }
        if (!$allowed) {
            return $this->createErrorResponse('Recipient not allowed');
        }
        return $this->sendMessage($recipient);
    }

    /**
     * @param Simple|ArrayObject $recipients
     * @return array
     * @throws \RuntimeException
     */
    protected function sendMessages($recipients): array
    {
        $params = null;
        /** @var Request $request */
        $request = $this->request;
        /** @var Manager $authManager */
        $authManager = $this->authManager;
        $session = $authManager->getSession();
        if (!$session) {
            throw new \RuntimeException('User not authorized');
        }
        $errors = [];
        /** @var Users $recipient */
        foreach ($recipients as $recipient) {
            $params = $request->getPost();
            $params['recipient'] = $recipient->getId();
            $params['sender'] = $session->getIdentity();
            $params['status'] = Status::STATUS_SENT;
            $params['sendMethod'] = $params['sendMethod'] ?? 'site';
            $params['sendMethod'] = $params['sendMethod'] ?: 'site';
            $validator = new MessageValidator();
            $message = new Messages();
            $date = (new \DateTime())->format('Y-m-d H:i:s');
            $message->setSentDate($date);
            $params['sentDate'] = $date;
            if ($validator->validate($params)->count() === 0) {
                if (!$message->save($params)) {
                    $errors[] = [
                        'message' => 'Message not saved',
                        'id' => $recipient->getId()
                    ];
                }
            } else {
                /** @var \Countable $messages */
                $messages = $validator->getMessages();
                if (\count($messages)) {
                    /** @var \Iterator $messages */
                    foreach ($messages as $message) {
                        $errors[] = [
                            'id' => $recipient->getId(),
                            'message' => $message->getMessage(),
                            'field' => $message->getField(),
                            'type' => $message->getType()
                        ];
                    }
                }
            }
        }
        return $errors;
    }

    /**
     * @param $role
     * @param $resourceKey
     *
     * @return mixed
     * @throws \RuntimeException
     * @throws Exception
     */
    public function getMessages($role = AclRoles::ALL_AUTHORIZED, $resourceKey = 'messages')
    {
        /** @var \PhalconApi\Http\Request $req */
        $req = $this->request;
        $read = $req->get('read');
        /** @var Manager $authManager */
        $authManager = $this->authManager;
        $session = $authManager->getSession();
        /** @var Service $userService */
        $userService = $this->userService;
        $senderRole = $userService->getRole();
        if (!$session) {
            throw new \RuntimeException('User not authorized');
        }
        $id = $session->getIdentity();
        $query = new Builder();
        $query->addFrom(Messages::class);
        $query->leftJoin(
            Users::class,
            '[' . Messages::class . '].[sender] = [' . Users::class . '].[id]'
        );

        if (\is_array($role)) {
            $firstRole = array_shift($role);
            $condition = '[' . Users::class . '].[role] = "' . $firstRole . '"';
            foreach ($role as $r) {
                $condition .= ' OR [' . Users::class . '].[role] = "' . $r . '"';
            }
            $query->andWhere($condition);
        } else {
            $query->andWhere('[' . Users::class . '].[role] = :name:', ['name' => $role]);
        }
        if (!\in_array($senderRole, [AclRoles::ADMIN, AclRoles::SUPERADMIN], true)) {
            $query->andWhere(
                '[' . Messages::class . '].[recipient] = :id: OR ' . '[' . Messages::class . '].[sender] = :id:',
                ['id' => $id]
            );
        }
        if ($read === 'showRead') {
            $query->andWhere('[' . Messages::class . '].[readDate] IS NOT NULL');
        } elseif ($read === 'showUnread') {
            $query->andWhere('[' . Messages::class . '].[readDate] IS NULL');
        }
        $messages = $query->getQuery()->execute();
        return $this->createCollectionResponse($messages, new MessagesTransformer(), $resourceKey);
    }

    /**
     * @param $role
     * @param $resourceKey
     * @param Simple|ArrayObject $collection
     * @return mixed
     * @throws \RuntimeException
     */
    public function getActiveMessages($role, $resourceKey, $collection)
    {
        if (\is_array($role)) {
            throw new \RuntimeException('Role is not string');
        }
        /** @var Manager $authManager */
        $authManager = $this->authManager;
        $session = $authManager->getSession();
        if (!$session) {
            throw new \RuntimeException('User not authorized');
        }
        $ids = [];
        /** @var Users $item */
        foreach ($collection as $item) {
            $ids[] = $item->getId();
        }
        $query = new Builder();
        $query->addFrom(Messages::class);
        $query->inWhere('[' . Messages::class . '].[sender]', $ids);
        $query->andWhere('[' . Messages::class . '].[recipient] = :id:', ['id' => $session->getIdentity()]);
        $messages = $query->getQuery()->execute();
        return $this->createCollectionResponse($messages, new MessagesTransformer(), $resourceKey);
    }

    /**
     * @param null $recipientId
     * @return mixed
     * @throws \RuntimeException
     */
    protected function sendMessage($recipientId = null)
    {
        $params = null;
        $res = null;
        /** @var Request $request */
        $request = $this->request;
        /** @var Manager $authManager */
        $authManager = $this->authManager;
        $params = $request->getPost();
        $session = $authManager->getSession();
        if (!$session) {
            throw new \RuntimeException('User not authorized');
        }
        if ($recipientId) {
            $params['recipient'] = (int) $recipientId;
        }

        $params['sender'] = $session->getIdentity();
        $params['status'] = Status::STATUS_SENT;
        $params['sendMethod'] = $params['sendMethod'] ?? 'site';
        $params['sendMethod'] = $params['sendMethod'] ?: 'site';
        $params['parent'] = is_numeric($params['parent']) ? $params['parent'] : null;
        $recipient = Users::findFirst($params['recipient']);
        if (!($recipient instanceof Users)) {
            return $this->createErrorResponse('Recipient not found');
        }
        $role = $recipient->getRole();
        $params['supportStatus'] = Status::SUPPORT_STATUS_OPEN;
        if (!\in_array($role, [AclRoles::ADMIN, AclRoles::MANAGER, AclRoles::SUPERADMIN], true)) {
            $params['supportStatus'] = Status::SUPPORT_STATUS_NOT_SUPPORT;
        }
        $validator = new MessageValidator();
        $message = new Messages();
        $date = (new \DateTime())->format('Y-m-d H:i:s');
        $message->setSentDate($date);
        $params['sentDate'] = $date;
        $res = $validator->validate($params) ?? null;
        if ($res->count() === 0) {
            if ($message->save($params)) {
                return $this->createOkResponse();
            }
            return $this->createErrorResponse('Message not saved');
        }
        /** @var Group $messages */
        $messages = $validator->getMessages();
        $errors = [];
        /** @var \Phalcon\Validation\Message $message */
        foreach ($messages as $message) {
            $errors[] = [
                'type' => $message->getType(),
                'field' => $message->getField(),
                'message' => $message->getMessage()
            ];
        }
        return $this->createErrorResponse($errors);
    }

    /**
     * @param $id
     * @return mixed
     * @throws \RuntimeException
     */
    public function getMessage($id)
    {
        $res = null;
        /** @var Request $request */
        $request = $this->request;
        /** @var Manager $authManager */
        $authManager = $this->authManager;
        if (!$id) {
            $id = $request->getPost('id');
        }
        $session = $authManager->getSession() ?? null;
        if (!$session) {
            throw new \RuntimeException('User not authorized');
        }
        $message = Messages::findFirst((int) $id);
        if (!($message instanceof Messages)) {
            throw new \RuntimeException('Message not found');
        }
        $this->_checkAvailability($message, $session);
        if (!($message instanceof Messages)) {
            throw new \RuntimeException('Message not found');
        }
        if ($message->getRecipient() === $session->getIdentity()) {
            $message->setStatus(Status::STATUS_READ);
            $message->setReadDate((new \DateTime())->format('Y-m-d H:i:s'));
            if (!$message->save()) {
                throw new \RuntimeException('message not saved');
            }
        }
        return $this->createItemResponse($message, new MessagesTransformer());
    }

    /**
     * @param $id
     * @return mixed
     * @throws \RuntimeException
     */
    public function setClosedSupportStatus($id)
    {
        /** @var Request $request */
        $request = $this->request;
        /** @var Manager $authManager */
        $authManager = $this->authManager;
        if (!$id) {
            $id = $request->getPost('id');
        }
        $session = $authManager->getSession() ?? null;
        if (!$session) {
            throw new \RuntimeException('User not authorized');
        }
        $message = Messages::findFirst((int) $id);
        $senderRoles = [
            AclRoles::SUPERADMIN,
            AclRoles::ADMIN,
            AclRoles::SUPERADMIN,
            AclRoles::ADMIN,
            AclRoles::MANAGER,
            AclRoles::APPLICANT,
            AclRoles::EMPLOYER
        ];
        $this->_checkAvailability($message, $session, $senderRoles);
        $message->setSupportStatus(Status::SUPPORT_STATUS_CLOSED);
        if (!$message->save()) {
            throw new \RuntimeException('message not saved');
        }

        return $this->createOkResponse();
    }

    /**
     * @param $id
     * @return mixed
     * @throws \RuntimeException
     */
    public function setProgressSupportStatus($id)
    {
        /** @var Request $request */
        $request = $this->request;
        /** @var Manager $authManager */
        $authManager = $this->authManager;
        if (!$id) {
            $id = $request->getPost('id');
        }
        $session = $authManager->getSession();
        if (!$session) {
            throw new \RuntimeException('User not authorized');
        }
        $message = Messages::findFirst((int) $id);
        $senderRoles = [
            AclRoles::SUPERADMIN,
            AclRoles::ADMIN,
            AclRoles::MANAGER,
            AclRoles::APPLICANT,
            AclRoles::EMPLOYER
        ];
        $this->_checkAvailability($message, $session, $senderRoles);
        /** @var \PhalconApi\User\Service $userService */
        $userService = $this->userService;
        try {
            $role = $userService->getDetails()->getRole();
        } catch (Exception $exception) {
            throw new \RuntimeException($exception->getMessage());
        }
        $allowedRoles = [
            AclRoles::SUPERADMIN,
            AclRoles::ADMIN,
            AclRoles::MANAGER
        ];
        if (!\in_array($role, $allowedRoles, true)
            || $session->getIdentity() === $message->getSender()
        ) {
            throw new \RuntimeException('You are not allowed for this request');
        }
        $message->setSupportStatus(Status::SUPPORT_STATUS_PROGRESS);
        if (!$message->save()) {
            throw new \RuntimeException('message not saved');
        }

        return $this->createOkResponse();
    }

    /**
     * @param Messages $message
     * @param null $session
     * @param null $senderRoles
     * @throws \RuntimeException
     */
    protected function _checkAvailability(Messages $message, $session = null, $senderRoles = null)
    {
        if (null === $session || !($session instanceof Session)) {
            /** @var Manager $authManager */
            $authManager = $this->authManager;
            $session = $authManager->getSession() ?? null;
            if (!$session) {
                throw new \RuntimeException('User not authorized');
            }
        }
        $identity = $session->getIdentity();

        /** @var \PhalconApi\User\Service $userService */
        $userService = $this->userService;
        try {
            $role = $userService->getDetails()->getRole();
        } catch (Exception $exception) {
            throw new \RuntimeException($exception->getMessage());
        }
        /** @var Users $sender */
        $sender = $message->getSender();
        if (!($sender instanceof Users)) {
            throw new \RuntimeException('Wrong message');
        }
        if ($senderRoles && !\in_array($sender->getRole(), $senderRoles, true)) {
            throw new \RuntimeException('Sender is not allowed');
        }
        if ($identity !== $message->getRecipient()
            && $identity !== $message->getSender()
            && !\in_array($role, [AclRoles::ADMIN, AclRoles::SUPERADMIN], true)
            ) {
            throw new \RuntimeException('You are not allowed for this request');
        }
    }
}
