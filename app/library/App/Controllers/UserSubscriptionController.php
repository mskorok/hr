<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Constants\Limits;
use App\Traits\RenderView;
use Phalcon\Http\Response;
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Paginator\Adapter\Model as Paginator;
use App\Model\UserSubscription;
use Phalcon\Mvc\Model\Query\Builder as QueryBuilder;
use Phalcon\Validation\Message\Group;

/**
 * Class UserSubscriptionController
 * @package App\Controllers
 */
class UserSubscriptionController extends ControllerBase
{
    use RenderView;

    public static $availableIncludes = [
        'Users',
        'Payments',
        'Subscriptions'
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
     * Searches for user_subscription
     * @throws \ReflectionException
     */
    public function searchAction()
    {
        $numberPage = 1;
        $parameters = [];
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, UserSubscription::class, $_POST);
            $parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery('page', 'int');
        }

        $parameters['order'] = 'id';

        $user_subscription = UserSubscription::find($parameters);
        if (\count($user_subscription) === 0) {
            return $this->response->redirect('/admin/user_subscription/index?notice=' . urlencode('The search did not find any user_subscription'));
        }

        /** @var UserSubscription $user */
        foreach ($user_subscription as $user) {
            $this->afterFind($user);

        }

        $paginator = new Paginator([
            'data' => $user_subscription,
            'limit'=> Limits::SEARCH_LIMIT,
            'page' => $numberPage
        ]);

        $page = $paginator->getPaginate();

        $this->returnView('search', ['page' => $page, 'limit' => $paginator->getLimit()]);
    }

    /**
     * Searches for user_subscription
     * @throws \ReflectionException
     */
    public function listAction()
    {
        $numberPage = $this->request->getQuery('page', 'int');

        /** @var Resultset $user_subscription */
        $user_subscription = UserSubscription::find();
        if ($user_subscription->count() === 0) {
            return $this->response->redirect('/admin/user_subscription/index?notice=' . urlencode('The search did not find any user_subscription'));
        }

        /** @var UserSubscription $user */
        foreach ($user_subscription as $user) {
            $this->afterFind($user);

        }

        $paginator = new Paginator([
            'data' => $user_subscription,
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
            return $this->response->redirect('/admin/user_subscription/index');
        }


        $user_subscription = UserSubscription::findFirst((int)$id);
        if (!$user_subscription) {
            return $this->response->redirect('/admin/user_subscription/index?notice=' . urlencode('user_subscription was not found'));
        }

        $this->view->id = $user_subscription->getId();

        $this->tag::setDefault('id', $user_subscription->getId());
        $this->tag::setDefault('user_id', $user_subscription->getUserId());
        $this->tag::setDefault('subscription_id', $user_subscription->getSubscriptionId());

        $this->returnView('edit');
        return null;
    }

    /**
     * Creates a new user_subscription
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            return $this->response->redirect('/admin/user_subscription/index');
        }

        $user_subscription = new UserSubscription();
        $user_subscription->setUserId($this->request->getPost('user_id'));
        $user_subscription->setSubscriptionId($this->request->getPost('subscription_id'));

        $this->transformModelBeforeSave($user_subscription);

        if (!$user_subscription->save()) {
            $mes = '';
            foreach ($user_subscription->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/user_subscription/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/user_subscription/index?success=' . urlencode('user_subscription was created successfully'));
    }

    /**
     * Saves a user_subscription edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            return $this->response->redirect('/admin/user_subscription/index');
        }

        $id = $this->request->getPost('id');
        $user_subscription = UserSubscription::findFirst((int)$id);

        if (!$user_subscription) {
            return $this->response->redirect('/admin/user_subscription/index?notice=' . urlencode('user_subscription does not exist ' . $id));
        }

        $user_subscription->setUserId($this->request->getPost('user_id'));
        $user_subscription->setSubscriptionId($this->request->getPost('subscription_id'));

        $this->transformModelBeforeSave($user_subscription);

        if (!$user_subscription->save()) {
            $mes = '';
            foreach ($user_subscription->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/user_subscription/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/user_subscription/index?success=' . urlencode('user_subscription was updated successfully'));
    }

    /**
     * @param $id
     * @return Response
     */
    public function deleteAction($id): Response
    {
        $user_subscription = UserSubscription::findFirst((int)$id);
        if (!$user_subscription) {
            return $this->response->redirect('/admin/user_subscription/index?notice=' . urlencode('user_subscription was not found'));
        }

        if (!$user_subscription->delete()) {
            $mes = '';
            foreach ($user_subscription->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/user_subscription/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/user_subscription/index?success=' . urlencode('user_subscription was deleted successfully'));
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
