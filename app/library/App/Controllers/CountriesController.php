<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Constants\Limits;
use App\Traits\RenderView;
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Paginator\Adapter\Model as Paginator;
use App\Model\Countries;
use Phalcon\Mvc\Model\Query\Builder as QueryBuilder;
use Phalcon\Validation\Message\Group;

/**
 * Class CountriesController
 * @package App\Controllers
 */
class CountriesController extends ControllerBase
{
    use RenderView;

    public static $availableIncludes = [
        'ArticleImages',
        'Images'
    ];

    public static $encodedFields = [
        'name',
        'short_name'
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
     * Searches for countries
     * @throws \ReflectionException
     */
    public function searchAction()
    {
        $numberPage = 1;
        $parameters = [];
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, Countries::class, $_POST);
            $parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery('page', 'int');
        }


        $parameters['order'] = 'id';

        $countries = Countries::find($parameters);
        if (\count($countries) === 0) {
            return $this->response->redirect('/admin/countries/index?notice=' . urlencode('The search did not find any countries'));
        }

        /** @var Countries $country */
        foreach ($countries as $country) {
            $this->afterFind($country);

        }

        $paginator = new Paginator([
            'data' => $countries,
            'limit'=> Limits::SEARCH_LIMIT,
            'page' => $numberPage
        ]);

        $page = $paginator->getPaginate();

        $this->returnView('search', ['page' => $page, 'limit' => $paginator->getLimit()]);
    }


    /**
     * Searches for countries
     * @throws \ReflectionException
     */
    public function listAction()
    {
        $numberPage = $this->request->getQuery('page', 'int');

        /** @var Resultset $countries */
        $countries = Countries::find();
        if ($countries->count() === 0) {
            return $this->response->redirect('/admin/countries/index?notice=' . urlencode('The search did not find any countries'));
        }

        /** @var Countries $country */
        foreach ($countries as $country) {
            $this->afterFind($country);

        }

        $paginator = new Paginator([
            'data' => $countries,
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
            return $this->response->redirect('/admin/countries/index');
        }
        $country = Countries::findFirst((int)$id);
        if (!$country) {
            return $this->response->redirect('/admin/countries/index?notice=' . urlencode('country was not found'));
        }

        $this->view->id = $country->getId();

        $this->tag::setDefault('id', $country->getId());
        $this->tag::setDefault('name', $country->getName());
        $this->tag::setDefault('short_name', $country->getShortName());

        $this->returnView('edit');
        return null;
    }

    /**
     * Creates a new countrie
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            return $this->response->redirect('/admin/countries/index');
        }

        $country = new Countries();
        $country->setName($this->request->getPost('name'));
        $country->setShortName($this->request->getPost('short_name'));

        $this->transformModelBeforeSave($country);

        if (!$country->save()) {
            $mes = '';
            foreach ($country->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/countries/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/countries/index?success=' . urlencode('country was created successfully'));
    }

    /**
     * Saves a country edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            return $this->response->redirect('/admin/countries/index');
        }

        $id = $this->request->getPost('id');
        $country = Countries::findFirst((int)$id);

        if (!$country) {
            return $this->response->redirect('/admin/countries/index?notice=' . urlencode('country does not exist ' . $id));
        }

        $country->setName($this->request->getPost('name'));
        $country->setShortName($this->request->getPost('short_name'));
        $this->transformModelBeforeSave($country);

        if (!$country->save()) {
            $mes = '';
            foreach ($country->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/countries/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/countries/index?success=' . urlencode('country was updated successfully'));
    }

    /**
     * Deletes a countrie
     *
     * @param mixed $id
     * @return \Phalcon\Http\Response
     */
    public function deleteAction($id)
    {
        $country = Countries::findFirst((int)$id);
        if (!$country) {
            return $this->response->redirect('/admin/countries/index?notice=' . urlencode('country was not found'));
        }

        if (!$country->delete()) {
            $mes = '';
            foreach ($country->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/countries/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/countries/index?success=' . urlencode('country was deleted successfully'));
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
