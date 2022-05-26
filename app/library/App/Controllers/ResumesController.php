<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Constants\AclRoles;
use App\Constants\Limits;
use App\Constants\Services;
use App\Forms\ResumesForm;
use App\Model\Companies;
use App\Model\FavoriteResume;
use App\Model\Images;
use App\Model\Invited;
use App\Model\JobTypes;
use App\Model\ResumeJobTypes;
use App\Model\Resumes;
use App\Model\Users;
use App\Services\UsersService;
use App\Traits\RenderView;
use App\User\Service;
use App\Validators\ImagesValidator;
use App\Validators\ResumesValidator;
use Exception;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Mvc\Model\Query\Builder;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Paginator\Adapter\Model as Paginator;
use Phalcon\Mvc\Model\Query\Builder as QueryBuilder;
use Phalcon\Paginator\Factory;
use Phalcon\Validation\Message;
use Phalcon\Validation\Message\Group;
use Phalcon\Http\Response;

/**
 * Class ResumesController
 * @package App\Controllers
 */
class ResumesController extends ControllerBase
{
    use RenderView;

    public static $availableIncludes = [
        'Users'
    ];

    public static $encodedFields = [
        'cv',
        'position',
        'professional_area',
        'currency',
        'work_place',
        'keySkills',
        'language',
        'about_me',
        'location',
        'certification'
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
     * Searches for resumes
     * @throws \ReflectionException
     */
    public function searchAction()
    {
        $numberPage = 1;
        $parameters = [];
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, Resumes::class, $_POST);
            $parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery('page', 'int');
        }


        $parameters['order'] = 'id';

        $resumes = Resumes::find($parameters);
        if (\count($resumes) === 0) {
            return $this->response->redirect('/admin/resumes/index?notice=' . urlencode('The search did not find any resumes'));
        }

        /** @var Resumes $resume */
        foreach ($resumes as $resume) {
            $this->afterFind($resume);
        }

        $paginator = new Paginator([
            'data' => $resumes,
            'limit'=> Limits::SEARCH_LIMIT,
            'page' => $numberPage
        ]);

        $page = $paginator->getPaginate();

        $this->returnView('search', ['page' => $page, 'limit' => $paginator->getLimit()]);
    }

    /**
     * Searches for resumes
     * @throws \ReflectionException
     */
    public function listAction()
    {
        $numberPage = $this->request->getQuery('page', 'int');

        /** @var Resultset $resumes */
        $resumes = Resumes::find();
        if ($resumes->count() === 0) {
            return $this->response->redirect('/admin/resumes/index?notice=' . urlencode('The search did not find any resumes'));
        }

        /** @var Resumes $resume */
        foreach ($resumes as $resume) {
            $this->afterFind($resume);
        }

        $paginator = new Paginator([
            'data' => $resumes,
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
     * @return Response
     * @throws \ReflectionException
     */
    public function editAction($id)
    {
        if ($this->request->isPost()) {
            return $this->response->redirect('/admin/resumes/index');
        }
        if (!$this->request->isPost()) {
            $resume = Resumes::findFirst((int)$id);
            if (!$resume) {
                return $this->response->redirect('/admin/resumes/index?notice=' . urlencode('resume was not found'));
            }

            $this->view->id = $resume->getId();

            $this->tag::setDefault('id', $resume->getId());
            $this->tag::setDefault('user_id', $resume->getUserId());
            $this->tag::setDefault('position', $resume->getPosition());
            $this->tag::setDefault('professional_area', $resume->getProfessionalArea());
            $this->tag::setDefault('salary', $resume->getSalary());
            $this->tag::setDefault('work_place', $resume->getWorkPlace());
            $this->tag::setDefault('key_skills', $resume->getKeySkills());
            $this->tag::setDefault('language', $resume->getLanguage());
            $this->tag::setDefault('about_me', $resume->getAboutMe());
            $this->tag::setDefault('certification', $resume->getCertification());

            $this->returnView('edit');
            return null;
        }
    }

    /**
     * Creates a new resume
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            return $this->response->redirect('/admin/resumes/index');
        }

        $resume = new Resumes();
        $resume->setUserId($this->request->getPost('user_id'));
        $resume->setPosition($this->request->getPost('position'));
        $resume->setProfessionalArea($this->request->getPost('professional_area'));
        $resume->setSalary($this->request->getPost('salary'));
        $resume->setWorkPlace($this->request->getPost('work_place'));
        $resume->setKeySkills($this->request->getPost('key_skills'));
        $resume->setLanguage($this->request->getPost('language'));
        $resume->setAboutMe($this->request->getPost('about_me'));
        $resume->setCertification($this->request->getPost('certification'));
        $resume->beforeCreate();

        $this->transformModelBeforeSave($resume);

        if (!$resume->save()) {
            $mes = '';
            foreach ($resume->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/resumes/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/resumes/index?success=' . urlencode('resume was created successfully'));
    }

    /**
     * Saves a resume edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            return $this->response->redirect('/admin/resumes/index');
        }

        $id = $this->request->getPost('id');
        $resume = Resumes::findFirst((int)$id);

        if (!$resume) {
            return $this->response->redirect('/admin/resumes/index?notice=' . urlencode('resume does not exist ' . $id));
        }

        $resume->setUserId($this->request->getPost('user_id'));
        $resume->setPosition($this->request->getPost('position'));
        $resume->setProfessionalArea($this->request->getPost('professional_area'));
        $resume->setSalary($this->request->getPost('salary'));
        $resume->setWorkPlace($this->request->getPost('work_place'));
        $resume->setKeySkills($this->request->getPost('key_skills'));
        $resume->setLanguage($this->request->getPost('language'));
        $resume->setAboutMe($this->request->getPost('about_me'));
        $resume->setCertification($this->request->getPost('certification'));
        $resume->beforeUpdate();

        $this->transformModelBeforeSave($resume);

        if (!$resume->save()) {
            $mes = '';
            foreach ($resume->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/resumes/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/resumes/index?success=' . urlencode('resume was updated successfully'));
    }

    /**
     * @param $id
     * @return Response
     */
    public function deleteAction($id): Response
    {
        $resume = Resumes::findFirst((int)$id);
        if (!$resume) {
            return $this->response->redirect('/admin/resumes/index?notice=' . urlencode('resume was not found'));
        }

        if (!$resume->delete()) {
            $mes = '';
            foreach ($resume->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/resumes/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/resumes/index?success=' . urlencode('resume was deleted successfully'));
    }


    /**
     * @param $data
     * @return array
     * @throws \RuntimeException
     */
    protected function transformPostData($data)
    {
        if (!isset($data['id'])) {
            $data['id'] = '';
        }

        $this->messages = new Group;
        $config = $this->getDI()->get(Services::CONFIG);
        $image = null;
        if ($this->request->hasFiles(true)) {
            $uploadDir = $config->application->uploadDir;

            if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755) && !is_dir($uploadDir)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $uploadDir));
            }
            /** @var \Phalcon\Http\Request\File $file */
            foreach ($this->request->getUploadedFiles(true) as $file) {
                if ($file->getKey() === 'fileName') {
                    $fileName = uniqid('Resume_' . date('Y-m-d') . '_', false);
                    $fileName .= '.' . $file->getExtension();
                    try {
                        $file->moveTo($uploadDir . $fileName);
                        $image = new Images();
                        $image->setFileName($fileName);
                        $image->setPath('/uploads/cv/');
                        $params = $image->toArray();
                        $imageValidator = new ImagesValidator();
                        $res = $imageValidator->validate($params);
                        if ($res->count() === 0) {
                            $image->save();
                        } else {
                            $this->messages = $imageValidator->getMessages();
                        }
                    } catch (\RuntimeException $exception) {
                        $message = new Message($exception->getMessage());
                        if (!($this->messages instanceof Group)) {
                            $this->messages = new Group;
                            $this->messages->appendMessage($message);
                        }
                    }
                }
            }
        }

        unset($data['fileName']);
        if ($image instanceof Images) {
            $data['cv'] = $image->getId();
        }

        if ($this->messages->count() > 0) {
            $messages = '';
            foreach ($this->messages as $message) {
                $messages .= $message->getMessage().PHP_EOL;
            }
            throw new \RuntimeException($messages);
        }

        return parent::transformPostData($data);
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


    /************************************ SITE *************/

    /**
     * @param $user
     * @param $resume
     * @return mixed
     */
    public function invite($user, $resume)
    {
        $user = Users::findFirst((int) $user);
        if ($user instanceof Users) {
            /** @var Resultset\Simple $companies */
            $companies = $user->getCompanies();
            $company = $companies[0];
            if ($company instanceof Companies) {
                $invite = new Invited();
                $invite->setUserId((int)$user);
                $invite->setResumeId((int) $resume);
                $invite->setCompanyId((int) $company);

                if ($invite->save()) {
                    return $this->createOkResponse();
                }

                return $this->createErrorResponse('Model not saved');
            }
            return $this->createErrorResponse('Company not found');
        }
        return $this->createErrorResponse('User not found');
    }


    /**
     * @param $id
     * @return \Phalcon\Http\Response | null
     * @throws \RuntimeException
     * @throws \ReflectionException
     * @throws Exception
     */
    public function showResume($id): ?Response
    {
        $id = (int)$id;
        if (empty($id)) {
            return $this->createErrorResponse('Id is absent');
        }
        $resume = Resumes::find($id)[0];

        if (!$resume) {
            return $this->createErrorResponse('Resume not found');
        }

        $this->afterFind($resume);


        /** @var UsersService $userService */
        $userService = $this->userService;

        if (!$userService->getIdentity()) {
            return $this->createErrorResponse('Not authorized');
        }

        $role = $userService->getRole();
        $options = [];
        if (\in_array($role, AclRoles::ADMIN_ROLES, true)) {
            $options['admin'] = true;
        }
        $options['show'] = true;
        $form = new ResumesForm($resume, $options);

        $form->setFormId('resume_form');

        $form->renderForm();
        $image = $resume->getUsers()->getImages();
        $config = $this->getDI()->get(Services::CONFIG);
        $uploadsDir = $config->hostName;
        $imageTag =  '';
        if ($image instanceof Images) {
            $imageTag = '<image src="' . $uploadsDir . $image->getPath() .
                $image->getFileName() . '" style="width: 100%;"/>';
        }

        $html = $this->returnView(
            'resume',
            [
                'form' => $form,
                'image' => $imageTag,
                'messages' => [],
                'id' => $resume->getId()
            ],
            true
        );
        return $this->createArrayResponse($html, 'html');

    }

    /**
     *
     * @throws \RuntimeException
     * @throws \ReflectionException
     * @throws Exception
     * @return Response | null
     */
    public function addResume(): ?Response
    {
        $resume = new Resumes();

        /** @var UsersService $userService */
        $userService = $this->userService;

        if (!$userService->getIdentity()) {
            return $this->createErrorResponse('Not authorized');
        }

        $role = $userService->getRole();
        $options = [];
        if (\in_array($role, [AclRoles::SUPERADMIN, AclRoles::ADMIN], true)) {
            $options = ['admin' => true];
        }
        $messages = [];
        $config = $this->getDI()->get(Services::CONFIG);
        $uploadsDir = $config->hostName;
        if ($this->request->isPost()) {
            $params = $this->request->getPost();
            $image = null;


            $validator = new ResumesValidator();
            $res = $validator->validate($params);
            if ($res->count() === 0) {
                $resume->beforeCreate();
                $this->sanitizePostData($params);
                if ($resume->save($params)) {
                    return $this->createOkResponse();
                }
                $messages = $resume->getMessages();
            } else {
                $messages = $validator->getMessages();
            }
        }

        $form = new ResumesForm($resume, $options);

        $form->setFormId('resume_form');

        $form->renderForm();
        $image = $resume->getUsers()->getImages();
        $imageTag =  '';
        if ($image instanceof Images) {
            $imageTag = '<image src="' . $uploadsDir . $image->getPath() .
                $image->getFileName() . '" style="width: 100%;"/>';
        }

        $html = $this->returnView(
            'resume',
            [
                'form' => $form,
                'image' => $imageTag,
                'messages' => $messages,
                'id' => $resume->getId()
            ],
            true
        );

        return $this->createArrayResponse($html, 'html');
    }


    /**
     * @param $id
     * @return \Phalcon\Http\Response | null
     * @throws \RuntimeException
     * @throws \ReflectionException
     * @throws Exception
     */
    public function updateResume($id): ?Response
    {
        $id = (int)$id;
        if (empty($id)) {
            return $this->createErrorResponse('Id is absent');
        }

        /** @var UsersService $userService */
        $userService = $this->userService;

        if (!$userService->getIdentity()) {
            return $this->createErrorResponse('Not authorized');
        }
        $resume = Resumes::find($id)[0];
        if (!$resume) {
            return $this->createErrorResponse('Resume not found');
        }
        $messages = [];

        $this->afterFind($resume);


        $role = $userService->getRole();
        $options = [];
        if (\in_array($role, [AclRoles::SUPERADMIN, AclRoles::ADMIN], true)) {
            $options = ['admin' => true];
        }

        $config = $this->getDI()->get(Services::CONFIG);
        $uploadsDir = $config->hostName;

        if ($this->request->isPost()) {
            $params = $this->request->getPost();

            $image = null;

            $validator = new ResumesValidator();
            $res = $validator->validate($params);
            if ($res->count() === 0) {
                /** @var Resumes $resume */
                $resume->beforeUpdate();
                $this->sanitizePostData($params);
                if ($resume->save($params)) {
                    $form = new ResumesForm($resume, $options);

                    $form->setFormId('resume_form');
                    $form->setShow(false);
                    $form->renderForm();
                    $image = $resume->getUsers()->getImages();
                    $imageTag =  '';
                    if ($image instanceof Images) {
                        $imageTag = '<image src="' . $uploadsDir . $image->getPath() .
                            $image->getFileName() . '" style="width: 100%;"/>';
                    }

                    $html = $this->returnView(
                        'resume',
                        [
                            'form' => $form,
                            'image' => $imageTag,
                            'messages' => $messages,
                            'id' => $resume->getId()
                        ],
                        true
                    );
                    return $this->createArrayResponse($html, 'html');
                }
            } else {
                $messages = $validator->getMessages();
            }

            $resume->refresh();
        }



        $form = new ResumesForm($resume, $options);

        $form->setFormId('resume_form');

        $form->renderForm();
        $image = $resume->getUsers()->getImages();
        $imageTag =  '';
        if ($image instanceof Images) {
            $imageTag = '<image src="' . $uploadsDir . $image->getPath() .
                $image->getFileName() . '" style="width: 100%;"/>';
        }

        $html = $this->returnView(
            'resume',
            [
                'form' => $form,
                'image' => $imageTag,
                'messages' => $messages,
                'id' => $resume->getId()
            ],
            true
        );
        return $this->createArrayResponse($html, 'html');

    }


    /**
     * @param $id
     * @return Response
     * @throws Exception
     */
    public function deleteResume($id): Response
    {
        $resume = Resumes::findFirst((int)$id);

        /** @var Users $me */
        $me = $this->userService->getDetails();

        if ($me) {
            return $this->createErrorResponse('Only for authorized users');
        }

        $user = $resume->getUsers();

        if ($user->getId() !== $me->getId() && !in_array($this->userService->getRole(), AclRoles::ADMIN_ROLES, true)) {
            return $this->createErrorResponse('You have no permission for this operation');
        }

        if (!$resume->delete()) {
            $mes = '';
            foreach ($user->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->createErrorResponse($mes);
        }

        return $this->createOkResponse();
    }


    /**
     * @param $page
     * @return mixed
     */
    public function listAllResumes($page)
    {
        $numberPage = (int)($page ?? 1);


        $count = Resumes::count();
        if ($count() === 0) {
            return $this->createErrorResponse('Not found');
        }

        /** @var Resultset $resumes */
        $resumes = Resumes::find();


        $paginator = new Paginator([
            'data' => $resumes,
            'limit'=> Limits::SEARCH_LIMIT,
            'page' => $numberPage
        ]);

        $page = $paginator->getPaginate();

        $items = $page->items;

        $_items = [];

        /** @var Resumes $item */
        foreach ($items as $item) {
            $_item = new \stdClass();
            $_item->id = $item->getId();
            $_item->user = $item->getUsers();
            $_item->cv = $item->getUploaded();
            $_item->currency = $item->getCurrency();
            $_item->salary = $item->getSalary();
            $_item->work_place = $item->getWorkPlace();
            $_item->certification = $item->getCertification();
            $_item->position = html_entity_decode($item->getPosition());
            $_item->professional_area = html_entity_decode($item->getProfessionalArea());
            $_item->key_skills = html_entity_decode($item->getKeySkills());
            $_item->language = html_entity_decode($item->getLanguage());
            $_item->about_me = html_entity_decode($item->getAboutMe());
            $_item->location = html_entity_decode($item->getLocation());
            $_item->date = $item->getCreationDate();
            $_item->modifiedDate = $item->getModifiedDate();

            $birthday = new \DateTime($_item->user->getBirthday());
            $now = new \DateTime();

            $diff = date_diff($now, $birthday);
            $_item->age = $diff->y;

            $_items[] = $_item;

        }

        $pagesInRange = $this->getPaginationRange($page);

        $data = [
            'resumes'       => $_items,
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
    public function listUserResumes($page)
    {
        $numberPage = (int)($page ?? 1);

        /** @var Resultset $resumes */


        /** @var UsersService $userService */
        $userService = $this->userService;

        if (!$userService->getIdentity()) {
            return $this->createErrorResponse('Not authorized');
        }


        $role = $userService->getRole();
        if (\in_array($role, AclRoles::ADMIN_ROLES, true)) {
            $resumes = Resumes::find();
        } else {
            $id = (int) $userService->getIdentity();
            $resumes = Resumes::find('user_id = ' . $id);
        }

        if ($resumes->count() === 0) {
            return $this->createErrorResponse('Not found');
        }

        $paginator = new Paginator([
            'data' => $resumes,
            'limit'=> Limits::SEARCH_LIMIT,
            'page' => $numberPage
        ]);

        $page = $paginator->getPaginate();

        $items = $page->items;

        $_items = [];

        /** @var Resumes $item */
        foreach ($items as $item) {
            $_item = new \stdClass();

            $_item->id = $item->getId();
            $_item->user = $item->getUsers();
            $_item->cv = $item->getUploaded();
            $_item->currency = $item->getCurrency();
            $_item->salary = $item->getSalary();
            $_item->work_place = $item->getWorkPlace();
            $_item->certification = $item->getCertification();
            $_item->position = html_entity_decode($item->getPosition());
            $_item->professional_area = html_entity_decode($item->getProfessionalArea());
            $_item->key_skills = html_entity_decode($item->getKeySkills());
            $_item->language = html_entity_decode($item->getLanguage());
            $_item->about_me = html_entity_decode($item->getAboutMe());
            $_item->location = html_entity_decode($item->getLocation());
            $_item->date = $item->getCreationDate();
            $_item->modifiedDate = $item->getModifiedDate();

            $birthday = new \DateTime($_item->user->getBirthday());
            $now = new \DateTime();

            $diff = date_diff($now, $birthday);
            $_item->age = $diff->y;

            $_items[] = $_item;
        }

        $pagesInRange = $this->getPaginationRange($page);

        $data = [
            'resumes'       => $_items,
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
     * @throws Exception
     */
    public function searchResume()
    {
        $params = $this->request->getQuery();

        if ($params['where'] === null) {
            $params['where'] = '';
        }

        if ($params['what'] === null) {
            $params['what'] = '';
        }

        $results = [];
        $results1 = [];


        $where = $this->sanitize($params['where']);
        $what = $this->sanitize($params['what']);
        $salary = (int) ($params['salary'] ?? 0);
        $currency = $params['currency'] ?? '';
        $type = $params['type'] ?? null;
        $_order = (int) ($params['order'] ?? 0);
        $offset = (int) ($params['page'] ?? 1);

        switch ($_order) {
            case 1:
                $order = 'creationDate';
                break;
            case 2:
                $order = 'creationDate DESC';
                break;
            case 3:
                $order = 'salary ASC';
                break;
            case 4:
                $order = 'salary DESC';
                break;
            default:
                $order = null;
        }

        if (!empty($what) && strlen($what) > 1) {
            $sql = "SELECT id,
                MATCH (`position`, `professional_area`, `about_me`, `certification`, `key_skills`, `location`) AGAINST ('{$what}' IN BOOLEAN MODE) as REL
                FROM `resumes`
                WHERE MATCH (`position`, `professional_area`, `about_me`, `certification`, `key_skills`, `location`) AGAINST ('{$what}' IN BOOLEAN MODE)
                ORDER BY REL;";

            $connection = $this->db;
            $res = $connection->query($sql);

            do {
                $row = $res->fetchArray();
                if ($row) {
                    $results[] = (int)$row['id'];
                }
            } while($row);
        }


        if (!empty($where) && strlen($where) > 1) {
            $sql1 = "SELECT id,
                MATCH (location) AGAINST ('{$where}' IN BOOLEAN MODE) as REL
                FROM `resumes`
                WHERE MATCH (location) AGAINST ('{$where}' IN BOOLEAN MODE)
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

        if ($what && $where) {
            $ids = array_intersect($results, $results1);
        } elseif ($what) {
            $ids = $results;
        } elseif ($where) {
            $ids = $results1;
        }


        $builder = new Builder();
        $builder->addFrom(Resumes::class);

        if (!empty($ids)) {
            $builder->inWhere('id', $ids);
        }

        if (!empty($salary)) {
            $builder->andWhere('[' . Resumes::class . '].[salary] >= :salary:', ['salary' => $salary]);
        }

        if (!empty($type) && in_array($type, ['insite', 'remote', 'part-time', 'full-time', 'project', 'volunteer'])) {
            $builder->andWhere('[' . Resumes::class . '].[work_place] = :type:', ['type' => '"' . $type . '"']);
        }

        if (!empty($currency) && in_array($currency, ['USD', 'EURO', 'GBP', 'BRL', 'TRY', 'PLN', 'SEK', 'JPY', 'CAD', 'AUD'])) {
            $builder->andWhere('[' . Resumes::class . '].[currency] = :currency:', ['currency' => '"' . $currency. '"']);
        }

        if (!empty($order)) {
            $builder->orderBy($order);
        }


        $options = [
            'builder' => $builder,
            'limit'   => Limits::SEARCH_LIMIT,
            'page'    => $offset,
            'adapter' => 'queryBuilder',
        ];

        $paginator = Factory::load($options);

        $page = $paginator->getPaginate();

        $items = $page->items;

        $_items = [];

        /** @var Resumes $item */
        foreach ($items as $item) {
            $_item = new \stdClass();

            $_item->id = $item->getId();
            $_item->user = $item->getUsers();
            $_item->cv = $item->getUploaded();
            $_item->currency = $item->getCurrency();
            $_item->salary = $item->getSalary();
            $_item->work_place = $item->getWorkPlace();
            $_item->certification = $item->getCertification();
            $_item->position = html_entity_decode($item->getPosition());
            $_item->professional_area = html_entity_decode($item->getProfessionalArea());
            $_item->key_skills = html_entity_decode($item->getKeySkills());
            $_item->language = html_entity_decode($item->getLanguage());
            $_item->about_me = html_entity_decode($item->getAboutMe());
            $_item->location = html_entity_decode($item->getLocation());
            $_item->date = $item->getCreationDate();
            $_item->modifiedDate = $item->getModifiedDate();

            $birthday = new \DateTime($_item->user->getBirthday());
            $now = new \DateTime();

            $diff = date_diff($now, $birthday);
            $_item->age = $diff->y;

            $_items[] = $_item;
        }

        $pagesInRange = $this->getPaginationRange($page);

        $data = [
            'resumes'       => $_items,
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
            'topInRange'    => $this->topInRange,
        ];

        return $this->createArrayResponse($data, 'data');
    }

    /**
     * @param $page
     * @return mixed
     */
    public function listInvited($page)
    {
        /** @var Service $userService */
        $userService = $this->userService;
        $id = $userService->getIdentity();
        $builder = new Builder();
        $builder->addFrom(Resumes::class);
        $builder->leftJoin(
            FavoriteResume::class,
            '[' . FavoriteResume::class . '].[resume_id] = [' . Resumes::class . '].[id]'
        );
        $builder->where('[' . FavoriteResume::class . '].[user_id] = :user:', ['user' => $id]);

        $options = [
            'builder' => $builder,
            'limit'   => Limits::SEARCH_LIMIT,
            'page'    => (int) $page,
            'adapter' => 'queryBuilder',
        ];

        $paginator = Factory::load($options);

        $page = $paginator->getPaginate();

        $items = $page->items;

        $_items = [];

        /** @var Resumes $item */
        foreach ($items as $item) {
            $_item = new \stdClass();

            $_item->id = $item->getId();
            $_item->user = $item->getUsers();
            $_item->cv = $item->getUploaded();
            $_item->currency = $item->getCurrency();
            $_item->salary = $item->getSalary();
            $_item->work_place = $item->getWorkPlace();
            $_item->certification = $item->getCertification();
            $_item->position = html_entity_decode($item->getPosition());
            $_item->professional_area = html_entity_decode($item->getProfessionalArea());
            $_item->key_skills = html_entity_decode($item->getKeySkills());
            $_item->language = html_entity_decode($item->getLanguage());
            $_item->about_me = html_entity_decode($item->getAboutMe());
            $_item->location = html_entity_decode($item->getLocation());
            $_item->date = $item->getCreationDate();
            $_item->modifiedDate = $item->getModifiedDate();

            $birthday = new \DateTime($_item->user->getBirthday());
            $now = new \DateTime();

            $diff = date_diff($now, $birthday);
            $_item->age = $diff->y;

            $_items[] = $_item;
        }

        $pagesInRange = $this->getPaginationRange($page);

        $data = [
            'resumes'       => $_items,
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


    /**************************  TEST *********************/

    /**
     *
     * @throws \ReflectionException
     */
    public function listResumes()
    {
        $this->returnView('list');
    }

    /**
     *
     * @throws \RuntimeException
     * @throws \ReflectionException
     * @throws Exception
     * @return Response | null
     */
    public function add(): ?Response
    {
        $resume = new Resumes();
        $messages = [];
        if ($this->request->isPost()) {
            $params = $this->request->getPost();
            $config = $this->getDI()->get(Services::CONFIG);
            $image = null;

            if ($this->request->hasFiles(true)) {
                $uploadDir = $config->application->uploadDir;

                if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755) && !is_dir($uploadDir)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $uploadDir));
                }
                /** @var \Phalcon\Http\Request\File $file */
                foreach ($this->request->getUploadedFiles(true) as $file) {
                    if ($file->getKey() === 'fileName') {
                        $fileName = uniqid('CV_' . date('Y-m-d') . '_', false);
                        $fileName .= '.' . $file->getExtension();
                        try {
                            $file->moveTo($uploadDir . $fileName);
                            $image = new Images();
                            $image->setFileName($fileName);
                            $image->setPath('/uploads/cv/');
                            $data = $image->toArray();
                            $imageValidator = new ImagesValidator();
                            $res = $imageValidator->validate($data);
                            if ($res->count() === 0) {
                                $image->save();
                            } else {
                                $messages = $imageValidator->getMessages();
                            }
                        } catch (\RuntimeException $exception) {
                            $messages['image_not_created'] = $exception->getMessage();
                        }
                    }
                }
            }
            unset($params['fileName']);
            if ($image instanceof Images) {
                $params['cv'] = $image->getId();
            }


            $validator = new ResumesValidator();
            $res = $validator->validate($params);
            if ($res->count() === 0) {
                $this->sanitizePostData($params);
                if ($resume->save($params)) {
                    return $this->response->redirect('/admin/resume/edit/' . $resume->getId());
                }
                $messages = $resume->getMessages();
            } else {
                $messages = $validator->getMessages();
            }
        }

        $form = new ResumesForm($resume);
        $form->renderForm();
        $this->returnView('add', compact('form', 'messages'));
        return null;
    }


    /**
     * @param $id
     * @return \Phalcon\Http\Response | null
     * @throws \RuntimeException
     * @throws \ReflectionException
     * @throws Exception
     */
    public function updates($id): ?Response
    {
        $id = (int)$id;
        if (empty($id)) {
            return $this->response->redirect('/admin/resume/list');
        }
        $resume = Resumes::find($id)[0];
        if (!$resume) {
            return $this->response->redirect('/admin/resume/list');
        }
        $messages = [];

        $this->afterFind($resume);

        if ($this->request->isPost()) {
            $params = $this->request->getPost();

            $config = $this->getDI()->get(Services::CONFIG);
            $image = null;

            if ($this->request->hasFiles(true)) {
                $uploadDir = $config->application->uploadDir;

                if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755) && !is_dir($uploadDir)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $uploadDir));
                }
                /** @var \Phalcon\Http\Request\File $file */
                foreach ($this->request->getUploadedFiles(true) as $file) {
                    if ($file->getKey() === 'fileName') {
                        $fileName = uniqid('CV_' . date('Y-m-d') . '_', false);
                        $fileName .= '.' . $file->getExtension();
                        try {
                            $file->moveTo($uploadDir . $fileName);
                            $image = new Images();
                            $image->setFileName($fileName);
                            $image->setPath('/uploads/cv/');
                            $data = $image->toArray();
                            $imageValidator = new ImagesValidator();
                            $res = $imageValidator->validate($data);
                            if ($res->count() === 0) {
                                $image->save();
                            } else {
                                $messages = $imageValidator->getMessages();
                            }
                        } catch (\RuntimeException $exception) {
                            $messages['image_not_created'] = $exception->getMessage();
                        }
                    }
                }
            }

            unset($params['fileName']);
            if ($image instanceof Images) {
                $params['cv'] = $image->getId();
            }


            $validator = new ResumesValidator();
            $res = $validator->validate($params);
            if ($res->count() === 0) {
                $this->sanitizePostData($params);
                /** @var Resumes $resume */
                $resume->save($params);
            } else {
                $messages = $validator->getMessages();
            }

            $resume->refresh();
        }

        $form = new ResumesForm($resume);


        $form->renderForm();


        $this->returnView(
            'updates',
            [
                'form' => $form,
                'messages' => $messages,
                'id' => $resume->getId()
            ]
        );
        return null;
    }

    /**
     * @param Model $item
     */
    protected function afterSave(Model $item): void
    {
        /** @var $item Resumes */
        parent::afterSave($item);

        $types = $this->request->getPost('type_of_job');

        foreach ($types as $name) {
            $type = JobTypes::findFirst("name = '{$name}'");
            $model = new ResumeJobTypes();
            $model->setResumeId($item->getId());
            $model->setTypeId($type->getId());
            $model->save();
        }
    }

    /**
     * @param Model $item
     */
    protected function afterUpdate(Model $item): void
    {
        /** @var $item Resumes */
        parent::afterUpdate($item);

        $id = $item->getId();
        $relations = ResumeJobTypes::find("resume_id = {$id}");
        $types = $this->request->getPost('type_of_job');

        $remained = [];

        $calculated = [];

        /** @var ResumeJobTypes $relation */
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
                $model = new ResumeJobTypes();
                $model->setResumeId($item->getId());
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

        /** @var Resumes $item */

        $items = ResumeJobTypes::find("resume_id = {$item->getId()}");

        foreach ($items as $entity) {
            $entity->delete();
        }
    }


}
