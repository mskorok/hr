<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 01.11.17
 * Time: 17:24
 */

namespace App\Controllers;

use App\Constants\AclRoles;
use App\Constants\Settings as SettingsConst;
use App\Constants\Services;
use App\Model\Users;
use App\Traits\Ajax;
use App\Traits\Limit;
use App\User\Service;
use App\Model\Settings;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Query\Builder as QueryBuilder;
use Phalcon\Mvc\Model\ResultInterface;
use Phalcon\Mvc\Model\Resultset\Simple;
use Phalcon\Validation\Message\Group;
use PhalconRest\Mvc\Controllers\CrudResourceController;

/**
 * Class ControllerBase
 * @package App\Controllers
 */
class ControllerBase extends CrudResourceController
{
    use Ajax, Limit;

    public static $encodedFields = [];

    /**
     * @var array
     */
    public static $availableIncludes = [];

    /**
     * @var array
     */
    public static $searchFields = [];

    /**
     * @var array
     */
    protected $formArray = [];

    /**
     * @var Group
     */
    protected $messages;

    public function onConstruct(): void
    {
        parent::onConstruct();
        $this->messages = new Group();
    }

    /**
     * @return mixed
     */
    protected function getAllData()
    {

        $collection = parent::getAllData();

        foreach ($collection as $item) {
            $this->afterFind($item);
        }
        return $collection;
    }

    /**
     * @param $id
     * @return null|mixed
     */
    protected function getFindData($id)
    {
        parent::getFindData($id);
        $phqlBuilder = $this->phqlQueryParser->fromQuery($this->query, $this->getResource());

        $phqlBuilder
            ->andWhere(
                '[' . $this->getResource()->getModel() . '].[id] = :id:',
                ['id' => (int)$id]
            )->limit(1);

        $this->modifyReadQuery($phqlBuilder);
        $this->modifyFindQuery($phqlBuilder, $id);

        /** @var Simple $results */
        $results = $phqlBuilder->getQuery()->execute();

        $result = \count($results) >= 1 ? $results->getFirst() : null;

        if (is_object($result)) {
            $this->afterFind($result);
        }

        return $result;
    }

    /**
     * @param $error
     * @return mixed
     */
    protected function createErrorResponse($error)
    {
        $response = ['result' => 'error', 'message' => $error];

        return $this->createResponse($response);
    }

    /**
     * @param QueryBuilder $query
     */
    protected function modifyAllQuery(QueryBuilder $query): void
    {
        $limit = $this->request->getQuery('limit');
        if (!$limit || $limit > $this->limit) {
            $query->limit($this->limit);
        }
    }

    /**
     * @param $data
     * @return mixed
     * @throws \RuntimeException
     */
    protected function onDataInvalid($data)
    {
        try {
            parent::onDataInvalid($data);
        } catch (\Exception $exception) {
            $this->di->get(Services::LOG)->warning($exception->getMessage());
        }
        $mes = [];
        foreach ($this->messages as $message) {
            $mes[] = $message->getMessage();
        }
        return $this->createErrorResponse($mes);
    }

    /**
     * @return bool
     * @throws \PhalconApi\Exception
     */
    protected function isAdminUser(): bool
    {
        /** @var Service $service */
        $service = $this->userService;
        $role = $service->getRole();
        return \in_array($role, [AclRoles::SUPERADMIN, AclRoles::ADMIN], true);
    }

    /**
     * @param $item
     * @return bool
     */
    protected function afterFind($item): bool
    {
        if (!is_object($item)) {
            return false;
        }

        foreach (static::$encodedFields as $field) {
            $method = 'get' . ucwords($field);
            $setMethod = 'set' . ucwords($field);
            if (method_exists($item, $method) && method_exists($item, $setMethod) && is_string($item->$method())) {
                $item->$setMethod(html_entity_decode($item->$method()));
            }
        }
        return true;
    }

    /**
     * @param $key
     * @param $value
     * @param $data
     * @return mixed
     */
    protected function transformPostDataValue($key, $value, $data)
    {
        $value = $this->sanitizePostDataValue($key, $value);
        return parent::transformPostDataValue($key, $value, $data);
    }

    /**
     * @param $params
     * @return array
     */
    protected function sanitizePostData($params): array
    {
        $result = [];

        foreach ($params as $key => $value) {
            $result[$key] = $this->sanitizePostDataValue($key, $value);
        }

        return $result;
    }

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    protected function sanitizePostDataValue($key, $value)
    {
        $fields = static::$encodedFields;
        if (in_array($key, $fields, true)) {
            $value = htmlspecialchars($value);
        }
        return $value;
    }

    /**
     * @param $model
     * @return bool
     */
    protected function transformModelBeforeSave($model): bool
    {
        if (!is_object($model)) {
            return false;
        }

        foreach (static::$encodedFields as $field) {
            $method = 'get' . ucwords($field);
            $setMethod = 'set' . ucwords($field);
            if (method_exists($model, $method) && method_exists($model, $setMethod)  && is_string($model->$method())) {
                $model->$setMethod(htmlspecialchars($model->$method()));

            }
        }
        return true;
    }

    /**
     * @return Users|ResultInterface|null
     */
    protected function getAdminUser(): ?Users
    {
        $adminId = $this->getAdminUserId();

        return Users::findFirst($adminId);
    }

    /**
     * @return integer
     */
    protected function getAdminUserId(): int
    {
        return Settings::findFirst([
            'conditions' => 'name = :name:',
            'bind' => [
                'name' => SettingsConst::ADMIN_USER
            ]
        ])->getIntegerData();
    }

    /**
     * @param iterable $collection
     * @return array
     */
    protected function getComplexArray(iterable $collection): array
    {
        $results = [];
        /** @var Model $item */
        foreach ($collection as $item) {
            if (is_object($item)) {
                $result = $item->toArray();
            } else {
                $result = $item;
            }

            foreach (static::$availableIncludes as $include) {
                $method = 'get' . $include;
                $result[$include] = $item->$method();
//                if (method_exists($item, $method)) {
//                    $result[$include] = $item->$method();
//                    $result[$include] = $item->$method();
//                }
            }
            $results[] = $result;
        }

        return $results;
    }

}
