<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Constants\Limits;
use App\Constants\Services;
use App\Interfaces\PaymentServerInterface;
use App\Traits\RenderView;
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Paginator\Adapter\Model as Paginator;
use App\Model\Payments;
use Phalcon\Mvc\Model\Query\Builder as QueryBuilder;
use Phalcon\Validation\Message\Group;

/**
 * Class PaymentsController
 * @package App\Controllers
 */
class PaymentsController extends ControllerBase
{
    use RenderView;

    public static $availableIncludes = [
        'CompanySubscriptions',
        'UserSubscriptions'
    ];

    public static $encodedFields = [
        'title',
        'description',
        'account',
        'swift',
        'bank',
        'currency'
    ];

    /**
     * @return mixed
     */
    public function setPayment()
    {
        if ($this->request->isPost()) {
            $params = $this->request->getPostedData();
            /** @var PaymentServerInterface $service */
            $service = $this->getDI()->get(Services::PAYMENT_SERVICE);
            if ($service->processPayment($params)) {
                return $this->createOkResponse();
            }
            return $this->createResponse(['error' => 'Payment not saved']);
        }
        return $this->createResponse(['error' => 'Wrong request method']);
    }

    /**
     * Index action
     * @throws \ReflectionException
     */
    public function indexAction()
    {
        $this->returnView('index');
    }

    /**
     * Searches for payments
     * @throws \ReflectionException
     */
    public function searchAction()
    {
        $numberPage = 1;
        $parameters = [];
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, Payments::class, $_POST);
            $parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery('page', 'int');
        }

        $parameters['order'] = 'id';

        $payments = Payments::find($parameters);
        if (\count($payments) === 0) {
            return $this->response->redirect('/admin/payments/index?notice=' . urlencode('The search did not find any payments'));
        }

        /** @var Payments $payment */
        foreach ($payments as $payment) {
            $this->afterFind($payment);

        }

        $paginator = new Paginator([
            'data' => $payments,
            'limit'=> Limits::SEARCH_LIMIT,
            'page' => $numberPage
        ]);

        $page = $paginator->getPaginate();

        $this->returnView('search', ['page' => $page, 'limit' => $paginator->getLimit()]);
    }


    /**
     * Searches for payments
     * @throws \ReflectionException
     */
    public function listAction()
    {
        $numberPage = $this->request->getQuery('page', 'int');

        /** @var Resultset $payments */
        $payments = Payments::find();
        if ($payments->count() === 0) {
            return $this->response->redirect('/admin/payments/index?notice=' . urlencode('The search did not find any payments'));
        }

        /** @var Payments $payment */
        foreach ($payments as $payment) {
            $this->afterFind($payment);

        }

        $paginator = new Paginator([
            'data' => $payments,
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
            return $this->response->redirect('/admin/payments/index');
        }
        $payment = Payments::findFirst((int)$id);
        if (!$payment) {
            return $this->response->redirect('/admin/payments/index?notice=' . urlencode('payment was not found'));
        }

        $this->view->id = $payment->getId();

        $this->tag::setDefault('id', $payment->getId());
        $this->tag::setDefault('title', $payment->getTitle());
        $this->tag::setDefault('amount', $payment->getAmount());
        $this->tag::setDefault('bank', $payment->getBank());
        $this->tag::setDefault('date', $payment->getDate());
        $this->tag::setDefault('user_subscription', $payment->getUserSubscription());
        $this->tag::setDefault('currency', $payment->getCurrency());

        $this->returnView('edit');
        return null;
    }

    /**
     * Creates a new payment
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            return $this->response->redirect('/admin/payments/index');
        }

        $payment = new Payments();
        $payment->setTitle($this->request->getPost('title'));
        $payment->setAmount($this->request->getPost('amount'));
        $payment->setBank($this->request->getPost('bank'));
        $payment->setDate($this->request->getPost('date'));
        $payment->setUserSubscription($this->request->getPost('user_subscription'));
        $payment->setCurrency($this->request->getPost('currency'));

        $this->transformModelBeforeSave($payment);

        if (!$payment->save()) {
            $mes = '';
            foreach ($payment->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/payments/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/payments/index?success=' . urlencode('payment was created successfully'));
    }

    /**
     * Saves a payment edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            return $this->response->redirect('/admin/payments/index');
        }

        $id = $this->request->getPost('id');
        $payment = Payments::findFirst((int)$id);

        if (!$payment) {
            return $this->response->redirect('/admin/payments/index?notice=' . urlencode('payment does not exist ' . $id));
        }

        $payment->setTitle($this->request->getPost('title'));
        $payment->setAmount($this->request->getPost('amount'));
        $payment->setBank($this->request->getPost('bank'));
        $payment->setDate($this->request->getPost('date'));
        $payment->setUserSubscription($this->request->getPost('user_subscription'));
        $payment->setCurrency($this->request->getPost('currency'));

        $this->transformModelBeforeSave($payment);

        if (!$payment->save()) {
            $mes = '';
            foreach ($payment->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/payments/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/payments/index?success=' . urlencode('payment was updated successfully'));
    }

    /**
     * @param $id
     * @return \Phalcon\Http\Response
     */
    public function deleteAction($id)
    {
        $payment = Payments::findFirst((int)$id);
        if (!$payment) {
            return $this->response->redirect('/admin/payments/index?notice=' . urlencode('payment was not found'));
        }

        if (!$payment->delete()) {
            $mes = '';
            foreach ($payment->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/payments/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/payments/index?success=' . urlencode('payment was deleted successfully'));
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
