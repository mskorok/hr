<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Constants\Limits;
use App\Forms\PartnerInfoForm;
use App\Traits\RenderView;
use App\Validators\PartnerInfoValidator;
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Paginator\Adapter\Model as Paginator;
use App\Model\PartnerInfo;
use Phalcon\Http\Response;

/**
 * Class PartnersController
 * @package App\Controllers
 */
class PartnerInfoController extends ControllerBase
{
    use RenderView;

    public static $availableIncludes = [
        'Countries',
        'Deals',
        'Manager',
        'Managers',
        'Payments'
    ];

    public static $encodedFields = [
        'info'
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
     * Searches for partners
     * @throws \ReflectionException
     */
    public function searchAction()
    {
        $numberPage = 1;
        $parameters = [];
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, __CLASS__, $_POST);
            $parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery('page', 'int');
        }


        $parameters['order'] = 'id';

        $partners = PartnerInfo::find($parameters);
        if (\count($partners) === 0) {
            return $this->response->redirect('/admin/partners/index?notice=' . urlencode('The search did not find any partners'));
        }

        /** @var PartnerInfo $user */
        foreach ($partners as $user) {
            $this->afterFind($user);

        }

        $paginator = new Paginator([
            'data' => $partners,
            'limit'=> Limits::SEARCH_LIMIT,
            'page' => $numberPage
        ]);

        $page = $paginator->getPaginate();

        $this->returnView('search', ['page' => $page, 'limit' => $paginator->getLimit()]);
    }


    /**
     * Searches for partners
     * @throws \ReflectionException
     */
    public function listAction()
    {
        $numberPage = $this->request->getQuery('page', 'int');

        /** @var Resultset $partners */
        $partners = PartnerInfo::find();
        if ($partners->count() === 0) {
            return $this->response->redirect('/admin/partners/index?notice=' . urlencode('The search did not find any partners'));
        }

        /** @var PartnerInfo $user */
        foreach ($partners as $user) {
            $this->afterFind($user);

        }

        $paginator = new Paginator([
            'data' => $partners,
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
            return $this->response->redirect('/admin/partners/index');
        }

        $partner = PartnerInfo::findFirst((int)$id);
        if (!$partner) {
            return $this->response->redirect('/admin/partners/index?notice=' . urlencode('partner was not found'));
        }

        $this->view->id = $partner->getId();

        $this->tag::setDefault('id', $partner->getId());
        $this->tag::setDefault('info', $partner->getInfo());
        $this->tag::setDefault('company_id', $partner->getCompanyId());
        $this->tag::setDefault('approved', $partner->getApproved());
        $this->tag::setDefault('level', $partner->getLevel());

        $this->returnView('edit');
        return null;
    }

    /**
     * Creates a new partner
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            return $this->response->redirect('/admin/partners/index');
        }

        $partner = new PartnerInfo();
        $partner->setInfo($this->request->getPost('info'));
        $partner->setCompanyId($this->request->getPost('company_id'));
        $partner->setApproved((int)$this->request->getPost('approved'));
        $partner->setLevel($this->request->getPost('level', null, 'middle'));

        $this->transformModelBeforeSave($partner);

        if (!$partner->save()) {
            $mes = '';
            foreach ($partner->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/partners/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/partners/index?success=' . urlencode('partner was created successfully'));
    }

    /**
     * Saves a partner edited
     *
     */
    public function saveAction()
    {
        if (!$this->request->isPost()) {
            return $this->response->redirect('/admin/partners/index');
        }

        $id = (int)$this->request->getPost('id');
        $partner = PartnerInfo::findFirst($id);

        if (!$partner) {
            return $this->response->redirect('/admin/partners/index?notice=' . urlencode('partner does not exist ' . $id));
        }

        $partner->setInfo($this->request->getPost('info'));
        $partner->setCompanyId($this->request->getPost('company_id'));
        $partner->setApproved((int)$this->request->getPost('approved'));
        $partner->setLevel($this->request->getPost('level', null, 'middle'));

        $this->transformModelBeforeSave($partner);

        if (!$partner->save()) {
            $mes = '';
            foreach ($partner->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/partners/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/partners/index?success=' . urlencode('partner was updated successfully'));
    }

    /**
     * @param $id
     * @return Response
     */
    public function deleteAction($id)
    {
        $partner = PartnerInfo::findFirst((int)$id);
        if (!$partner) {
            return $this->response->redirect('/admin/partners/index?notice=' . urlencode('partner was not found'));
        }

        if (!$partner->delete()) {
            $mes = '';
            foreach ($partner->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/partners/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/partners/index?success=' . urlencode('partner was deleted successfully'));
    }

    /**************************  TEST *********************/

    /**
     *
     * @throws \ReflectionException
     */
    public function listPartners()
    {
        $this->returnView('list');
    }

    /**
     *
     * @throws \RuntimeException
     * @throws \ReflectionException
     * @throws \Exception
     * @return Response | null
     */
    public function add(): ?Response
    {
        $partner = new PartnerInfo();
        $messages = [];
        if ($this->request->isPost()) {
            $partner = new PartnerInfo();
            $params = $this->request->getPost();


            $validator = new PartnerInfoValidator();
            $res = $validator->validate($params);
            if ($res->count() === 0) {
                if ($partner->save($params)) {
                    return $this->response->redirect('/admin/partner/edit/' . $partner->getId());
                }
                $messages = $partner->getMessages();
            } else {
                $messages = $validator->getMessages();
            }
        }

        $form = new PartnerInfoForm($partner);
        $form->renderForm();
        $this->returnView('add', compact('form', 'messages'));
        return null;
    }


    /**
     * @param $id
     * @return \Phalcon\Http\Response | null
     * @throws \RuntimeException
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function updates($id): ?Response
    {
        $id = (int)$id;
        if (empty($id)) {
            return $this->response->redirect('/admin/partner/list');
        }
        $partner = PartnerInfo::find($id)[0];
        if (!$partner) {
            return $this->response->redirect('/admin/partner/list');
        }
        $messages = [];

        if ($this->request->isPost()) {
            $params = $this->request->getPost();
            $validator = new PartnerInfoValidator();
            $res = $validator->validate($params);
            if ($res->count() === 0) {
                $this->sanitizePostData($params);
                /** @var PartnerInfo $partner */
                $partner->save($params);
            } else {
                $messages = $validator->getMessages();
            }

            $partner->refresh();
        }

        $form = new PartnerInfoForm($partner);


        $form->renderForm();


        $this->returnView(
            'updates',
            [
                'form' => $form,
                'messages' => $messages,
                'id' => $partner->getId()
            ]
        );
        return null;
    }

}
