<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Constants\Limits;
use App\Model\Skills;
use App\Traits\RenderView;
use Phalcon\Http\Response;
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Paginator\Adapter\Model as Paginator;
use Phalcon\Mvc\Model\Query\Builder as QueryBuilder;
use Phalcon\Validation\Message\Group;

/**
 * Class SkillsController
 * @package App\Controllers
 */
class SkillsController extends ControllerBase
{
    use RenderView;

    public static $encodedFields = [
        'title'
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
     * Searches for skills
     * @throws \ReflectionException
     */
    public function searchAction()
    {
        $numberPage = 1;
        $parameters = [];
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, Skills::class, $_POST);
            $parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery('page', 'int');
        }


        $parameters['order'] = 'id';

        $skills = Skills::find($parameters);
        if (\count($skills) === 0) {
            return $this->response->redirect('/admin/skills/index?notice=' . urlencode('The search did not find any skills'));
        }

        /** @var Skills $skill */
        foreach ($skills as $skill) {
            $this->afterFind($skill);

        }

        $paginator = new Paginator([
            'data' => $skills,
            'limit' => Limits::SEARCH_LIMIT,
            'page' => $numberPage
        ]);

        $page = $paginator->getPaginate();

        $this->returnView('search', ['page' => $page, 'limit' => $paginator->getLimit()]);
    }

    /**
     * Searches for skills
     * @throws \ReflectionException
     */
    public function listAction()
    {
        $numberPage = $this->request->getQuery('page', 'int');

        /** @var Resultset $skills */
        $skills = Skills::find();
        if ($skills->count() === 0) {
            return $this->response->redirect('/admin/skills/index?notice=' . urlencode('The search did not find any skills'));
        }

        /** @var Skills $skill */
        foreach ($skills as $skill) {
            $this->afterFind($skill);

        }

        $paginator = new Paginator([
            'data' => $skills,
            'limit' => Limits::SEARCH_LIMIT,
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
            return $this->response->redirect('/admin/skills/index');
        }
        $skill = Skills::findFirst((int)$id);
        if (!$skill) {
            return $this->response->redirect('/admin/skills/index?notice=' . urlencode('skill was not found'));
        }

        $this->afterFind($skill);


        $this->view->id = $skill->getId();

        $this->tag::setDefault('id', $skill->getId());
        $this->tag::setDefault('title', $skill->getTitle());

        $this->returnView('edit');
        return null;
    }

    /**
     * Creates a new skill
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            return $this->response->redirect('/admin/skills/index');
        }

        $skill = new Skills();
        $skill->setTitle($this->request->getPost('title'));

        $this->transformModelBeforeSave($skill);

        if (!$skill->save()) {
            $mes = '';
            foreach ($skill->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/skills/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/skills/index?success=' . urlencode('skill was created successfully'));
    }

    /**
     * Saves a skill edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            return $this->response->redirect('/admin/skills/index');
        }

        $id = $this->request->getPost('id');
        $skill = Skills::findFirst((int)$id);

        if (!$skill) {
            return $this->response->redirect('/admin/skills/index?notice=' . urlencode('skill does not exist ' . $id));
        }

        $skill->setTitle($this->request->getPost('title'));

        $this->transformModelBeforeSave($skill);

        if (!$skill->save()) {
            $mes = '';
            foreach ($skill->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/skills/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/skills/index?success=' . urlencode('skill was updated successfully'));
    }

    /**
     * @param $id
     * @return Response
     */
    public function deleteAction($id): Response
    {
        $skill = Skills::findFirst((int)$id);
        if (!$skill) {
            return $this->response->redirect('/admin/skills/index?notice=' . urlencode('skill was not found'));
        }

        if (!$skill->delete()) {
            $mes = '';
            foreach ($skill->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/skills/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/skills/index?success=' . urlencode('skill was deleted successfully'));
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
