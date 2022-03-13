<?php
declare(strict_types=1);

namespace App\Controllers;
 
use App\Constants\AclRoles;
use App\Constants\Limits;
use App\Constants\Services;
use App\Forms\ProfessionExperienceForm;
use App\Services\UsersService;
use App\Traits\RenderView;
use App\Validators\ProfessionalExperienceValidator;
use Phalcon\Http\Response;
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\View\Simple;
use Phalcon\Paginator\Adapter\Model as Paginator;
use App\Model\ProfessionalExperience;
use Phalcon\Mvc\Model\Query\Builder as QueryBuilder;
use Phalcon\Validation\Message\Group;

/**
 * Class ProfessionalExperienceController
 * @package App\Controllers
 */
class ProfessionalExperienceController extends ControllerBase
{
    use RenderView;

    public static $availableIncludes = [
        'Users'
    ];

    public static $encodedFields = [
        'organization',
        'title',
        'description',
        'location',
        'site',
        'professional_area',
        'position'
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
     * Searches for professional_experience
     * @throws \ReflectionException
     */
    public function searchAction()
    {
        $numberPage = 1;
        $parameters = [];
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, ProfessionalExperience::class, $_POST);
            $parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery('page', 'int');
        }

        
        $parameters['order'] = 'id';

        $professional_experience = ProfessionalExperience::find($parameters);
        if (\count($professional_experience) === 0) {
            return $this->response->redirect('/admin/professional_experience/index?notice=' . urlencode('The search did not find any professional_experience'));
        }

        /** @var ProfessionalExperience $pro */
        foreach ($professional_experience as $pro) {
            $this->afterFind($pro);

        }

        $paginator = new Paginator([
            'data' => $professional_experience,
            'limit'=> Limits::SEARCH_LIMIT,
            'page' => $numberPage
        ]);

        $page = $paginator->getPaginate();

        $this->returnView('search', ['page' => $page, 'limit' => $paginator->getLimit()]);
    }

    /**
     * Searches for professional_experience
     * @throws \ReflectionException
     */
    public function listAction()
    {
        $numberPage = $this->request->getQuery('page', 'int');

        /** @var Resultset $professional_experience */
        $professional_experience = ProfessionalExperience::find();
        if ($professional_experience->count() === 0) {
            return $this->response->redirect('/admin/professional_experience/index?notice=' . urlencode('The search did not find any professional_experience'));
        }

        /** @var ProfessionalExperience $pro */
        foreach ($professional_experience as $pro) {
            $this->afterFind($pro);

        }

        $paginator = new Paginator([
            'data' => $professional_experience,
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
    public function editAction($id)
    {
        if ($this->request->isPost()) {
            return $this->response->redirect('/admin/professional_experience/index');
        }
        $professional_experience = ProfessionalExperience::findFirst((int)$id);
        if (!$professional_experience) {
            return $this->response->redirect('/admin/professional_experience/index?notice=' . urlencode('professional_experience was not found'));
        }

        $this->view->id = $professional_experience->getId();

        $this->tag::setDefault('id', $professional_experience->getId());
        $this->tag::setDefault('title', $professional_experience->getTitle());
        $this->tag::setDefault('description', $professional_experience->getDescription());
        $this->tag::setDefault('user_id', $professional_experience->getUserId());
        $this->tag::setDefault('organization', $professional_experience->getOrganization());
        $this->tag::setDefault('country', $professional_experience->getCountry());
        $this->tag::setDefault('site', $professional_experience->getSite());
        $this->tag::setDefault('professional_area', $professional_experience->getProfessionalArea());
        $this->tag::setDefault('position', $professional_experience->getPosition());
        $this->tag::setDefault('start', $professional_experience->getStart());
        $this->tag::setDefault('finish', $professional_experience->getFinish());

        $this->returnView('edit');
        return null;
    }

    /**
     * Creates a new professional_experience
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            return $this->response->redirect('/admin/professional_experience/index');
        }

        $professional_experience = new ProfessionalExperience();
        $professional_experience->setTitle($this->request->getPost('title'));
        $professional_experience->setDescription($this->request->getPost('description'));
        $professional_experience->setUserId($this->request->getPost('user_id'));
        $professional_experience->setOrganization($this->request->getPost('organization'));
        $professional_experience->setCountry($this->request->getPost('country'));
        $professional_experience->setSite($this->request->getPost('site'));
        $professional_experience->setProfessionalArea($this->request->getPost('professional_area'));
        $professional_experience->setPosition($this->request->getPost('position'));
        $professional_experience->setStart($this->request->getPost('start'));
        $professional_experience->setFinish($this->request->getPost('finish'));

        $this->transformModelBeforeSave($professional_experience);


        if (!$professional_experience->save()) {
            $mes = '';
            foreach ($professional_experience->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/professional_experience/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/professional_experience/index?success=' . urlencode('professional_experience was created successfully'));
    }

    /**
     * Saves a professional_experience edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            return $this->response->redirect('/admin/professional_experience/index');
        }

        $id = $this->request->getPost('id');
        $professional_experience = ProfessionalExperience::findFirst((int)$id);

        if (!$professional_experience) {
            return $this->response->redirect('/admin/professional_experience/index?notice=' . urlencode('professional_experience does not exist ' . $id));
        }

        $professional_experience->setTitle($this->request->getPost('title'));
        $professional_experience->setDescription($this->request->getPost('description'));
        $professional_experience->setUserId($this->request->getPost('user_id'));
        $professional_experience->setOrganization($this->request->getPost('organization'));
        $professional_experience->setCountry($this->request->getPost('country'));
        $professional_experience->setSite($this->request->getPost('site'));
        $professional_experience->setProfessionalArea($this->request->getPost('professional_area'));
        $professional_experience->setPosition($this->request->getPost('position'));
        $professional_experience->setStart($this->request->getPost('start'));
        $professional_experience->setFinish($this->request->getPost('finish'));

        $this->transformModelBeforeSave($professional_experience);

        if (!$professional_experience->save()) {
            $mes = '';
            foreach ($professional_experience->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/professional_experience/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/professional_experience/index?success=' . urlencode('professional_experience was updated successfully'));
    }

    /**
     * @param $id
     * @return Response
     */
    public function deleteAction($id): Response
    {
        $professional_experience = ProfessionalExperience::findFirst((int)$id);
        if (!$professional_experience) {
            return $this->response->redirect('/admin/professional_experience/index?notice=' . urlencode('professional_experience was not found'));
        }

        if (!$professional_experience->delete()) {
            $mes = '';
            foreach ($professional_experience->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/professional_experience/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/professional_experience/index?success=' . urlencode('professional_experience was deleted successfully'));
    }


    /************************************ SITE *************/


    /**
     * @param $id
     * @return \Phalcon\Http\Response | null | array
     * @throws \RuntimeException
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function showExperience($id)
    {
        $id = (int)$id;
        if (empty($id)) {
            return $this->createErrorResponse('Id is absent');
        }
        $exp = ProfessionalExperience::find($id)[0];
        if (!$exp) {
            return $this->createErrorResponse('Experience not found');
        }

        $this->afterFind($exp);

        /** @var UsersService $userService */
        $userService = $this->userService;
        $role = $userService->getRole();
        $options = [];
        if (\in_array($role, AclRoles::ADMIN_ROLES, true)) {
            $options['admin'] = true;
        }
        $options['show'] = true;
        $form = new ProfessionExperienceForm($exp, $options);

        $form->setFormId('experience_form');

        $form->renderForm();

        $imageTag =  '';


        $config = $this->getDI()->get(Services::CONFIG);

        $view = new Simple();
        $template = $config->get('application')->viewsDir . '/professionalexperience/experience.phtml';

        $html = $view->render(
            $template,
            [
                'form' => $form,
                'image' => $imageTag,
                'messages' => [],
                'id' => $exp->getId()
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
    public function addExperience()
    {
        $exp = new ProfessionalExperience();
        $messages = [];

        $config = $this->getDI()->get(Services::CONFIG);

        $view = new Simple();

        $template = $config->get('application')->viewsDir . '/professionalexperience/experience.phtml';

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


            $validator = new ProfessionalExperienceValidator();
            $res = $validator->validate($params);
            if ($res->count() === 0) {
                $this->sanitizePostData($params);
                if ($exp->save($params)) {
                    return $this->createOkResponse();
                }
                $messages = $exp->getMessages();
            } else {
                $messages = $validator->getMessages();
            }
        }

        $form = new ProfessionExperienceForm($exp, $options);

        $form->setFormId('experience_form');

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
    public function updateExperience($id)
    {
        $id = (int)$id;
        if (empty($id)) {
            return $this->createErrorResponse('Id is absent');
        }
        $exp = ProfessionalExperience::find($id)[0];
        if (!$exp) {
            return $this->createErrorResponse('Experience not found');
        }
        $messages = [];

        $imageTag =  '';


        $config = $this->getDI()->get(Services::CONFIG);

        $view = new Simple();
        $template = $config->get('application')->viewsDir . '/professionalexperience/experience.phtml';

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

            $validator = new ProfessionalExperienceValidator();
            $res = $validator->validate($params);
            if ($res->count() === 0) {
                $this->sanitizePostData($params);
                if ($exp->save($params)) {
                    $form = new ProfessionExperienceForm($exp, $options);

                    $form->setFormId('experience_form');
                    $form->setShow(false);
                    $form->renderForm();

                    $imageTag =  '';


                    $html = $view->render(
                        $template,
                        [
                            'form' => $form,
                            'image' => $imageTag,
                            'messages' => $messages,
                            'id' => $exp->getId()
                        ]
                    );
                    return $this->createArrayResponse($html, 'html');
                }
            } else {
                $messages = $validator->getMessages();
            }

            $exp->refresh();
        }


        $form = new ProfessionExperienceForm($exp, $options);

        $form->setFormId('experience_form');

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
    public function listUserExperiences()
    {
        $numberPage = $this->request->getQuery('page', 'int');

        /** @var Resultset $exp */


        /** @var UsersService $userService */
        $userService = $this->userService;

        if (!$userService->getIdentity()) {
            return $this->createErrorResponse('Not authorized');
        }


        $role = $userService->getRole();
        if (\in_array($role, AclRoles::ADMIN_ROLES, true)) {
            $exp = ProfessionalExperience::find();
        } else {
            $id = (int) $userService->getIdentity();
            $exp = ProfessionalExperience::find('user_id = ' . $id);
        }

        if ($exp->count() === 0) {
            return $this->createErrorResponse('Not found');
        }

        $paginator = new Paginator([
            'data' => $exp,
            'limit'=> Limits::SEARCH_LIMIT,
            'page' => $numberPage
        ]);

        $page = $paginator->getPaginate();

        $items = $page->items;

        foreach ($items as &$item) {
            $item->title = html_entity_decode($item->title);
            $item->description = html_entity_decode($item->description);
            $item->location = html_entity_decode($item->location);
            $item->professional_area = html_entity_decode($item->professional_area);
            $item->position = html_entity_decode($item->position);
            $item->organization = html_entity_decode($item->organization);
            $item->site = html_entity_decode($item->site);
        }

        unset($item);

        $pagesInRange = $this->getPaginationRange($page);

        $data = [
            'experiences'   => $items,
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
    public function deleteExperience($id): Response
    {
        $exp = ProfessionalExperience::findFirst((int)$id);

        $me = $this->userService->getDetails();

        if ($me) {
            return $this->createErrorResponse('Only for authorized users');
        }

        $user = $exp->getUsers();

        if ($me->getId() !== $user->getId() && !in_array($this->userService->getRole(), AclRoles::ADMIN_ROLES, true)) {
            return $this->createErrorResponse('You have no permission for this operation');
        }

        if (!$exp->delete()) {
            $mes = '';
            foreach ($exp->getMessages() as $message) {
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
