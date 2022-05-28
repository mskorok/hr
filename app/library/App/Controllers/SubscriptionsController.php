<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Constants\Limits;
use App\Traits\RenderView;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Paginator\Adapter\Model as Paginator;
use App\Model\Subscriptions;
use Phalcon\Mvc\Model\Query\Builder as QueryBuilder;
use Phalcon\Validation\Message\Group;

/**
 * Class SubscriptionsController
 * @package App\Controllers
 */
class SubscriptionsController extends ControllerBase
{
    use RenderView;

    public static $availableIncludes = [
        'CompanySubscription',
        'Companies',
        'UserSubscription',
        'Users'
    ];

    public static $encodedFields = [
        'title',
        'description'
    ];

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->returnView('index');
    }

    /**
     * Searches for subscriptions
     */
    public function searchAction()
    {
        $numberPage = 1;
        $parameters = [];
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, Subscriptions::class, $_POST);
            $parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery('page', 'int');
        }


        $parameters['order'] = 'id';

        $subscriptions = Subscriptions::find($parameters);
        if (\count($subscriptions) === 0) {
            return $this->response->redirect('/admin/subscriptions/index?notice=' . urlencode('The search did not find any subscriptions'));
        }

        /** @var Subscriptions $sub */
        foreach ($subscriptions as $sub) {
            $this->afterFind($sub);

        }

        $paginator = new Paginator([
            'data' => $subscriptions,
            'limit'=> Limits::SEARCH_LIMIT,
            'page' => $numberPage
        ]);

        $page = $paginator->getPaginate();

        $this->returnView('search', ['page' => $page, 'limit' => $paginator->getLimit()]);
    }

    /**
     * Searches for subscriptions
     */
    public function listAction()
    {
        $numberPage = $this->request->getQuery('page', 'int');

        /** @var Resultset $subscriptions */
        $subscriptions = Subscriptions::find();
        if ($subscriptions->count() === 0) {
            return $this->response->redirect('/admin/subscriptions/index?notice=' . urlencode('The search did not find any subscriptions'));
        }

        /** @var Subscriptions $sub */
        foreach ($subscriptions as $sub) {
            $this->afterFind($sub);

        }

        $paginator = new Paginator([
            'data' => $subscriptions,
            'limit'=> Limits::SEARCH_LIMIT,
            'page' => $numberPage
        ]);

        $page = $paginator->getPaginate();

        $this->returnView('lists', ['page' => $page, 'limit' => $paginator->getLimit()]);
    }

    /**
     * Displays the creation form
     */
    public function newAction()
    {
        $this->returnView('new');
    }

    /**
     * @param $id
     * @return ResponseInterface|null
     */
    public function editAction($id): ?ResponseInterface
    {
        if ($this->request->isPost()) {
            return $this->response->redirect('/admin/subscriptions/index');
        }
        $subscription = Subscriptions::findFirst((int)$id);
        if (!$subscription) {
            return $this->response->redirect('/admin/subscriptions/index?notice=' . urlencode('subscription was not found'));
        }

        $this->view->id = $subscription->getId();

        $this->tag::setDefault('id', $subscription->getId());
        $this->tag::setDefault('title', $subscription->getTitle());
        $this->tag::setDefault('description', $subscription->getDescription());

        $this->returnView('edit');
        return null;
    }

    /**
     * Creates a new subscription
     */
    public function createAction(): ResponseInterface
    {
        if (!$this->request->isPost()) {
            return $this->response->redirect('/admin/subscriptions/index');
        }

        $subscription = new Subscriptions();
        $subscription->setTitle($this->request->getPost('title'));
        $subscription->setDescription($this->request->getPost('description'));

        $this->transformModelBeforeSave($subscription);

        if (!$subscription->save()) {
            $mes = implode('', $subscription->getMessages());

            return $this->response->redirect('/admin/subscriptions/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/subscriptions/index?success=' . urlencode('subscription was created successfully'));
    }

    /**
     * Saves a subscription edited
     *
     */
    public function saveAction(): ResponseInterface
    {

        if (!$this->request->isPost()) {
            return $this->response->redirect('/admin/subscriptions/index');
        }

        $id = $this->request->getPost('id');
        $subscription = Subscriptions::findFirst((int)$id);

        if (!$subscription) {
            return $this->response->redirect('/admin/subscriptions/index?notice=' . urlencode('subscription does not exist ' . $id));
        }

        $subscription->setTitle($this->request->getPost('title'));
        $subscription->setDescription($this->request->getPost('description'));

        $this->transformModelBeforeSave($subscription);

        if (!$subscription->save()) {
            $mes = implode('', $subscription->getMessages());

            return $this->response->redirect('/admin/subscriptions/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/subscriptions/index?success=' . urlencode('subscription was updated successfully'));
    }

    /**
     * @param $id
     * @return ResponseInterface
     */
    public function deleteAction($id): ResponseInterface
    {
        $subscription = Subscriptions::findFirst((int)$id);
        if (!$subscription) {
            return $this->response->redirect('/admin/subscriptions/index?notice=' . urlencode('subscription was not found'));
        }

        if (!$subscription->delete()) {
            $mes = implode('', $subscription->getMessages());

            return $this->response->redirect('/admin/subscriptions/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/subscriptions/index?success=' . urlencode('subscription was deleted successfully'));
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
        foreach ($this->messages as $message) {
            $mes[] = $message->getMessage();
        }

        return $this->createErrorResponse($mes);
    }
}
