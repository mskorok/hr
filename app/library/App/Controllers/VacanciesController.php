<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Constants\AclRoles;
use App\Constants\Limits;
use App\Constants\Services;
use App\Forms\VacanciesForm;
use App\Model\Applied;
use App\Model\Companies;
use App\Model\Images;
use App\Model\JobTypes;
use App\Model\Users;
use App\Model\Vacancies;
use App\Model\VacancyJobTypes;
use App\Services\UsersService;
use App\Traits\RenderView;
use App\User\Service;
use App\Validators\VacanciesValidator;
use Exception;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Mvc\Model\Query\Builder;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\View\Simple;
use Phalcon\Paginator\Adapter\Model as Paginator;
use Phalcon\Mvc\Model\Query\Builder as QueryBuilder;
use Phalcon\Paginator\Factory;
use Phalcon\Validation\Message\Group;
use Phalcon\Http\Response;
use ReflectionException;
use RuntimeException;

/**
 * Class VacanciesController
 * @package App\Controllers
 */
class VacanciesController extends ControllerBase
{

    use RenderView;

    public static $availableIncludes = [
        'Applicants',
        'Companies',
        'Favorites',
        'JobTypes'
    ];

    public static $encodedFields = [
        'name',
        'currency',
        'description',
        'city',
        'professional_experience',
        'work_place',
        'location',
        'responsibilities',
        'main_requirements',
        'additional_requirements',
        'work_conditions',
        'key_skills'
    ];

    /**
     * Index action
     */
    public function indexAction(): void
    {
        $this->returnView('index');
    }

    /**
     * Searches for vacancies
     */
    public function searchAction()
    {
        $numberPage = 1;
        $parameters = [];
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, Vacancies::class, $_POST);
            $parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery('page', 'int');
        }


        $parameters['order'] = 'id';

        $vacancies = Vacancies::find($parameters);
        if (\count($vacancies) === 0) {
            return $this->response->redirect('/admin/vacancies/index?notice=' . urlencode('The search did not find any vacancies'));
        }


        /** @var Vacancies $vacancy */
        foreach ($vacancies as $vacancy) {
            $this->afterFind($vacancy);

        }

        $paginator = new Paginator([
            'data' => $vacancies,
            'limit'=> Limits::SEARCH_LIMIT,
            'page' => $numberPage
        ]);

        $page = $paginator->getPaginate();

        $this->returnView('search', ['page' => $page, 'limit' => $paginator->getLimit()]);
    }

    /**
     * Searches for vacancies
     */
    public function listAction()
    {
        $numberPage = $this->request->getQuery('page', 'int');

        /** @var Resultset $vacancies */
        $vacancies = Vacancies::find();
        if ($vacancies->count() === 0) {
            return $this->response->redirect('/admin/vacancies/index?notice=' . urlencode('The search did not find any vacancies'));
        }

        /** @var Vacancies $vacancy */
        foreach ($vacancies as $vacancy) {
            $this->afterFind($vacancy);

        }

        $paginator = new Paginator([
            'data' => $vacancies,
            'limit'=> Limits::SEARCH_LIMIT,
            'page' => $numberPage
        ]);

        $page = $paginator->getPaginate();

        $this->returnView('lists', ['page' => $page, 'limit' => $paginator->getLimit()]);
    }

    /**
     * Displays the creation form
     * @throws ReflectionException
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
            return $this->response->redirect('/admin/vacancies/index');
        }

        $vacancy = Vacancies::findFirst((int)$id);
        if (!$vacancy) {
            return $this->response->redirect('/admin/vacancies/index?notice=' . urlencode('vacancy was not found'));
        }

        $this->view->id = $vacancy->getId();

        $this->tag::setDefault('id', $vacancy->getId());
        $this->tag::setDefault('name', $vacancy->getName());
        $this->tag::setDefault('salary', $vacancy->getSalary());
        $this->tag::setDefault('company_id', $vacancy->getCompanyId());
        $this->tag::setDefault('professional_experience', $vacancy->getProfessionalExperience());
        $this->tag::setDefault('work_place', $vacancy->getWorkPlace());
        $this->tag::setDefault('description', $vacancy->getDescription());
        $this->tag::setDefault('responsibilities', $vacancy->getResponsibilities());
        $this->tag::setDefault('main_requirements', $vacancy->getMainRequirements());
        $this->tag::setDefault('additional_requirements', $vacancy->getAdditionalRequirements());
        $this->tag::setDefault('work_conditions', $vacancy->getWorkConditions());
        $this->tag::setDefault('key_skills', $vacancy->getKeySkills());
        $this->tag::setDefault('start', $vacancy->getStart());
        $this->tag::setDefault('finish', $vacancy->getFinish());

        $this->returnView('edit');
        return null;
    }

    /**
     * @desc Creates a new vacancy
     * @return ResponseInterface
     */
    public function createAction(): ResponseInterface
    {
        if (!$this->request->isPost()) {
            return $this->response->redirect('/admin/vacancies/index');
        }

        $vacancy = new Vacancies();
        $vacancy->setName($this->request->getPost('name'));
        $vacancy->setSalary($this->request->getPost('salary'));
        $vacancy->setCompanyId($this->request->getPost('company_id'));
        $vacancy->setProfessionalExperience($this->request->getPost('professional_experience'));
        $vacancy->setWorkPlace($this->request->getPost('work_place'));
        $vacancy->setDescription($this->request->getPost('description'));
        $vacancy->setResponsibilities($this->request->getPost('responsibilities'));
        $vacancy->setMainRequirements($this->request->getPost('main_requirements'));
        $vacancy->setAdditionalRequirements($this->request->getPost('additional_requirements'));
        $vacancy->setWorkConditions($this->request->getPost('work_conditions'));
        $vacancy->setKeySkills($this->request->getPost('key_skills'));
        $vacancy->setStart($this->request->getPost('start'));
        $vacancy->setFinish($this->request->getPost('finish'));
        $vacancy->beforeCreate();

        $this->transformModelBeforeSave($vacancy);

        if (!$vacancy->save()) {
            $mes = implode('', $vacancy->getMessages());

            return $this->response->redirect('/admin/vacancies/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/vacancies/index?success=' . urlencode('vacancy was created successfully'));
    }

    /**
     * @desc Saves edited vacancy
     * @return ResponseInterface
     */
    public function saveAction(): ResponseInterface
    {

        if (!$this->request->isPost()) {
            return $this->response->redirect('/admin/vacancies/index');
        }

        $id = $this->request->getPost('id');
        $vacancy = Vacancies::findFirst((int)$id);

        if (!$vacancy) {
            return $this->response->redirect('/admin/vacancies/index?notice=' . urlencode('vacancy does not exist ' . $id));
        }

        $vacancy->setName($this->request->getPost('name'));
        $vacancy->setSalary($this->request->getPost('salary'));
        $vacancy->setCompanyId($this->request->getPost('company_id'));
        $vacancy->setProfessionalExperience($this->request->getPost('professional_experience'));
        $vacancy->setWorkPlace($this->request->getPost('work_place'));
        $vacancy->setDescription($this->request->getPost('description'));
        $vacancy->setResponsibilities($this->request->getPost('responsibilities'));
        $vacancy->setMainRequirements($this->request->getPost('main_requirements'));
        $vacancy->setAdditionalRequirements($this->request->getPost('additional_requirements'));
        $vacancy->setWorkConditions($this->request->getPost('work_conditions'));
        $vacancy->setKeySkills($this->request->getPost('key_skills'));
        $vacancy->setStart($this->request->getPost('start'));
        $vacancy->setFinish($this->request->getPost('finish'));
        $vacancy->beforeUpdate();

        $this->transformModelBeforeSave($vacancy);

        if (!$vacancy->save()) {
            $mes = implode('', $vacancy->getMessages());

            return $this->response->redirect('/admin/vacancies/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/vacancies/index?success=' . urlencode('vacancy was updated successfully'));
    }

    /**
     * @param $id
     * @return ResponseInterface
     */
    public function deleteAction($id): ResponseInterface
    {
        $vacancy = Vacancies::findFirst((int)$id);
        if (!$vacancy) {
            return $this->response->redirect('/admin/vacancies/index?notice=' . urlencode('vacancy was not found'));
        }

        if (!$vacancy->delete()) {
            $mes = implode('', $vacancy->getMessages());

            return $this->response->redirect('/admin/vacancies/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/vacancies/index?success=' . urlencode('vacancy was deleted successfully'));
    }


    /************************************ SITE *************/


    /**
     * @param $user
     * @param $vacancy
     * @return mixed
     */
    public function apply($user, $vacancy)
    {
        $applied = new Applied();
        $applied->setUserId((int) $user);
        $applied->setVacancyId((int) $vacancy);

        if ($applied->save()) {
            return $this->createOkResponse();
        }
        return $this->createErrorResponse('Model not saved');
    }


    /**
     * @param $id
     * @return Response | null | array
     * @throws RuntimeException
     * @throws ReflectionException
     * @throws Exception
     */
    public function showVacancy($id)
    {
        $id = (int)$id;
        if (empty($id)) {
            return $this->createErrorResponse('Id is absent');
        }
        $vacancy = Vacancies::find($id)[0];
        if (!$vacancy) {
            return $this->createErrorResponse('Vacancy not found');
        }
        $this->afterFind($vacancy);

        /** @var UsersService $userService */
        $userService = $this->userService;
        $role = $userService->getRole();
        $options = [];
        if (\in_array($role, AclRoles::ADMIN_ROLES, true)) {
            $options['admin'] = true;
        }
        $options['show'] = true;
        $options['user'] = $userService->getDetails();
        $form = new VacanciesForm($vacancy, $options);

        $form->setFormId('vacancy_form');


        $form->renderForm();
        $image = $vacancy->getCompanies()->getImages();
        $imageTag =  '';
        $config = $this->getDI()->get(Services::CONFIG);
        $uploadsDir = $config->hostName;
        if ($image instanceof Images) {
            $imageTag = '<image src="' . $uploadsDir . $image->getPath() .
                $image->getFileName() . '" style="width: 100%;"/>';
        }

        $config = $this->getDI()->get(Services::CONFIG);

        $view = new Simple();

        $template = $config->get('application')->viewsDir . '/vacancies/vacancy.phtml';

        $html = $view->render(
            $template,
            [
                'form' => $form,
                'image' => $imageTag,
                'messages' => [],
                'id' => $vacancy->getId()
            ]
        );
        return $this->createArrayResponse($html, 'html');

    }

    /**
     *
     * @throws RuntimeException
     * @throws ReflectionException
     * @throws Exception
     * @return Response | null | array
     */
    public function addVacancy()
    {
        $vacancy = new Vacancies();
        $messages = [];

        $config = $this->getDI()->get(Services::CONFIG);

        $view = new Simple();

        $template = $config->get('application')->viewsDir . '/vacancies/vacancy.phtml';

        /** @var UsersService $userService */
        $userService = $this->userService;
        $user = $userService->getDetails();
        if (!$user) {
            return $this->createErrorResponse('User not found in controller' . serialize($this->request->getHeaders()));
        }

        $role = $userService->getRole();

        $options = [];
        if (\in_array($role, [AclRoles::SUPERADMIN, AclRoles::ADMIN], true)) {
            $options = ['admin' => true];
        } else {
            /** @var Resultset\Simple $companies */
            $companies = $user->getCompanies();
            if (!$companies || !($companies->getFirst() instanceof Companies)) {
                return $this->createErrorResponse('company not found');
            }
        }
        $options['user'] = $user;

        $config = $this->getDI()->get(Services::CONFIG);
        $uploadsDir = $config->hostName;

        if ($this->request->isPost()) {
            $params = $this->request->getPost();
            $image = null;


            $validator = new VacanciesValidator();
            $res = $validator->validate($params);
            if ($res->count() === 0) {
                $vacancy->beforeCreate();
                $this->sanitizePostData($params);
                if ($vacancy->save($params)) {
                    return $this->createOkResponse();
                }
                $messages = $vacancy->getMessages();
            } else {
                $messages = $validator->getMessages();
            }
        }

        $form = new VacanciesForm($vacancy, $options);

        $form->setFormId('vacancy_form');

        $form->renderForm();


        $company = $vacancy->getCompanies();
        $image = null;
        if ($company instanceof Companies) {
            $image = $company->getImages();
        }

        $imageTag =  '';
        if ($image instanceof Images) {
            $imageTag = '<image src="' . $uploadsDir . $image->getPath() .
                $image->getFileName() . '" style="width: 100%;"/>';
        }


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
     * @return Response | null | array
     * @throws RuntimeException
     * @throws ReflectionException
     * @throws Exception
     */
    public function updateVacancy($id)
    {
        $id = (int)$id;
        if (empty($id)) {
            return $this->createErrorResponse('Id is absent');
        }
        $vacancy = Vacancies::find($id)[0];
        if (!$vacancy) {
            return $this->createErrorResponse('Vacancy not found');
        }
        $messages = [];

        $this->afterFind($vacancy);

        /** @var UsersService $userService */
        $userService = $this->userService;
        $role = $userService->getRole();
        $options = [];
        if (\in_array($role, [AclRoles::SUPERADMIN, AclRoles::ADMIN], true)) {
            $options = ['admin' => true];
        }

        $options['user'] = $userService->getDetails();

        $config = $this->getDI()->get(Services::CONFIG);
        $uploadsDir = $config->hostName;

        if ($this->request->isPost()) {
            $params = $this->request->getPost();

            $image = null;

            $validator = new VacanciesValidator();
            $res = $validator->validate($params);
            if ($res->count() === 0) {
                /** @var Vacancies $vacancy */
                $vacancy->beforeUpdate();
                $this->sanitizePostData($params);
                if ($vacancy->save($params)) {
                    $form = new VacanciesForm($vacancy, $options);

                    $form->setFormId('vacancy_form');
                    $form->setShow(false);

                    $form->renderForm();
                    $image = $vacancy->getCompanies()->getImages();
                    $imageTag =  '';
                    if ($image instanceof Images) {
                        $imageTag = '<image src="' . $uploadsDir . $image->getPath() .
                            $image->getFileName() . '" style="width: 100%;"/>';
                    }

                    $html = $this->returnView(
                        'vacancy',
                        [
                            'form' => $form,
                            'image' => $imageTag,
                            'messages' => $messages,
                            'id' => $vacancy->getId()
                        ],
                        true
                    );
                    return $this->createArrayResponse($html, 'html');
                }
            } else {
                $messages = $validator->getMessages();
            }

            $vacancy->refresh();
        }


        $form = new VacanciesForm($vacancy, $options);

        $form->setFormId('vacancy_form');

        $form->renderForm();
        $image = $vacancy->getCompanies()->getImages();
        $imageTag =  '';
        if ($image instanceof Images) {
            $imageTag = '<image src="' . $uploadsDir . $image->getPath() .
                $image->getFileName() . '" style="width: 100%;"/>';
        }

        $config = $this->getDI()->get(Services::CONFIG);

        $view = new Simple();
        $template = $config->get('application')->viewsDir . '/vacancies/vacancy.phtml';

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
     * @return Response
     * @throws Exception
     */
    public function deleteVacancy($id): Response
    {
        $vacancy = Vacancies::findFirst((int)$id);

        $me = $this->userService->getDetails();

        if ($me) {
            return $this->createErrorResponse('Only for authorized users');
        }

        $users = $vacancy->getCompanies()->getUsers();

        $ids = [];

        /** @var Users $user */
        foreach ($users as $user) {
            $ids[] = $user->getId();
        }

        if (!in_array($me->getId(), $ids, true) && !in_array($this->userService->getRole(), AclRoles::ADMIN_ROLES, true)) {
            return $this->createErrorResponse('You have no permission for this operation');
        }

        if (!$vacancy->delete()) {
            $mes = implode('', $vacancy->getMessages());

            return $this->createErrorResponse($mes);
        }

        return $this->createOkResponse();
    }

    /**
     * @param $page
     * @return mixed
     */
    public function listAllVacancies($page)
    {
        $numberPage = (int)($page ?? 1);

        $count = Vacancies::count();
        if ($count() === 0) {
            return $this->createErrorResponse('Not found');
        }

        /** @var Resultset $vacancies */
        $vacancies = Vacancies::find();

        $paginator = new Paginator([
            'data' => $vacancies,
            'limit'=> Limits::SEARCH_LIMIT,
            'page' => $numberPage
        ]);

        $page = $paginator->getPaginate();

        $items = $page->items;

        /** @var Vacancies $vacancy */
        foreach ($items as $vacancy) {
            $this->afterFind($vacancy);

        }

        $pagesInRange = $this->getPaginationRange($page);

        $items = $this->getComplexArray($items);


        $data = [
            'vacancies'     => $items,
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
     * @param $page
     * @return mixed
     * @throws \PhalconApi\Exception
     */
    public function listUserVacancies($page)
    {
        $numberPage = (int)($page ?? 1);

        /** @var Resultset $vacancies */


        /** @var UsersService $userService */
        $userService = $this->userService;

        if (!$userService->getIdentity()) {
            return $this->createErrorResponse('Not authorized');
        }


        $role = $userService->getRole();
        if (\in_array($role, AclRoles::ADMIN_ROLES, true)) {
            $vacancies = Vacancies::find();
        } else {
            /** @var Users $user */
            $user = (int) $userService->getDetails();

            $companies = $user->getCompanies();
            if ($companies->count() > 0) {
                /** @var Companies $company */
                $company = $companies[0];
                $vacancies = Vacancies::find('company_id = ' . $company->getId());
            } else {
                return $this->createErrorResponse('Company not found');
            }

        }

        if ($vacancies->count() === 0) {
            return $this->createErrorResponse('Not found');
        }

        /** @var Vacancies $vacancy */
        foreach ($vacancies as $vacancy) {
            $this->afterFind($vacancy);

        }

        $paginator = new Paginator([
            'data' => $vacancies,
            'limit'=> Limits::SEARCH_LIMIT,
            'page' => $numberPage
        ]);

        $page = $paginator->getPaginate();

        $items = $page->items;

        /** @var Vacancies $vacancy */
        foreach ($items as $vacancy) {
            $this->afterFind($vacancy);

        }

        $pagesInRange = $this->getPaginationRange($page);

        $items = $this->getComplexArray($items);

        $data = [
            'vacancies'     => $items,
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
     * @return mixed
     */
    public function searchVacancy()
    {
        $params = $this->request->getPost();

        if ($params['where'] === null) {
            $params['where'] = '';
        }

        if ($params['what'] === null) {
            $params['what'] = '';
        }

        $results = [];
        $results1 = [];
        $results2 = [];


        $where = $this->sanitize($params['where']);
        $what = $this->sanitize($params['what']);
        $salary = (int) ($params['salary'] ?? 0);
        $currency = $params['currency'] ?? '';
        $type = $params['type'] ?? null;
        $_order = (int) ($params['order'] ?? 0);
        $offset = (int) ($params['page'] ?? 1);

        switch ($_order) {
            case 1:
                $order = 'creationDate DESC';
                break;
            case 2:
                $order = 'creationDate ASC';
                break;
            case 3:
                $order = 'salary DESC';
                break;
            case 4:
                $order = 'salary ASC';
                break;
            default:
                $order = null;
        }

        if (!empty($what) && strlen($what) > 1) {
            $sql = "SELECT id,
                MATCH (`name`, `professional_experience`, `description`, `responsibilities`, `main_requirements`, `additional_requirements`, `work_conditions`, `key_skills`,`location`) AGAINST ('{$what}' IN BOOLEAN MODE) as REL
                FROM `vacancies`
                WHERE MATCH (`name`, `professional_experience`, `description`, `responsibilities`, `main_requirements`, `additional_requirements`, `work_conditions`, `key_skills`,`location`) AGAINST ('{$what}' IN BOOLEAN MODE)
                ORDER BY REL;";

            $connection = $this->db;
            $res = $connection->query($sql);

            if ($res->numRows() === 0) {
                $sql = "SELECT id FROM `vacancies` WHERE  `name` LIKE '%{$what}%' 
                            OR `professional_experience` LIKE '%{$what}%' 
                            OR `description` LIKE '%{$what}%' 
                            OR `responsibilities` LIKE '%{$what}%' 
                            OR `main_requirements` LIKE '%{$what}%' 
                            OR `additional_requirements` LIKE '%{$what}%' 
                            OR `work_conditions` LIKE '%{$what}%'
                            OR `key_skills` LIKE '%{$what}%' 
                            OR `location` LIKE '%{$what}%' 
                            ";

                $res = $connection->query($sql);
            }

            do {
                $row = $res->fetchArray();
                if ($row) {
                    $results[] = (int)$row['id'];
                }
            } while($row);
        }

        if (!empty($where) && strlen($where) > 1) {
            $sql1 = "SELECT id,
                MATCH (`location`) AGAINST ('{$where}' IN BOOLEAN MODE) as REL
                FROM `vacancies`
                WHERE MATCH (`location`) AGAINST ('{$where}' IN BOOLEAN MODE)
                ORDER BY REL;";

            $connection = $this->db;
            $res = $connection->query($sql1);

            do {
                $row = $res->fetchArray();
                if ($row) {
                    $results1[] = (int)$row['id'];
                }
            } while($row);
        }

        if (!empty($where) && strlen($where) > 1) {
            $sql2 = "SELECT id,
                MATCH (`city`) AGAINST ('{$where}' IN BOOLEAN MODE) as REL
                FROM `vacancies`
                WHERE MATCH (`city`) AGAINST ('{$where}' IN BOOLEAN MODE)
                ORDER BY REL;";

            $connection = $this->db;
            $res = $connection->query($sql2);

            do {
                $row = $res->fetchArray();
                if ($row) {
                    $results2[] = (int)$row['id'];
                }
            } while($row);
        }

        if ($what && $where) {
            $_ids = array_merge($results1, $results2);
            $_ids = array_unique($_ids);
            $ids = array_intersect($results, $_ids);
        } elseif ($what) {
            $ids = $results;
        } elseif ($where) {
            $_ids = array_merge($results1, $results2);
            $ids = array_unique($_ids);
        }

        $builder = new Builder();
        $builder->addFrom(Vacancies::class);

        if (!empty($ids)) {
            $builder->inWhere('id', $ids);
        }

        if (!empty($salary)) {
            $builder->andWhere('[' . Vacancies::class . '].[salary] >= :salary:', ['salary' => $salary]);
        }

        if (!empty($type) && in_array($type, ['insite', 'remote', 'part-time', 'full-time', 'project', 'volunteer'])) {
            $builder->andWhere('[' . Vacancies::class . '].[work_place] = :type:', ['type' => $type]);
        }

        if (!empty($currency) && in_array($currency, ['USD', 'EURO', 'GBP', 'BRL', 'TRY', 'PLN', 'SEK', 'JPY', 'CAD', 'AUD'])) {
            $builder->andWhere('[' . Vacancies::class . '].[currency] = :currency:', ['currency' => $currency]);
        }

        if ($order) {
            $builder->orderBy($order);
        }


        $options = [
            'builder' => $builder,
            'limit'   => Limits::SEARCH_LIMIT,
            'page'    => $offset,
            'adapter' => 'queryBuilder',
        ];

        $paginator = Factory::load($options);

        $page =  $paginator->getPaginate();

        $items = $page->items;

        /** @var Vacancies $vacancy */
        foreach ($items as $vacancy) {
            $this->afterFind($vacancy);

        }

        $pagesInRange = $this->getPaginationRange($page);

        $items = $this->getComplexArray($items);

        $data = [
            'vacancies'     => $items,
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
     * @param $page
     * @return mixed
     */
    public function listApplied($page)
    {
        /** @var Service $userService */
        $userService = $this->userService;
        $id = $userService->getIdentity();
        $builder = new Builder();
        $builder->addFrom(Vacancies::class);
        $builder->leftJoin(
            Applied::class,
            '[' . Applied::class . '].[vacancy_id] = [' . Vacancies::class . '].[id]'
        );
        $builder->where('[' . Applied::class . '].[user_id] = :user:', ['user' => $id]);




        $options = [
            'builder' => $builder,
            'limit'   => Limits::SEARCH_LIMIT,
            'page'    => (int) $page,
            'adapter' => 'queryBuilder',
        ];

        $paginator = Factory::load($options);

        $page =  $paginator->getPaginate();

        $items = $page->items;

        /** @var Vacancies $vacancy */
        foreach ($items as $vacancy) {
            $this->afterFind($vacancy);

        }

        $pagesInRange = $this->getPaginationRange($page);

        $items = $this->getComplexArray($items);

        $data = [
            'vacancies'     => $items,
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
     * @throws RuntimeException
     */
    protected function onDataInvalid($data)
    {
        $mes = [];
        foreach ($this->messages as $message) {
            $mes[] = $message->getMessage();
        }

        return $this->createErrorResponse($mes);
    }

    /**************************  TEST *********************/

    /**
     *
     */
    public function listVacancies(): void
    {
        $this->returnView('list');
    }

    /**
     *
     * @return ResponseInterface|null
     *@throws ReflectionException
     * @throws Exception
     * @throws RuntimeException
     */
    public function add(): ?ResponseInterface
    {
        $vacancy = new Vacancies();
        $messages = [];
        if ($this->request->isPost()) {
            $params = $this->request->getPost();


            $validator = new VacanciesValidator();
            $res = $validator->validate($params);
            if ($res->count() === 0) {
                $this->sanitizePostData($params);
                if ($vacancy->save($params)) {
                    return $this->response->redirect('/admin/vacancy/edit/' . $vacancy->getId());
                }
                $messages = $vacancy->getMessages();
            } else {
                $messages = $validator->getMessages();
            }
        }

        $form = new VacanciesForm($vacancy);
        $form->renderForm();
        $this->returnView('add', compact('form', 'messages'));
        return null;
    }


    /**
     * @param $id
     * @return ResponseInterface|null
     * @throws RuntimeException
     * @throws ReflectionException
     * @throws Exception
     */
    public function updates($id): ?ResponseInterface
    {
        $id = (int)$id;
        if (empty($id)) {
            return $this->response->redirect('/admin/vacancy/list');
        }
        $vacancy = Vacancies::find($id)[0];
        if (!$vacancy) {
            return $this->response->redirect('/admin/vacancy/list');
        }
        $messages = [];

        $this->afterFind($vacancy);

        if ($this->request->isPost()) {
            $params = $this->request->getPost();
            $validator = new VacanciesValidator();
            $res = $validator->validate($params);
            if ($res->count() === 0) {
                $this->sanitizePostData($params);
                /** @var Vacancies $vacancy */
                $vacancy->save($params);
            } else {
                $messages = $validator->getMessages();
            }

            $vacancy->refresh();
        }

        $form = new VacanciesForm($vacancy);


        $form->renderForm();


        $this->returnView(
            'updates',
            [
                'form' => $form,
                'messages' => $messages,
                'id' => $vacancy->getId()
            ]
        );
        return null;
    }


    /**
     * @param Model $item
     */
    protected function afterSave(Model $item): void
    {
        /** @var $item Vacancies */
        parent::afterSave($item);

        $types = $this->request->getPost('type_of_job');

        foreach ($types as $name) {
            $type = JobTypes::findFirst("name = '{$name}'");
            $model = new VacancyJobTypes();
            $model->setVacancyId($item->getId());
            $model->setTypeId($type->getId());
            $model->save();
        }
    }

    /**
     * @param Model $item
     */
    protected function afterUpdate(Model $item): void
    {
        /** @var $item Vacancies */
        parent::afterUpdate($item);

        $id = $item->getId();
        $relations = VacancyJobTypes::find("vacancy_id = {$id}");
        $types = $this->request->getPost('type_of_job');

        $remained = [];

        $calculated = [];

        /** @var VacancyJobTypes $relation */
        foreach ($relations as $relation) {
            foreach ($types as $name) {
                $keys = array_keys($calculated);
                if (in_array($name, $keys, true)) {
                    $type = $calculated[$name];
                } else {
                    $type = JobTypes::findFirst("name = '{$name}'");
                    $calculated[$name] = $type;
                }

                if ($relation->getTypeId() === $type->getId()) {
                    $remained[] = $relation->getId();
                    continue;
                }
                $model = new VacancyJobTypes();
                $model->setVacancyId($item->getId());
                $model->setTypeId($type->getId());
                $model->save();
            }
        }

        foreach ($relations as $relation) {
            $id = $relation->getId();
            if (!in_array($id, $remained, true)) {
                $relation->delete();
            }
        }
    }


    /**
     * @param Model $item
     */
    protected function beforeRemove(Model $item): void
    {
        parent::beforeRemove($item);

        /** @var Vacancies $item */

        $items = VacancyJobTypes::find("vacancy_id = {$item->getId()}");

        foreach ($items as $entity) {
            $entity->delete();
        }
    }
}
