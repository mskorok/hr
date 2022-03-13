<?php
declare(strict_types=1);

namespace App\Controllers;
 
use App\Constants\Limits;
use App\Model\CompanyManager;
use App\Traits\RenderView;
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Paginator\Adapter\Model as Paginator;
use Phalcon\Mvc\Model\Query\Builder as QueryBuilder;
use Phalcon\Validation\Message\Group;

/**
 * Class CompanyManagerController
 * @package App\Controllers
 */
class CompanyManagerController extends ControllerBase
{
    use RenderView;

    public static $availableIncludes = [
        'Companies',
        'Users'
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
     * Searches for company_manager
     * @throws \ReflectionException
     */
    public function searchAction()
    {
        $numberPage = 1;
        $parameters = [];
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, CompanyManager::class, $_POST);
            $parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery('page', 'int');
        }
        
        $parameters['order'] = 'id';

        $company_manager = CompanyManager::find($parameters);
        if (\count($company_manager) === 0) {
            return $this->response->redirect('/admin/company_manager/index?notice=' . urlencode('The search did not find any company_manager'));
        }

        $paginator = new Paginator([
            'data' => $company_manager,
            'limit'=> Limits::SEARCH_LIMIT,
            'page' => $numberPage
        ]);

        $page = $paginator->getPaginate();

        $this->returnView('search', ['page' => $page, 'limit' => $paginator->getLimit()]);
    }

    /**
     * Searches for company_manager
     * @throws \ReflectionException
     */
    public function listAction()
    {
        $numberPage = $this->request->getQuery('page', 'int');

        /** @var Resultset $company_manager */
        $company_manager = CompanyManager::find();
        if ($company_manager->count() === 0) {
            return $this->response->redirect('/admin/company_manager/index?notice=' . urlencode('The search did not find any company_manager'));
        }

        $paginator = new Paginator([
            'data' => $company_manager,
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
     * @return null|\Phalcon\Http\Response
     * @throws \ReflectionException
     */
    public function editAction($id)
    {
        if ($this->request->isPost()) {
            return $this->response->redirect('/admin/company_manager/index');
        }

        $company_manager = CompanyManager::findFirst((int)$id);
        if (!$company_manager) {
            return $this->response->redirect('/admin/company_manager/index?notice=' . urlencode('company_manager was not found'));
        }

        $this->view->id = $company_manager->getId();

        $this->tag::setDefault('id', $company_manager->getId());
        $this->tag::setDefault('company_id', $company_manager->getCompanyId());
        $this->tag::setDefault('user_id', $company_manager->getUserId());

        $this->returnView('edit');
        return null;
    }

    /**
     * Creates a new company_manager
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            return $this->response->redirect('/admin/company_manager/index');
        }

        $company_manager = new CompanyManager();
        $company_manager->setCompanyId($this->request->getPost('company_id'));
        $company_manager->setUserId($this->request->getPost('user_id'));
        

        if (!$company_manager->save()) {
            $mes = '';
            foreach ($company_manager->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/company_manager/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/company_manager/index?success=' . urlencode('company_manager was created successfully'));
    }

    /**
     * Saves a company_manager edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            return $this->response->redirect('/admin/company_manager/index');
        }

        $id = $this->request->getPost('id');
        $company_manager = CompanyManager::findFirst((int)$id);

        if (!$company_manager) {
            return $this->response->redirect('/admin/company_manager/index?notice=' . urlencode('company_manager does not exist ' . $id));
        }

        $company_manager->setCompanyId($this->request->getPost('company_id'));
        $company_manager->setUserId($this->request->getPost('user_id'));
        

        if (!$company_manager->save()) {
            $mes = '';
            foreach ($company_manager->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/company_manager/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/company_manager/index?success=' . urlencode('company_manager was updated successfully'));
    }

    /**
     * @param $id
     * @return \Phalcon\Http\Response
     */
    public function deleteAction($id)
    {
        $company_manager = CompanyManager::findFirst((int)$id);
        if (!$company_manager) {
            return $this->response->redirect('/admin/company_manager/index?notice=' . urlencode('company_manager was not found'));
        }

        if (!$company_manager->delete()) {
            $mes = '';
            foreach ($company_manager->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/company_manager/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/company_manager/index?success=' . urlencode('company_manager was deleted successfully'));
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
