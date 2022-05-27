<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Constants\AclRoles;
use App\Constants\Limits;
use App\Constants\Services;
use App\Forms\CompaniesForm;
use App\Model\CompanyManager;
use App\Model\Images;
use App\Model\Users;
use App\Services\UsersService;
use App\Traits\RenderView;
use App\Validators\CompaniesValidator;
use App\Validators\ImagesValidator;
use Exception;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Http\Response;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\View\Simple;
use Phalcon\Paginator\Adapter\Model as Paginator;
use App\Model\Companies;
use Phalcon\Mvc\Model\Query\Builder as QueryBuilder;
use Phalcon\Validation\Message;
use Phalcon\Validation\Message\Group;
use PhalconApi\Constants\PostedDataMethods;
use ReflectionException;
use RuntimeException;

/**
 * Class CompaniesController
 * @package App\Controllers
 */
class CompaniesController extends ControllerBase
{

    use RenderView;

    public static $availableIncludes = [
//        'CompanyManager',
//        'CompanySubscription',
        'Countries',
        'Subscriptions',
//        'Vacancies',
//        'Users',
        'Images'
    ];

    public  static $encodedFields = [
        'name',
        'address',
        'description',
        'phone',
        'email',
        'city',
        'reg',
        'site',
        'requisites'
    ];

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->returnView('index');
    }

    /**
     * Searches for companies
     */
    public function searchAction()
    {
        $numberPage = 1;
        $parameters = [];
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, Companies::class, $_POST);
            $parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery('page', 'int');
        }

        $parameters['order'] = 'id';

        $companies = Companies::find($parameters);
        if (\count($companies) === 0) {
            return $this->response->redirect('/admin/companies/index?notice=' . urlencode('The search did not find any company'));
        }

        /** @var Companies $company */
        foreach ($companies as $company) {
            $this->afterFind($company);
        }

        $paginator = new Paginator([
            'data' => $companies,
            'limit'=> Limits::SEARCH_LIMIT,
            'page' => $numberPage
        ]);

        $page = $paginator->getPaginate();

        $this->returnView('search', ['page' => $page, 'limit' => $paginator->getLimit()]);
    }

    /**
     * Searches for articles
     */
    public function listAction()
    {
        $numberPage = $this->request->getQuery('page', 'int');

        /** @var Resultset $companies */
        $companies = Companies::find();
        if ($companies->count() === 0) {
            return $this->response->redirect('/admin/companies/index?notice=' . urlencode('The search did not find any company'));
        }

        /** @var Companies $company */
        foreach ($companies as $company) {
            $this->afterFind($company);
        }


        $paginator = new Paginator([
            'data' => $companies,
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
     * @throws ReflectionException
     */
    public function editAction($id): ?ResponseInterface
    {
        if ($this->request->isPost()) {
            return $this->response->redirect('/admin/companies/index');
        }
        $company = Companies::findFirst((int)$id);
        if (!$company) {
            return $this->response->redirect('/admin/companies/index?notice=' . urlencode('company was not found'));
        }

        $this->view->id = $company->getId();

        $this->tag::setDefault('id', $company->getId());
        $this->tag::setDefault('name', $company->getName());
        $this->tag::setDefault('description', $company->getDescription());
        $this->tag::setDefault('address', $company->getAddress());
        $this->tag::setDefault('status', $company->getStatus());
        $this->tag::setDefault('phone', $company->getPhone());
        $this->tag::setDefault('email', $company->getEmail());
        $this->tag::setDefault('city', $company->getCity());
        $this->tag::setDefault('country', $company->getCountry());
        $this->tag::setDefault('avatar', $company->getAvatar());
        $this->tag::setDefault('reg', $company->getReg());
        $this->tag::setDefault('site', $company->getSite());
        $this->tag::setDefault('requisites', $company->getRequisites());
        $this->returnView('edit');
        return null;
    }

    /**
     * Creates a new company
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            return $this->response->redirect('/admin/companies/index');
        }

        /** @var UsersService $userService */
        $userService = $this->userService;

        if (!$userService->getIdentity()) {
            return $this->createErrorResponse('Not authorized');
        }

        $company = new Companies();
        $company->setName($this->request->getPost('name'));
        $company->setDescription($this->request->getPost('description'));
        $company->setAddress($this->request->getPost('address'));
        $company->setStatus($this->request->getPost('status'));
        $company->setPhone($this->request->getPost('phone'));
        $company->setEmail($this->request->getPost('email', 'email'));
        $company->setCity($this->request->getPost('city'));
        $company->setCountry($this->request->getPost('country'));
        $company->setAvatar($this->request->getPost('avatar'));
        $company->setReg($this->request->getPost('reg'));
        $company->setSite($this->request->getPost('site'));
        $company->setRequisites($this->request->getPost('requisites'));
        $company->beforeCreate();

        $this->transformModelBeforeSave($company);

        if (!$company->save()) {
            $mes = implode('', $company->getMessages());

            return $this->response->redirect('/admin/companies/index?notice=' . urlencode($mes));
        }


        $manager = new CompanyManager();
        $manager->setCompanyId($company->getId());
        $manager->setUserId($userService->getIdentity());
        $manager->save();

        return $this->response->redirect('/admin/companies/index?success=' . urlencode('company was created successfully'));
    }

    /**
     * Saves a company edited
     *
     */
    public function saveAction()
    {
        if (!$this->request->isPost()) {
            return $this->response->redirect('/admin/companies/index');
        }

        /** @var UsersService $userService */
        $userService = $this->userService;

        if (!$userService->getIdentity()) {
            return $this->createErrorResponse('Not authorized');
        }

        $id = $this->request->getPost('id');
        $company = Companies::findFirst((int)$id);

        if (!$company) {
            return $this->response->redirect('/admin/companies/index?notice=' . urlencode('company does not exist ' . $id));
        }

        $company->setName($this->request->getPost('name'));
        $company->setDescription($this->request->getPost('description'));
        $company->setAddress($this->request->getPost('address'));
        $company->setStatus($this->request->getPost('status'));
        $company->setPhone($this->request->getPost('phone'));
        $company->setEmail($this->request->getPost('email', 'email'));
        $company->setCity($this->request->getPost('city'));
        $company->setCountry($this->request->getPost('country'));
        $company->setAvatar($this->request->getPost('avatar'));
        $company->setReg($this->request->getPost('reg'));
        $company->setSite($this->request->getPost('site'));
        $company->setRequisites($this->request->getPost('requisites'));
        $company->beforeUpdate();

        $this->transformModelBeforeSave($company);

        if (!$company->save()) {
            $mes = implode('', $company->getMessages());

            return $this->response->redirect('/admin/companies/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/companies/index?success=' . urlencode('company was updated successfully'));
    }

    /**
     * @param $id
     * @return ResponseInterface
     */
    public function deleteAction($id): ResponseInterface
    {
        $company = Companies::findFirst((int)$id);
        if (!$company) {
            return $this->response->redirect('/admin/companies/index?notice=' . urlencode('company was not found'));
        }

        if (!$company->delete()) {
            $mes = implode('', $company->getMessages());

            return $this->response->redirect('/admin/companies/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/companies/index?success=' . urlencode('company was deleted successfully'));
    }


    /**
     *
     */
    public function listCompanies()
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
        $company = new Companies();
        $messages = [];

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
        if ($this->request->isPost()) {
            $company = new Companies();
            $params = $this->request->getPost();
            $config = $this->getDI()->get(Services::CONFIG);
            $image = null;

            if ($this->request->hasFiles(true)) {
                $uploadDir = $config->application->uploadDir;

                if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755) && !is_dir($uploadDir)) {
                    throw new RuntimeException(sprintf('Directory "%s" was not created', $uploadDir));
                }
                /** @var \Phalcon\Http\Request\File $file */
                foreach ($this->request->getUploadedFiles(true) as $file) {
                    if ($file->getKey() === 'fileName') {
                        $fileName = uniqid('Company_' . date('Y-m-d') . '_', false);
                        $fileName .= '.' . $file->getExtension();
                        try {
                            $file->moveTo($uploadDir . $fileName);
                            $image = new Images();
                            $image->setFileName($fileName);
                            $image->setPath('/uploads/');
                            $data = $image->toArray();
                            $imageValidator = new ImagesValidator();
                            $res = $imageValidator->validate($data);
                            if ($res->count() === 0) {
                                $image->save();
                            } else {
                                $messages = $imageValidator->getMessages();
                            }
                        } catch (RuntimeException $exception) {
                            $messages['image_not_created'] = $exception->getMessage();
                        }
                    }
                }
            }
            unset($params['fileName']);
            if ($image instanceof Images) {
                $params['avatar'] = $image->getId();
            }

            $validator = new CompaniesValidator();
            $res = $validator->validate($params);
            if ($res->count() === 0) {
                $company->beforeCreate();
                $this->sanitizePostData($params);
                if ($company->save($params)) {
                    $manager = new CompanyManager();
                    $manager->setCompanyId($company->getId());
                    $manager->setUserId($userService->getIdentity());
                    $manager->save();
                    return $this->response->redirect('/admin/company/edit/' . $company->getId());
                }
                $messages = $company->getMessages();
            } else {
                $messages = $validator->getMessages();
            }
        }
        $form = new CompaniesForm($company, $options);
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
            return $this->response->redirect('/admin/company/list');
        }
        $company = Companies::find($id)[0];
        if (!$company) {
            return $this->response->redirect('/admin/company/list');
        }

        $this->afterFind($company);

        $config = $this->getDI()->get(Services::CONFIG);

        $messages = [];

        if ($this->request->isPost()) {
            $params = $this->request->getPost();
            $config = $this->getDI()->get(Services::CONFIG);
            $image = null;

            if ($this->request->hasFiles(true)) {
                $uploadDir = $config->application->uploadDir;

                if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755) && !is_dir($uploadDir)) {
                    throw new RuntimeException(sprintf('Directory "%s" was not created', $uploadDir));
                }
                /** @var \Phalcon\Http\Request\File $file */
                foreach ($this->request->getUploadedFiles(true) as $file) {
                    if ($file->getKey() === 'fileName') {
                        $fileName = uniqid('Company_' . date('Y-m-d') . '_', false);
                        $fileName .= '.' . $file->getExtension();
                        try {
                            $file->moveTo($uploadDir . $fileName);
                            $image = new Images();
                            $image->setFileName($fileName);
                            $image->setPath('/uploads/');
                            $data = $image->toArray();
                            $imageValidator = new ImagesValidator();
                            $res = $imageValidator->validate($data);
                            if ($res->count() === 0) {
                                $image->save();
                            } else {
                                $messages = $imageValidator->getMessages();
                            }
                        } catch (RuntimeException $exception) {
                            $messages['image_not_created'] = $exception->getMessage();
                        }
                    }
                }
            }

            unset($params['fileName']);
            if ($image instanceof Images) {
                $params['avatar'] = $image->getId();
            }

            $validator = new CompaniesValidator();
            $res = $validator->validate($params);
            if ($res->count() === 0) {
                /** @var Companies $company */
                $company->beforeUpdate();
                $this->sanitizePostData($params);
                $company->save($params);
            } else {
                $messages = $validator->getMessages();
            }

            $company->refresh();
        }

        $form = new CompaniesForm($company);


        $form->renderForm();
        $image = $company->getAvatar();
        $uploadsDir = $config->hostName;
        $imageTag =  '';
        if ($image instanceof Images) {
            $imageTag = '<image src="' . $uploadsDir . $image->getPath() .
                $image->getFileName() . '" style="width: 130px;"/>';
        }

        $this->returnView(
            'updates',
            [
                'form' => $form,
                'image' => $imageTag,
                'messages' => $messages,
                'id' => $company->getId()
            ]
        );
        return null;
    }


    /************************************ SITE *************/


    /**
     * @param $id
     * @return Response | null
     * @throws RuntimeException
     * @throws ReflectionException
     * @throws Exception
     */
    public function showCompany($id): ?Response
    {
        $id = (int)$id;
        if (empty($id)) {
            return $this->createErrorResponse('Id is absent');
        }
        $company = Companies::find($id)[0];
        if (!$company) {
            return $this->createErrorResponse('Company not found');
        }
        $this->afterFind($company);

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
        $form = new CompaniesForm($company, $options);

        $form->setFormId('company_form');

        $form->renderForm();

        $image = $company->getImages();
        $config = $this->getDI()->get(Services::CONFIG);
        $uploadsDir = $config->hostName;
        $imageTag =  '';
        if ($image instanceof Images) {
            $imageTag = '<image src="' . $uploadsDir . $image->getPath() .
                $image->getFileName() . '" style="width: 130px;"/>';
        }

        $html = $this->returnViewWithEmptyLayout(
            'company',
            [
                'form' => $form,
                'image' => $imageTag,
                'messages' => [],
                'id' => $company->getId()
            ],
            true
        );
        return $this->createArrayResponse($html, 'html');

    }

    /**
     *
     * @throws RuntimeException
     * @throws ReflectionException
     * @throws Exception
     * @return Response|null|array
     */
    public function addCompany()
    {
        $company = new Companies();

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
        $config = $this->getDI()->get(Services::CONFIG);
        $messages = [];
        if ($this->request->isPost()) {
            $params = $this->request->getPost();
            $image = null;


            $validator = new CompaniesValidator();
            $res = $validator->validate($params);
            if ($res->count() === 0) {
                $company->beforeCreate();
                $this->sanitizePostData($params);
                $image = null;
                if ($this->request->hasFiles(true)) {

                    $uploadDir = $config->application->uploadDir;

                    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755) && !is_dir($uploadDir)) {
                        throw new RuntimeException(sprintf('Directory "%s" was not created', $uploadDir));
                    }
                    /** @var \Phalcon\Http\Request\File $file */
                    foreach ($this->request->getUploadedFiles(true) as $file) {
                        if ($file->getKey() === 'fileName') {
                            $fileName = uniqid('User_' . date('Y-m-d') . '_', false);
                            $fileName .= '.' . $file->getExtension();
                            try {
                                $file->moveTo($uploadDir . $fileName);
                                $image = new Images();
                                $image->setFileName($fileName);
                                $image->setPath('/uploads/');
                                $imageParams = $image->toArray();
                                $imageValidator = new ImagesValidator();
                                $res = $imageValidator->validate($imageParams);
                                if ($res->count() === 0) {
                                    $image->save();
                                } else {
                                    $this->messages = $imageValidator->getMessages();
                                }
                            } catch (RuntimeException $exception) {
                                $message = new Message($exception->getMessage());
                                if (!($this->messages instanceof Group)) {
                                    $this->messages = new Group;
                                    $this->messages->appendMessage($message);
                                }
                            }
                        }
                    }
                }

                unset($params['fileName']);
                if ($image instanceof Images) {
                    $params['avatar'] = $image->getId();
                }
                if ($company->save($params)) {
                    $manager = new CompanyManager();
                    $manager->setCompanyId($company->getId());
                    $manager->setUserId($userService->getIdentity());
                    $manager->save();

                    if (!\in_array($role, [AclRoles::SUPERADMIN, AclRoles::ADMIN], true)) {
                        $user = $userService->getDetails();
                        if ($user instanceof Users) {
                            $user->setRole(AclRoles::COMPANY_ADMIN);
                            $user->save();
                        }
                    }


                    return $this->createOkResponse();
                }
                $messages = $company->getMessages();
            } else {
                $messages = $validator->getMessages();
            }
        }

        $form = new CompaniesForm($company, $options);

        $form->setFormId('company_form');

        $form->renderForm();
        $image = $company->getImages();
        $imageTag =  '';
        $uploadsDir = $config->hostName;
        if ($image instanceof Images) {
            $imageTag = '<image src="' . $uploadsDir . $image->getPath() .
                $image->getFileName() . '" style="width: 130px;"/>';
        }

        $html = $this->returnViewWithEmptyLayout(
            'company',
            [
                'form' => $form,
                'image' => $imageTag,
                'messages' => $messages,
                'id' => $company->getId()
            ],
            true
        );

        return $this->createArrayResponse($html, 'html');
    }


    /**
     * @param $id
     * @return Response|null|array
     * @throws RuntimeException
     * @throws ReflectionException
     * @throws Exception
     */
    public function updateCompany($id)
    {
        $id = (int)$id;
        if (empty($id)) {
            return $this->createErrorResponse('Id is absent');
        }

        $config = $this->getDI()->get(Services::CONFIG);

        /** @var UsersService $userService */
        $userService = $this->userService;

        if (!$userService->getIdentity()) {
            return $this->createErrorResponse('Not authorized');
        }
        $company = Companies::find($id)[0];
        if (!$company) {
            return $this->createErrorResponse('Resume not found');
        }
        $messages = [];

        $this->afterFind($company);

        $role = $userService->getRole();
        $options = [];
        if (\in_array($role, [AclRoles::SUPERADMIN, AclRoles::ADMIN], true)) {
            $options = ['admin' => true];
        }

        if ($this->request->isPost()) {
            $params = $this->request->getPost();

            $image = null;

            $validator = new CompaniesValidator();
            $res = $validator->validate($params);
            if ($res->count() === 0) {
                /** @var Companies $company */
                $company->beforeUpdate();
                $this->sanitizePostData($params);

                $image = null;
                if ($this->request->hasFiles(true)) {
                    $config = $this->getDI()->get(Services::CONFIG);
                    $uploadDir = $config->application->uploadDir;

                    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755) && !is_dir($uploadDir)) {
                        throw new RuntimeException(sprintf('Directory "%s" was not created', $uploadDir));
                    }
                    /** @var \Phalcon\Http\Request\File $file */
                    foreach ($this->request->getUploadedFiles(true) as $file) {
                        if ($file->getKey() === 'fileName') {
                            $fileName = uniqid('User_' . date('Y-m-d') . '_', false);
                            $fileName .= '.' . $file->getExtension();
                            try {
                                $file->moveTo($uploadDir . $fileName);
                                $image = new Images();
                                $image->setFileName($fileName);
                                $image->setPath('/uploads/');
                                $imageParams = $image->toArray();
                                $imageValidator = new ImagesValidator();
                                $res = $imageValidator->validate($imageParams);
                                if ($res->count() === 0) {
                                    $image->save();
                                } else {
                                    $this->messages = $imageValidator->getMessages();
                                }
                            } catch (RuntimeException $exception) {
                                $message = new Message($exception->getMessage());
                                if (!($this->messages instanceof Group)) {
                                    $this->messages = new Group;
                                    $this->messages->appendMessage($message);
                                }
                            }
                        }
                    }
                }

                unset($params['fileName']);
                if ($image instanceof Images) {
                    $params['avatar'] = $image->getId();
                }
                if ($company->save($params)) {
                    $form = new CompaniesForm($company, $options);

                    $form->setFormId('company_form');
                    $form->setShow(false);

                    $form->renderForm();
                    $image = $company->getImages();
                    $uploadsDir = $config->hostName;
                    $imageTag =  '';
                    if ($image instanceof Images) {
                        $imageTag = '<image src="' . $uploadsDir . $image->getPath() .
                            $image->getFileName() . '" style="width: 130px;"/>';
                    }

                    $html = $this->returnViewWithEmptyLayout(
                        'company',
                        [
                            'form' => $form,
                            'image' => $imageTag,
                            'messages' => $messages,
                            'id' => $company->getId()
                        ],
                        true
                    );
                    return $this->createArrayResponse($html, 'html');
                }
            } else {
                $messages = $validator->getMessages();
            }

            $company->refresh();
        }



        $form = new CompaniesForm($company, $options);

        $form->setFormId('company_form');
        $form->setShow(false);

        $form->renderForm();
        $image = $company->getImages();
        $uploadsDir = $config->hostName;
        $imageTag =  '';
        if ($image instanceof Images) {
            $imageTag = '<image src="' . $uploadsDir . $image->getPath() .
                $image->getFileName() . '" style="width: 130px;"/>';
        }

        $html = $this->returnViewWithEmptyLayout(
            'company',
            [
                'form' => $form,
                'image' => $imageTag,
                'messages' => $messages,
                'id' => $company->getId()
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
    public function deleteCompany($id): Response
    {
        $company = Companies::findFirst((int)$id);

        /** @var Users $me */
        $me = $this->userService->getDetails();

        if ($me) {
            return $this->createErrorResponse('Only for authorized users');
        }

        $users = $company->getUsers();

        $ids = [];

        /** @var Users $user */
        foreach ($users as $user) {
            $ids[] = $user->getId();
        }

        if (!in_array($me->getId(), $ids, true) && !in_array($this->userService->getRole(), AclRoles::ADMIN_ROLES, true)) {
            return $this->createErrorResponse('You have no permission for this operation');
        }

        if (in_array($me->getId(), $ids, true)) {
            $manager = CompanyManager::findFirst(' user_id = ' . $me->getId() . ' AND company_id = ' . $company->getId() . ' ');
            if ($manager instanceof CompanyManager && $manager->delete()) {
                return $this->createOkResponse();
            }

            return $this->createErrorResponse('Something went wrong');
        }

        if (!$company->delete()) {
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
     * @throws \PhalconApi\Exception
     */
    public function listUserCompanies($page)
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
            /** @var Simple $collection */
            $collection = Companies::find();
        } else {
            /** @var Users $user */
            $user =  $userService->getDetails();
            $collection = $user->getCompanies();
        }



        $paginator = new Paginator([
            'data' => $collection,
            'limit'=> Limits::SEARCH_LIMIT,
            'page' => $numberPage
        ]);

        $page = $paginator->getPaginate();

        $items = $page->items;

        /** @var Companies $company */
        foreach ($items as $company) {
            $this->afterFind($company);

        }

        $pagesInRange = $this->getPaginationRange($page);

        $items = $this->getComplexArray($items);

        $data = [
            'companies'     => $items,
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


    /********    PROTECTED    *******/

    /**
     * @param $data
     * @return array
     * @throws RuntimeException
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
                throw new RuntimeException(sprintf('Directory "%s" was not created', $uploadDir));
            }
            /** @var \Phalcon\Http\Request\File $file */
            foreach ($this->request->getUploadedFiles(true) as $file) {
                if ($file->getKey() === 'fileName') {
                    $fileName = uniqid('User_' . date('Y-m-d') . '_', false);
                    $fileName .= '.' . $file->getExtension();
                    try {
                        $file->moveTo($uploadDir . $fileName);
                        $image = new Images();
                        $image->setFileName($fileName);
                        $image->setPath('/uploads/');
                        $params = $image->toArray();
                        $imageValidator = new ImagesValidator();
                        $res = $imageValidator->validate($params);
                        if ($res->count() === 0) {
                            $image->save();
                        } else {
                            $this->messages = $imageValidator->getMessages();
                        }
                    } catch (RuntimeException $exception) {
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
            $data['avatar'] = $image->getId();
        }

        if ($this->messages->count() > 0) {
            $messages = '';
            foreach ($this->messages as $message) {
                $messages .= $message->getMessage().PHP_EOL;
            }
            throw new RuntimeException($messages);
        }

        return parent::transformPostData($data);
    }


    /**
     *
     */
    protected function beforeHandleCreate()
    {
        $resource = $this->getResource();
        $resource->postedDataMethod(PostedDataMethods::POST);
    }

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
        $mes['Post-data is invalid'];
        foreach ($this->messages as $message) {
            $mes[] = $message->getMessage();
        }

        return $this->createErrorResponse($mes);
    }

    /**
     * @param $data
     * @param $isUpdate
     * @return bool
     */
    protected function postDataValid($data, $isUpdate): bool
    {
        $params = $data;
        if (isset($params['fileName'])) {
            unset($params['fileName']);
        }
        $validator = new CompaniesValidator();
        $res = $validator->validate($params);
        $this->messages = $validator->getMessages();
        if ($res->count() !== 0) {
            return false;
        }
        return $res->count() === 0;
    }

    /**
     * @param $id
     * @throws RuntimeException
     * @throws \PhalconApi\Exception
     */
    protected function beforeHandleRemove($id)
    {
        $admin = $this->isAdminUser();
        if (!$admin) {
            throw new RuntimeException('Only admin has permission to remove User');
        }
    }
}
