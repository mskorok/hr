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
use App\Model\Settings;
use App\Model\Users;
use App\Traits\Ajax;
use App\Traits\Limit;
use App\User\Service;
use Phalcon\Mvc\Model\Query\Builder as QueryBuilder;
use Phalcon\Mvc\Model\Resultset\Simple;
use Phalcon\Validation\Message\Group;
use PhalconRest\Mvc\Controllers\CrudResourceController;

/**
 * Class BaseApiController
 * @package App\Controllers
 */
class BaseApiController extends CrudResourceController
{
    use Ajax, Limit;

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

    public function onConstruct()
    {
        parent::onConstruct();
        $this->messages = new Group();
    }

    /**
     * @param $id
     * @return null
     */
    protected function getFindData($id)
    {
        $phqlBuilder = $this->phqlQueryParser->fromQuery($this->query, $this->getResource());

        $phqlBuilder
            ->andWhere(
                '[' . $this->getResource()->getModel() . '].[id] = :id:',
                ['id' => (int) $id]
            )->limit(1);

        $this->modifyReadQuery($phqlBuilder);
        $this->modifyFindQuery($phqlBuilder, $id);

        /** @var Simple $results */
        $results = $phqlBuilder->getQuery()->execute();

        return \count($results) >= 1 ? $results->getFirst() : null;
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
        $limit = $this->request->getQuery('limit', 'int');
        if (!empty($limit) || $limit > $this->limit) {
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
        $service =  $this->userService;
        $role = $service->getRole();
        return \in_array($role, [AclRoles::SUPERADMIN, AclRoles::ADMIN], true);
    }

    /**
     * @return Users|\Phalcon\Mvc\Model\ResultInterface
     */
    protected function getAdminUser()
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
            'bind'       => [
                'name' => SettingsConst::ADMIN_USER
            ]
        ])->getIntegerData();
    }
}
