<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Constants\Limits;
use App\Model\ExpertInfo;
use App\Traits\RenderView;
use Phalcon\Http\Response;
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Paginator\Adapter\Model as Paginator;
use Phalcon\Mvc\Model\Query\Builder as QueryBuilder;
use Phalcon\Validation\Message\Group;

/**
 * Class ExpertInfoController
 * @package App\Controllers
 */
class ExpertInfoController extends ControllerBase
{
    use RenderView;

    public static $availableIncludes = [
        'Users'
    ];

    public static $encodedFields = [
        'skills'
    ];

    /**
     * Index action
     * @throws \ReflectionException
     */
    public function indexAction()
    {
        $this->returnView('index');
    }

    /**
     * Searches for expert_info
     * @throws \ReflectionException
     */
    public function searchAction()
    {
        $numberPage = 1;
        $parameters = [];
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, ExpertInfo::class, $_POST);
            $parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery('page', 'int');
        }

        $parameters['order'] = 'id';

        $expert_info = ExpertInfo::find($parameters);
        if (\count($expert_info) === 0) {
            return $this->response->redirect('/admin/expert_info/index?notice=' . urlencode('The search did not find any expert_info'));
        }

        /** @var ExpertInfo $exp */
        foreach ($expert_info as $exp) {
            $this->afterFind($exp);

        }

        $paginator = new Paginator([
            'data' => $expert_info,
            'limit'=> Limits::SEARCH_LIMIT,
            'page' => $numberPage
        ]);

        $page = $paginator->getPaginate();

        $this->returnView('search', ['page' => $page, 'limit' => $paginator->getLimit()]);
    }

    /**
     * Searches for expert_info
     * @throws \ReflectionException
     */
    public function listAction()
    {
        $numberPage = $this->request->getQuery('page', 'int');

        /** @var Resultset $expert_info */
        $expert_info = ExpertInfo::find();
        if ($expert_info->count() === 0) {
            return $this->response->redirect('/admin/expert_info/index?notice=' . urlencode('The search did not find any expert_info'));
        }

        /** @var ExpertInfo $exp */
        foreach ($expert_info as $exp) {
            $this->afterFind($exp);

        }

        $paginator = new Paginator([
            'data' => $expert_info,
            'limit'=> Limits::SEARCH_LIMIT,
            'page' => $numberPage
        ]);

        $page = $paginator->getPaginate();

        $this->returnView('lists', ['page' => $page, 'limit' => $paginator->getLimit()]);
    }

    /**
     * Displays the creation form
     * @throws \ReflectionException
     */
    public function newAction()
    {
        $this->returnView('new');
    }

    /**
     * @param $id
     * @return null|Response
     * @throws \ReflectionException
     */
    public function editAction($id): ?Response
    {
        if ($this->request->isPost()) {
            return $this->response->redirect('/admin/expert_info/index');
        }


        $expert_info = ExpertInfo::findFirst((int)$id);
        if (!$expert_info) {
            return $this->response->redirect('/admin/expert_info/index?notice=' . urlencode('expert_info was not found'));
        }

        $this->view->id = $expert_info->getId();

        $this->tag::setDefault('id', $expert_info->getId());
        $this->tag::setDefault('user_id', $expert_info->getUserId());
        $this->tag::setDefault('skills', $expert_info->getSkills());
        $this->tag::setDefault('level', $expert_info->getLevel());

        $this->returnView('edit');
        return null;
    }

    /**
     * Creates a new expert_info
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            return $this->response->redirect('/admin/expert_info/index');
        }

        $expert_info = new ExpertInfo();
        $expert_info->setUserId($this->request->getPost('user_id'));
        $expert_info->setLevel($this->request->getPost('level'));
        $expert_info->setSkills($this->request->getPost('skills'));

        $this->transformModelBeforeSave($expert_info);

        if (!$expert_info->save()) {
            $mes = '';
            foreach ($expert_info->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/expert_info/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/expert_info/index?success=' . urlencode('expert_info was created successfully'));
    }

    /**
     * Saves a expert_info edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            return $this->response->redirect('/admin/expert_info/index');
        }

        $id = $this->request->getPost('id');
        $expert_info = ExpertInfo::findFirst((int)$id);

        if (!$expert_info) {
            return $this->response->redirect('/admin/expert_info/index?notice=' . urlencode('expert_info does not exist ' . $id));
        }

        $expert_info->setUserId($this->request->getPost('user_id'));
        $expert_info->setLevel($this->request->getPost('level'));
        $expert_info->setSkills($this->request->getPost('skills'));

        $this->transformModelBeforeSave($expert_info);

        if (!$expert_info->save()) {
            $mes = '';
            foreach ($expert_info->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/expert_info/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/expert_info/index?success=' . urlencode('expert_info was updated successfully'));
    }

    /**
     * @param $id
     * @return Response
     */
    public function deleteAction($id): Response
    {
        $expert_info = ExpertInfo::findFirst((int)$id);
        if (!$expert_info) {
            return $this->response->redirect('/admin/expert_info/index?notice=' . urlencode('expert_info was not found'));
        }

        if (!$expert_info->delete()) {
            $mes = '';
            foreach ($expert_info->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/expert_info/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/expert_info/index?success=' . urlencode('expert_info was deleted successfully'));
    }

    /*************** PROTECTED   *********************/

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
     *
     */
    protected function beforeHandle()
    {
        $this->messages = new Group();
    }

    /**
     * @param $data
     * @return mixed
     * @throws \RuntimeException
     */
    protected function onDataInvalid($data)
    {
        $mes = [];
        $mes['Post-data is invalid'];
        foreach ($this->messages as $message) {
            $mes[] = $message->getMessage();
        }

        return $this->createErrorResponse($mes);
    }
}
