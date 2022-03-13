<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Constants\AclRoles;
use App\Constants\Limits;
use App\Constants\Services;
use App\Forms\EducationForm;
use App\Services\UsersService;
use App\Traits\RenderView;
use App\Validators\EducationValidator;
use Phalcon\Http\Response;
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\View\Simple;
use Phalcon\Paginator\Adapter\Model as Paginator;
use App\Model\Education;
use Phalcon\Mvc\Model\Query\Builder as QueryBuilder;
use Phalcon\Validation\Message\Group;

/**
 * Class EducationController
 */
class EducationController extends ControllerBase
{
    use RenderView;

    public static $availableIncludes = [
        'Users'
    ];

    public static $encodedFields = [
        'name',
        'specialization',
        'organization'
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
     * Searches for education
     * @throws \ReflectionException
     */
    public function searchAction()
    {
        $numberPage = 1;
        $parameters = [];
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, Education::class, $_POST);
            $parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery('page', 'int');
        }

        $parameters['order'] = 'id';

        $educations = Education::find($parameters);
        if (\count($educations) === 0) {
            return $this->response->redirect('/admin/education/index?notice=' . urlencode('The search did not find any education'));
        }

        /** @var Education $education */
        foreach ($educations as $education) {
            $this->afterFind($education);

        }

        $paginator = new Paginator([
            'data' => $educations,
            'limit'=> Limits::SEARCH_LIMIT,
            'page' => $numberPage
        ]);

        $page = $paginator->getPaginate();

        $this->returnView('search', ['page' => $page, 'limit' => $paginator->getLimit()]);
    }

    /**
     * Searches for education
     * @throws \ReflectionException
     */
    public function listAction()
    {
        $numberPage = $this->request->getQuery('page', 'int');

        /** @var Resultset $educations */
        $educations = Education::find();
        if ($educations->count() === 0) {
            return $this->response->redirect('/admin/education/index?notice=' . urlencode('The search did not find any education'));
        }

        /** @var Education $education */
        foreach ($educations as $education) {
            $this->afterFind($education);

        }

        $paginator = new Paginator([
            'data' => $educations,
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
            return $this->response->redirect('/admin/education/index');
        }
        $education = Education::findFirst((int)$id);
        if (!$education) {
            return $this->response->redirect('/admin/education/index?notice=' . urlencode('education was not found'));
        }

        $this->view->id = $education->getId();

        $this->tag::setDefault('id', $education->getId());
        $this->tag::setDefault('user_id', $education->getUserId());
        $this->tag::setDefault('name', $education->getName());
        $this->tag::setDefault('specialization', $education->getSpecialization());
        $this->tag::setDefault('level', $education->getLevel());
        $this->tag::setDefault('start', $education->getStart());
        $this->tag::setDefault('finish', $education->getFinish());

        $this->returnView('edit');
        return null;
    }

    /**
     * Creates a new education
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            return $this->response->redirect('/admin/education/index');
        }

        $education = new Education();
        $education->setUserId($this->request->getPost('user_id'));
        $education->setName($this->request->getPost('name'));
        $education->setSpecialization($this->request->getPost('specialization'));
        $education->setLevel($this->request->getPost('level'));
        $education->setStart($this->request->getPost('start'));
        $education->setFinish($this->request->getPost('finish'));

        $this->transformModelBeforeSave($education);

        if (!$education->save()) {
            $mes = '';
            foreach ($education->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/education/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/education/index?success=' . urlencode('education was created successfully'));
    }

    /**
     * Saves a education edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            return $this->response->redirect('/admin/education/index');
        }

        $id = $this->request->getPost('id');
        $education = Education::findFirst((int)$id);

        if (!$education) {
            return $this->response->redirect('/admin/education/index?notice=' . urlencode('education does not exist ' . $id));
        }

        $education->setUserId($this->request->getPost('user_id'));
        $education->setName($this->request->getPost('name'));
        $education->setSpecialization($this->request->getPost('specialization'));
        $education->setLevel($this->request->getPost('level'));
        $education->setStart($this->request->getPost('start'));
        $education->setFinish($this->request->getPost('finish'));

        $this->transformModelBeforeSave($education);

        if (!$education->save()) {
            $mes = '';
            foreach ($education->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/education/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/education/index?success=' . urlencode('education was updated successfully'));
    }

    /**
     * @param $id
     * @return Response
     */
    public function deleteAction($id): Response
    {
        $education = Education::findFirst((int)$id);
        if (!$education) {
            return $this->response->redirect('/admin/education/index?notice=' . urlencode('education was not found'));
        }

        if (!$education->delete()) {
            $mes = '';
            foreach ($education->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/education/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/education/index?success=' . urlencode('education was deleted successfully'));
    }



    /************************************ SITE *************/


    /**
     * @param $id
     * @return \Phalcon\Http\Response | null | array
     * @throws \RuntimeException
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function showEducation($id)
    {
        $id = (int)$id;
        if (empty($id)) {
            return $this->createErrorResponse('Id is absent');
        }
        $edu = Education::find($id)[0];
        if (!$edu) {
            return $this->createErrorResponse('Education not found');
        }
        $this->afterFind($edu);

        /** @var UsersService $userService */
        $userService = $this->userService;
        $role = $userService->getRole();
        $options = [];
        if (\in_array($role, AclRoles::ADMIN_ROLES, true)) {
            $options['admin'] = true;
        }
        $options['show'] = true;
        $form = new EducationForm($edu, $options);

        $form->setFormId('education_form');


        $form->renderForm();
        $imageTag =  '';

        $config = $this->getDI()->get(Services::CONFIG);

        $view = new Simple();
        $template = $config->get('application')->viewsDir . '/education/education.phtml';

        $html = $view->render(
            $template,
            [
                'form' => $form,
                'image' => $imageTag,
                'messages' => [],
                'id' => $edu->getId()
            ]
        );
        return $this->createArrayResponse($html, 'html');

    }

    /**
     *
     * @throws \RuntimeException
     * @throws \ReflectionException
     * @throws \Exception
     * @return Response | null | array
     */
    public function addEducation()
    {
        $edu = new Education();
        $messages = [];

        $config = $this->getDI()->get(Services::CONFIG);

        $view = new Simple();

        $template = $config->get('application')->viewsDir . '/education/education.phtml';

        /** @var UsersService $userService */
        $userService = $this->userService;
        $role = $userService->getRole();

        $options = [];
        if (\in_array($role, [AclRoles::SUPERADMIN, AclRoles::ADMIN], true)) {
            $options = ['admin' => true];
        }

        if ($this->request->isPost()) {
            $params = $this->request->getPost();
            $image = null;


            $validator = new EducationValidator();
            $res = $validator->validate($params);
            if ($res->count() === 0) {
                $this->sanitizePostData($params);
                if ($edu->save($params)) {
                    return $this->createOkResponse();
                }
                $messages = $edu->getMessages();
            } else {
                $messages = $validator->getMessages();
            }
        }

        $form = new EducationForm($edu, $options);

        $form->setFormId('education_form');

        $form->renderForm();

        $imageTag =  '';

        $html = $view->render(
            $template,
            [
                'form' => $form,
                'image' => $imageTag,
                'messages' => $messages
            ]
        );
        return $this->createArrayResponse($html, 'html');
    }


    /**
     * @param $id
     * @return \Phalcon\Http\Response | null | array
     * @throws \RuntimeException
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function updateEducation($id)
    {
        $id = (int)$id;
        if (empty($id)) {
            return $this->createErrorResponse('Id is absent');
        }
        $edu = Education::find($id)[0];
        if (!$edu) {
            return $this->createErrorResponse('Education not found');
        }
        $messages = [];

        $this->afterFind($edu);

        $imageTag =  '';


        $config = $this->getDI()->get(Services::CONFIG);

        $view = new Simple();
        $template = $config->get('application')->viewsDir . '/education/education.phtml';

        /** @var UsersService $userService */
        $userService = $this->userService;
        $role = $userService->getRole();
        $options = [];
        if (\in_array($role, [AclRoles::SUPERADMIN, AclRoles::ADMIN], true)) {
            $options = ['admin' => true];
        }

        if ($this->request->isPost()) {
            $params = $this->request->getPost();

            $image = null;

            $validator = new EducationValidator();
            $res = $validator->validate($params);
            if ($res->count() === 0) {
                $this->sanitizePostData($params);
                if ($edu->save($params)) {
                    $form = new EducationForm($edu, $options);

                    $form->setFormId('education_form');
                    $form->setShow(false);

                    $form->renderForm();

                    $imageTag =  '';

                    $html = $view->render(
                        $template,
                        [
                            'form' => $form,
                            'image' => $imageTag,
                            'messages' => $messages,
                            'id' => $edu->getId()
                        ]
                    );
                    return $this->createArrayResponse($html, 'html');
                }
            } else {
                $messages = $validator->getMessages();
            }

            $edu->refresh();
        }


        $form = new EducationForm($edu, $options);

        $form->setFormId('education_form');

        $form->renderForm();



        $html = $view->render(
            $template,
            [
                'form' => $form,
                'image' => $imageTag,
                'messages' => $messages
            ]
        );
        return $this->createArrayResponse($html, 'html');

    }


    /**
     * @return mixed
     * @throws \PhalconApi\Exception
     */
    public function listUserEducation()
    {
        $numberPage = $this->request->getQuery('page', 'int');

        /** @var Resultset $edu */


        /** @var UsersService $userService */
        $userService = $this->userService;

        if (!$userService->getIdentity()) {
            return $this->createErrorResponse('Not authorized');
        }


        $role = $userService->getRole();
        if (\in_array($role, AclRoles::ADMIN_ROLES, true)) {
            $edu = Education::find();
        } else {
            $id = (int) $userService->getIdentity();
            $edu = Education::find('user_id = ' . $id);
        }

        if ($edu->count() === 0) {
            return $this->createErrorResponse('Not found');
        }

        /** @var Education $education */
        foreach ($edu as $education) {
            $this->afterFind($education);

        }

        $paginator = new Paginator([
            'data' => $edu,
            'limit'=> Limits::SEARCH_LIMIT,
            'page' => $numberPage
        ]);

        $page = $paginator->getPaginate();

        $items = $page->items;

        foreach ($items as &$item) {
            $item->name = html_entity_decode($item->name);
            $item->specialization = html_entity_decode($item->specialization);
            $item->organization = html_entity_decode($item->organization);
        }

        unset($item);

        $pagesInRange = $this->getPaginationRange($page);

        $data = [
            'educations'    => $items,
            'totalItems'    => $page->total_items,
            'totalPages'    => $page->total_pages,
            'limit'         => $page->limit,
            'current'       => $page->current,
            'before'        => $page->before,
            'next'          => $page->next,
            'last'          => $page->last,
            'first'         => $this->firstPage,
            'pagesRange'    => $pagesInRange,
            'bottomInRange' => $this->bottomInRange,
            'topInRange'    => $this->topInRange
        ];

        return $this->createArrayResponse($data, 'data');
    }


    /**
     * @param $id
     * @return Response
     * @throws \Exception
     */
    public function deleteEducation($id): Response
    {
        $edu = Education::findFirst((int)$id);

        $me = $this->userService->getDetails();

        if ($me) {
            return $this->createErrorResponse('Only for authorized users');
        }

        $user = $edu->getUsers();

        if ($me->getId() !== $user->getId() && !in_array($this->userService->getRole(), AclRoles::ADMIN_ROLES, true)) {
            return $this->createErrorResponse('You have no permission for this operation');
        }

        if (!$edu->delete()) {
            $mes = '';
            foreach ($edu->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->createErrorResponse($mes);
        }

        return $this->createOkResponse();
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
