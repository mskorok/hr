<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Constants\Limits;
use App\Traits\RenderView;
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Paginator\Adapter\Model as Paginator;
use App\Model\Deals;

/**
 * Class DealsController
 * @package App\Controllers
 */
class DealsController extends ControllerBase
{
    use RenderView;

    public static $availableIncludes = [
        'Partners',
        'Users',
        'Vacancies'
    ];

    public static $encodedFields = [
        'description'
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
     * Searches for deals
     * @throws \ReflectionException
     */
    public function searchAction()
    {
        $numberPage = 1;
        $parameters = [];
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, Deals::class, $_POST);
            $parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery('page', 'int');
        }


        $parameters['order'] = 'id';

        $deals = Deals::find($parameters);
        if (\count($deals) === 0) {
            return $this->response->redirect('/admin/deals/index?notice=' . urlencode('The search did not find any deals'));
        }

        $paginator = new Paginator([
            'data' => $deals,
            'limit'=> Limits::SEARCH_LIMIT,
            'page' => $numberPage
        ]);

        $page = $paginator->getPaginate();

        $this->returnView('search', ['page' => $page, 'limit' => $paginator->getLimit()]);
    }


    /**
     * Searches for deals
     * @throws \ReflectionException
     */
    public function listAction()
    {
        $numberPage = $this->request->getQuery('page', 'int');

        /** @var Resultset $deals */
        $deals = Deals::find();
        if ($deals->count() === 0) {
            return $this->response->redirect('/admin/deals/index?notice=' . urlencode('The search did not find any deals'));
        }

        $paginator = new Paginator([
            'data' => $deals,
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
            return $this->response->redirect('/admin/deals/index');
        }
        $deal = Deals::findFirst((int)$id);
        if (!$deal) {
            return $this->response->redirect('/admin/deals/index?notice=' . urlencode('deal was not found'));
        }

        $this->view->id = $deal->getId();

        $this->tag::setDefault('id', $deal->getId());
        $this->tag::setDefault('partner_id', $deal->getPartnerId());
        $this->tag::setDefault('user_id', $deal->getUserId());
        $this->tag::setDefault('vacancy_id', $deal->getVacancyId());
        $this->tag::setDefault('created', $deal->getCreated());
        $this->tag::setDefault('success', $deal->getSuccess());
        $this->tag::setDefault('description', $deal->getDescription());

        $this->returnView('edit');
        return null;
    }

    /**
     * Creates a new deal
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            return $this->response->redirect('/admin/deals/index');
        }

        $deal = new Deals();
        $deal->setId((int)$this->request->getPost('id'));
        $deal->setPartnerId((int) $this->request->getPost('partner_id'));
        $deal->setUserId((int) $this->request->getPost('user_id'));
        $deal->setVacancyId((int) $this->request->getPost('vacancy_id'));
        $deal->setSuccess((int) $this->request->getPost('success'));
        $deal->setDescription($this->request->getPost('description'));
        

        if (!$deal->save()) {
            $mes = '';
            foreach ($deal->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/deals/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/deals/index?success=' . urlencode('deal was created successfully'));
    }

    /**
     * Saves a deal edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            return $this->response->redirect('/admin/deals/index');
        }

        $id = $this->request->getPost('id');
        $deal = Deals::findFirst((int)$id);

        if (!$deal) {
            return $this->response->redirect('/admin/deals/index?notice=' . urlencode('deal does not exist ' . $id));
        }

        $deal->setId((int)$this->request->getPost('id'));
        $deal->setPartnerId((int) $this->request->getPost('partner_id'));
        $deal->setUserId((int) $this->request->getPost('user_id'));
        $deal->setVacancyId((int) $this->request->getPost('vacancy_id'));
        $deal->setSuccess((int) $this->request->getPost('success'));
        $deal->setDescription($this->request->getPost('description'));
        

        if (!$deal->save()) {
            $mes = '';
            foreach ($deal->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/deals/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/deals/index?success=' . urlencode('deal was updated successfully'));
    }

    /**
     * @param $id
     * @return \Phalcon\Http\Response
     */
    public function deleteAction($id)
    {
        $deal = Deals::findFirst((int)$id);
        if (!$deal) {
            return $this->response->redirect('/admin/deals/index?notice=' . urlencode('deal was not found'));
        }

        if (!$deal->delete()) {
            $mes = '';
            foreach ($deal->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/deals/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/deals/index?success=' . urlencode('deal was deleted successfully'));
    }

}
