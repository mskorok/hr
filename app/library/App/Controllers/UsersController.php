<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Auth\UsernameAccountType;
use App\Constants\AclRoles;
use App\Constants\Limits;
use App\Constants\Services;
use App\Forms\LoginForm;
use App\Forms\RecoveryForm;
use App\Forms\RecoveryPasswordForm;
use App\Forms\UsersForm;
use App\Jobs\ConfirmationLetterJob;
use App\Jobs\RecoveryLetterJob;
use App\Jobs\ThankYouLetterJob;
use App\Mail\MailService;
use App\Model\Images;
use App\Model\MailSubscription;
use App\Services\UsersService;
use App\Traits\RenderView;
use App\Transformers\UsersTransformer;
use App\Validators\ImagesValidator;
use App\Validators\RecoveryPasswordValidator;
use App\Validators\UsersUpdateValidator;
use App\Validators\UsersValidator;
use Phalcon\DiInterface;
use Phalcon\Http\Response;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\View\Simple;
use Phalcon\Paginator\Adapter\Model as Paginator;
use App\Model\Users;
use Phalcon\Validation\Message;
use PhalconApi\Auth\Session;
use PhalconApi\Constants\PostedDataMethods;
use PhalconApi\Exception;
use Phalcon\Mvc\Model\Query\Builder as QueryBuilder;
use Phalcon\Validation\Message\Group;

/**
 * Class UsersController
 * @package App\Controllers
 */
class UsersController extends ControllerBase
{

    use RenderView;

    public static $availableIncludes = [
        'CompanyManager',
        'Deals',
        'Education',
        'Favorite',
        'Favorites',
        'Managers',
        'Partners',
        'PaymentUser',
        'ProfessionalExperiences',
        'Recipients',
        'Resumes',
        'Senders',
        'UserSubscription',
        'Images',
        'Companies',
        'Payments',
        'Subscriptions'
    ];

    public static $encodedFields = [
        'name',
        'surname',
        'username',
        'about_me',
        'github',
        'linkedIn',
        'fb',
        'hh',
        'phone',
        'email',
        'skype',
        'country',
        'city',
        'address',
        'language'
    ];

    /**
     * @var bool
     */
    protected $createMode = false;

    /**
     * @return mixed
     *
     */
    public function me()
    {
        try {
            $me = $this->userService->getDetails();
            return $this->createResourceResponse($me);
        } catch (Exception $exception) {
            return $this->createErrorResponse($exception->getMessage());
        }
    }

    /**
     * @return mixed
     * @throws \RuntimeException
     */
    public function authenticate()
    {
        $username = $this->request->getUsername();
        $password = $this->request->getPassword();

        try {
            $session = $this->authManager
                ->loginWithUsernamePassword(UsernameAccountType::NAME, $username, $password);
        } catch (Exception $exception) {
            throw new \RuntimeException($exception->getMessage());
        }

        $transformer = new UsersTransformer();
        $transformer->setModelClass(Users::class);

        /** @var Users $user */
        $user = Users::findFirst($session->getIdentity());
        if ($user instanceof Users) {
            $user->setLastLoginDate(date('Y-m-d H:i:s'));
            $this->transformModelBeforeSave($user);
            $user->save();
        }

        $role = $user->getRole();

        $response = [
            'token' => $session->getToken(),
            'expires' => $session->getExpirationTime(),
            'user' => $user,
            'avatar' => $user->getImages(),
            'role' => $role
        ];

        return $this->createArrayResponse($response, 'data');
    }

    /**
     * @return mixed
     */
    public function logout()
    {
        // Destroy the whole session
        $this->session->destroy();
        return $this->createOkResponse();
    }

    /**
     * @return mixed
     */
    public function recovery()
    {
        /** @var \Phalcon\Queue\Beanstalk $queue */
        $queue = $this->getDI()->get(Services::QUEUE);
        $email = $this->request->getPost('email');
        $user = Users::findFirst(['email = "' . $email . '"']);
        if ($user instanceof Users) {
            $job = new RecoveryLetterJob($queue, $user->getId(), null);
            $queue->put($job);
            return $this->createResponse(['result' => 'Password recovery letter has been sent']);
        }
        return $this->createResponse(['result' => 'Authentication error.']);
    }

    /**
     *
     * @throws \RuntimeException
     * @throws \ReflectionException
     */
    public function newPassword()
    {
        $messages = [];
        $recoveryToken = $this->request->getQuery('recovery_token');
        $test = $this->request->getQuery('test');
        if ($this->request->isPost()) {
            $token = $this->request->getQuery('recovery_token');
            if (!$token) {
                $token = $this->request->getPost('recovery_token');
            }
            $params = $this->request->getPost();
            $validator = new RecoveryPasswordValidator();
            $res = $validator->validate($params);
            if ($res->count() === 0) {
                $user = Users::findFirst(['token = "' . $token . '"']);
                if ($user instanceof Users) {
                    $user->setPassword($this->security->hash($params['password']));
                    $user->setToken(self::random(40));
                    $this->transformModelBeforeSave($user);
                    $user->save();
                    return $this->createResponse(['result' => 'Password successfully changed']);
                }
                throw new \RuntimeException('User not found');
            }
            $messages = $validator->getMessages();
            return $this->createArrayResponse($messages->toArray, 'messages');
        }
        if ($test) {
            /** @var Simple $view */
            $view = $this->getDI()->get(Services::VIEW);
            $form = new RecoveryPasswordForm();
            $form->setAction('/users/password?recovery_token=' . $recoveryToken);
            $form->renderForm();
            return $view->render('general/recovery', compact('form', 'messages'));
        }
        $token = $this->request->getQuery('recovery_token');
        $queryString = 'recovery_token=' . $token;
        $config = $this->getDI()->get(Services::CONFIG);
        return $this->response->redirect($config->clientHostName . $config->passwordRecoveryUri . '?' . $queryString);
    }

    /**
     * @return mixed
     * @throws \RuntimeException
     */
    public function confirm()
    {
        $params = $this->request->getQuery();

        $email = null;
        $token = null;

        if (\is_array($params) && array_key_exists('confirm_token', $params)) {
            $token = $params['confirm_token'];
        }
        if (\is_array($params) && array_key_exists('email', $params)) {
            $email = $params['email'];
        }

        $user = Users::findFirst([
            'email ="' . $email . '" AND token ="' . $token . '"'
        ]);
        if ($user instanceof Users) {
            $user->setEmailConfirmed(1);
            $this->transformModelBeforeSave($user);
            if ($user->save()) {
                /** @var \Phalcon\DiInterface $di */
                $di = $this->getDI();
                $queue = $this->getDI()->get(Services::QUEUE);
                if ($di instanceof DiInterface) {
                    $body = [
                        'recipient' => $user->getName() . ' ' . $user->getSurname(),
                        'to' => $user->getEmail()
                    ];
                    $job = new ThankYouLetterJob($queue, $user->getId(), $body);
                    $queue->put($job);
                }


                $duration = $this->authManager->getSessionDuration();
                $startTime = time();
                $accountTypeName = UsernameAccountType::NAME;
                $session = new Session($accountTypeName, $user->getId(), $startTime, $startTime + $duration);
                $token = $this->tokenParser->getToken($session);
                $session->setToken($token);
                $this->session = $session;
                $transformer = new UsersTransformer();
                $transformer->setModelClass(Users::class);

                $user = $this->createItemResponse($user, $transformer);

                $response = [
                    'token' => $session->getToken(),
                    'expires' => $session->getExpirationTime(),
                    'user' => $user
                ];

                return $this->createArrayResponse($response, 'data');
            }
            throw new \RuntimeException('User not saved');
        }
        throw new \RuntimeException('User not found');
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
     * Searches for users
     * @throws \ReflectionException
     */
    public function searchAction()
    {
        $numberPage = 1;
        $parameters = [];
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, Users::class, $_POST);
            $parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery('page', 'int');
        }


        $parameters['order'] = 'id';

        $users = Users::find($parameters);
        if (\count($users) === 0) {
            return $this->response->redirect('/admin/users/index?notice=' . urlencode('The search did not find any users'));
        }

        /** @var Users $user */
        foreach ($users as $user) {
            $this->afterFind($user);

        }

        $paginator = new Paginator([
            'data' => $users,
            'limit' => Limits::SEARCH_LIMIT,
            'page' => $numberPage
        ]);

        $page = $paginator->getPaginate();

        $this->returnView('search', ['page' => $page, 'limit' => $paginator->getLimit()]);
    }


    /**
     * Searches for users
     * @throws \ReflectionException
     */
    public function listAction()
    {
        $numberPage = $this->request->getQuery('page', 'int');

        /** @var Resultset $users */
        $users = Users::find();
        if ($users->count() === 0) {
            return $this->response->redirect('/admin/users/index?notice=' . urlencode('The search did not find any users'));
        }

        /** @var Users $user */
        foreach ($users as $user) {
            $this->afterFind($user);

        }

        $paginator = new Paginator([
            'data' => $users,
            'limit' => Limits::SEARCH_LIMIT,
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
     * @return null
     * @throws \ReflectionException
     */
    public function editAction($id)
    {
        if ($this->request->isPost()) {
            return $this->response->redirect('/admin/users/index');
        }

        $user = Users::findFirst((int)$id);
        if (!$user) {
            return $this->response->redirect('/admin/users/index?notice=' . urlencode('user was not found'));
        }


        $this->afterFind($user);


        $this->view->id = $user->getId();

        $this->tag::setDefault('id', $user->getId());
        $this->tag::setDefault('name', $user->getName());
        $this->tag::setDefault('surname', $user->getSurname());
        $this->tag::setDefault('username', $user->getUsername());
        $this->tag::setDefault('password', $user->getPassword());
        $this->tag::setDefault('birthday', $user->getBirthday());
        $this->tag::setDefault('github', $user->getGithub());
        $this->tag::setDefault('linkedIn', $user->getLinkedIn());
        $this->tag::setDefault('fb', $user->getFb());
        $this->tag::setDefault('hh', $user->getHh());
        $this->tag::setDefault('phone', $user->getPhone());
        $this->tag::setDefault('email', $user->getEmail());
        $this->tag::setDefault('skype', $user->getSkype());
        $this->tag::setDefault('country', $user->getCountry());
        $this->tag::setDefault('city', $user->getCity());
        $this->tag::setDefault('avatar', $user->getAvatar());
        $this->tag::setDefault('address', $user->getAddress());
        $this->tag::setDefault('token', $user->getToken());
        $this->tag::setDefault('language', $user->getLanguage());
        $this->tag::setDefault('status', $user->getStatus());
        $this->tag::setDefault('role', $user->getRole());

        $this->returnView('edit');
        return null;
    }

    /**
     * Creates a new user
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            return $this->response->redirect('/admin/users/index');
        }

        $user = new Users();
        $user->setId($this->request->getPost('id'));
        $user->setName($this->request->getPost('name'));
        $user->setSurname($this->request->getPost('surname'));
        $user->setUsername($this->request->getPost('username'));
        $user->setPassword($this->request->getPost('password'));
        $user->setBirthday($this->request->getPost('birthday'));
        $user->setGithub($this->request->getPost('github'));
        $user->setLinkedIn($this->request->getPost('linkedIn'));
        $user->setFb($this->request->getPost('fb'));
        $user->setHh($this->request->getPost('hh'));
        $user->setPhone($this->request->getPost('phone'));
        $user->setEmail($this->request->getPost('email', 'email'));
        $user->setSkype($this->request->getPost('skype'));
        $user->setCountry($this->request->getPost('country'));
        $user->setCity($this->request->getPost('city'));
        $user->setAvatar($this->request->getPost('avatar'));
        $user->setAddress($this->request->getPost('address'));
        $user->setToken($this->request->getPost('token'));
        $user->setLanguage($this->request->getPost('language'));
        $user->setStatus($this->request->getPost('status'));
        $user->setRole($this->request->getPost('role'));
        $user->beforeCreate();

        $this->transformModelBeforeSave($user);

        if (!$user->save()) {
            $mes = '';
            foreach ($user->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/users/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/users/index?success=' . urlencode('user was created successfully'));
    }

    /**
     * Saves a user edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            return $this->response->redirect('/admin/users/index');
        }

        $id = $this->request->getPost('id');
        $user = Users::findFirst((int)$id);

        if (!$user) {
            return $this->response->redirect('/admin/users/index?notice=' . urlencode('user does not exist ' . $id));
        }

        $user->setId($this->request->getPost('id'));
        $user->setName($this->request->getPost('name'));
        $user->setSurname($this->request->getPost('surname'));
        $user->setUsername($this->request->getPost('username'));
        $user->setPassword($this->request->getPost('password'));
        $user->setBirthday($this->request->getPost('birthday'));
        $user->setGithub($this->request->getPost('github'));
        $user->setLinkedIn($this->request->getPost('linkedIn'));
        $user->setFb($this->request->getPost('fb'));
        $user->setHh($this->request->getPost('hh'));
        $user->setPhone($this->request->getPost('phone'));
        $user->setEmail($this->request->getPost('email', 'email'));
        $user->setSkype($this->request->getPost('skype'));
        $user->setCountry($this->request->getPost('country'));
        $user->setCity($this->request->getPost('city'));
        $user->setAvatar($this->request->getPost('avatar'));
        $user->setAddress($this->request->getPost('address'));
        $user->setToken($this->request->getPost('token'));
        $user->setLanguage($this->request->getPost('language'));
        $user->setStatus($this->request->getPost('status'));
        $user->setRole($this->request->getPost('role'));
        $user->beforeUpdate();
        $this->transformModelBeforeSave($user);

        if (!$user->save()) {
            $mes = '';
            foreach ($user->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/users/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/users/index?success=' . urlencode('user was updated successfully'));
    }

    /**
     * @param $id
     * @return Response
     */
    public function deleteAction($id): Response
    {
        $user = Users::findFirst((int)$id);
        if (!$user) {
            return $this->response->redirect('/admin/users/index?notice=' . urlencode('user was not found'));
        }

        if (!$user->delete()) {
            $mes = '';
            foreach ($user->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/users/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/users/index?success=' . urlencode('user was deleted successfully'));
    }



    /***** PROTECTED  ******/

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
            $data['avatar'] = $image->getId();
        }

        if ($this->messages->count() > 0) {
            $messages = '';
            foreach ($this->messages as $message) {
                $messages .= $message->getMessage() . PHP_EOL;
            }
            throw new \RuntimeException($messages);
        }

        return parent::transformPostData($data);
    }

    /**
     * @param $id
     * @return null|Model
     */
    protected function getItem($id)
    {
        $user = Users::findFirst((int)$id);
        if ($user instanceof Users && $user->getEmailConfirmed() === 1) {
            return $user;
        }
        return null;
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

        $query->andWhere('emailConfirmed =  1');
    }

    /**
     * @param QueryBuilder $query
     * @param $id
     */
    protected function modifyFindQuery(QueryBuilder $query, $id)
    {
        if (!$this->createMode) {
            $query->andWhere('emailConfirmed =  1');
        }
        $this->createMode = false;
    }

    /**
     *
     */
    protected function beforeHandle()
    {
        $this->messages = new Group();
    }

    /**
     *
     */
    protected function beforeHandleCreate()
    {
        $this->createMode = true;
        $resource = $this->getResource();
        $resource->postedDataMethod(PostedDataMethods::POST);
    }

    /**
     * @param Model $item
     * @param $data
     */
    protected function beforeAssignData(Model $item, $data)
    {
        /** @var Users $user */
        $user = $item;
        if (isset($data['password'])) {
            $user->setPassword($this->security->hash($data['password']));
        }
        unset($data['confirmPassword'], $data['password']);
        if ($user->getId()) {
            $user->beforeUpdate();
        } else {
            $user->setToken(self::random(40));
            $user->beforeCreate();
            $user->setEmailConfirmed(0);
        }
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
        $validator = new UsersValidator();
        $res = $validator->validate($params);
        $this->messages = $validator->getMessages();
        if ($res->count() !== 0) {
            return false;
        }
        return $res->count() === 0;
    }

    /**
     * @param $id
     * @throws \RuntimeException
     * @throws Exception
     */
    protected function beforeHandleRemove($id)
    {
        $admin = $this->isAdminUser();
        if (!$admin) {
            throw new \RuntimeException('Only admin has permission to remove User');
        }
    }

    /**
     * @param Model $createdItem
     * @param $data
     * @param $response
     * @return void
     */
    protected function afterHandleCreate(Model $createdItem, $data, $response): void
    {
        /** @var $createdItem Users */

        /** @var \Phalcon\DiInterface $di */
        $di = $this->getDI();
        /** @var \Phalcon\Queue\Beanstalk $queue */
        $queue = $this->getDI()->get(Services::QUEUE);
        if ($di instanceof DiInterface) {
            /** @var MailService $mail */
//            $mail = $di->get(Services::MAIL);
            /** @var Users $createdItem */
//            $config = $this->getDI()->get(Services::CONFIG);
//            $mail->sendConfirmationLetter($createdItem, $config);
            $job = new ConfirmationLetterJob($queue, $createdItem->getId(), null);
            $queue->put($job);
        }
    }

    /**************************  TEST *********************/

    /**
     *
     * @throws \ReflectionException
     */
    public function listUsers()
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
        $user = new Users();
        $messages = [];
        if ($this->request->isPost()) {
            $user = new Users();
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
                        $fileName = uniqid('User_' . date('Y-m-d') . '_', false);
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
                        } catch (\RuntimeException $exception) {
                            $messages['image_not_created'] = $exception->getMessage();
                        }
                    }
                }
            }
            unset($params['fileName']);
            if ($image instanceof Images) {
                $params['avatar'] = $image->getId();
            }

            $validator = new UsersValidator();
            $res = $validator->validate($params);
            if ($res->count() === 0) {
                $params['password'] = $this->security->hash($params['password']);
                unset($params['confirmPassword']);
                $params['emailConfirmed'] = 0;
                $user->beforeCreate();
                $this->sanitizePostData($params);
                if ($user->save($params)) {
                    return $this->response->redirect('/admin/user/list/');
                }
                $messages = $user->getMessages();
            } else {
                $messages = $validator->getMessages();
            }
        }
        /** @var UsersService $userService */
        $userService = $this->userService;
        $role = $userService->getRole();
        $options = [];
        if (\in_array($role, [AclRoles::SUPERADMIN, AclRoles::ADMIN], true)) {
            $options = ['admin' => true];
        }
        $form = new UsersForm($user, $options);
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
        $url = $this->router->getRewriteUri();
        if (empty($id)) {
            if (strpos($url, 'admin') !== false) {
                return $this->response->redirect('/admin/user/list');
            }
            return $this->createErrorResponse('Id is absent');
        }
        $user = Users::find($id)[0];
        if (!$user) {
            if (strpos($url, 'admin') !== false) {
                return $this->response->redirect('/admin/user/list');
            }

            return $this->createErrorResponse('User not found');
        }

        $this->afterFind($user);

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
                        $fileName = uniqid('User_' . date('Y-m-d') . '_', false);
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
                        } catch (\RuntimeException $exception) {
                            $messages['image_not_created'] = $exception->getMessage();
                        }
                    }
                }
            }

            unset($params['fileName'], $params['role']);
            if ($image instanceof Images) {
                $params['avatar'] = $image->getId();
            }

            $validator = new UsersValidator();
            $res = $validator->validate($params);
            if ($res->count() === 0) {
                /** @var Users $user */
                $user->beforeUpdate();
                $this->sanitizePostData($params);
                if ($user->save($params)) {
                    return $this->response->redirect('/admin/user/list/');
                }
            } else {
                $messages = $validator->getMessages();
            }

            $user->refresh();
        }
        /** @var UsersService $userService */
        $userService = $this->userService;
        $role = $userService->getRole();
        $options = [];
        if (\in_array($role, [AclRoles::SUPERADMIN, AclRoles::ADMIN], true)) {
            $options = ['admin' => true];
        }
        $form = new UsersForm($user, $options);


        $form->renderForm();
        $image = $user->getAvatar();
        $imageTag =  '';
        $config = $this->getDI()->get(Services::CONFIG);
        $uploadsDir = $config->hostName;
        if ($image instanceof Images) {
            $imageTag = '<image src="' . $uploadsDir . $image->getPath() .
                $image->getFileName() . '" style="width: 100%;"/>';
        }

        $this->returnView(
            'updates',
            [
                'form' => $form,
                'image' => $imageTag,
                'messages' => $messages,
                'id' => $user->getId()
            ]
        );
        return null;
    }

    /**************************  SITE *********************/


    /**
     * @param $id
     * @return mixed
     * @throws \RuntimeException
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function showProfile($id)
    {
        $id = (int)$id;
        if (empty($id)) {
            return $this->createErrorResponse('Id is absent');
        }
        $user = Users::find($id)[0];
        if (!$user) {
            return $this->createErrorResponse('User not found');
        }

        $this->afterFind($user);

        /** @var UsersService $userService */
        $userService = $this->userService;
        $role = $userService->getRole();
        $options = [];
        if (\in_array($role, AclRoles::ADMIN_ROLES, true)) {
            $options['admin'] = true;
        }
        $options['show'] = true;
        $form = new UsersForm($user, $options);


        $form->renderForm();
        $image = $user->getAvatar();
        $config = $this->getDI()->get(Services::CONFIG);
        $uploadsDir = $config->hostName;
        $imageTag =  '';
        if ($image instanceof Images) {
            $imageTag = '<image src="' . $uploadsDir . $image->getPath() .
                $image->getFileName() . '" style="width: 100%;"/>';
        }

        $config = $this->getDI()->get(Services::CONFIG);

        $view = new Simple();
        $template = $config->get('application')->viewsDir . '/users/profile.phtml';
        $html = $view->render(
            $template,
            [
                'form' => $form,
                'image' => $imageTag,
                'messages' => [],
                'id' => $user->getId()
            ]
        );
        return $this->createArrayResponse($html, 'html');

    }

    /**
     *
     * @throws \RuntimeException
     * @throws \ReflectionException
     * @throws \Exception
     * @return Response|null|array
     */
    public function addProfile()
    {
        $user = new Users();
        $messages = [];
        /** @var UsersService $userService */
        $userService = $this->userService;
        $role = $userService->getRole();
        $options = [];
        if (\in_array($role, [AclRoles::SUPERADMIN, AclRoles::ADMIN], true)) {
            $options = ['admin' => true];
        }

        $config = $this->getDI()->get(Services::CONFIG);


        if ($this->request->isPost()) {
            $user = new Users();
            $params = $this->request->getPost();


            $image = null;

            if ($this->request->hasFiles(true)) {
                $uploadDir = $config->application->uploadDir;

                if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755) && !is_dir($uploadDir)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $uploadDir));
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
                $params['avatar'] = $image->getId();
            }

            $validator = new UsersValidator();
            $res = $validator->validate($params);

            if ($res->count() === 0) {
                $params['password'] = $this->security->hash($params['password']);
                unset($params['confirmPassword']);
                $params['emailConfirmed'] = 0;
                $user->setToken(self::random(40));
                $user->beforeCreate();
                $this->sanitizePostData($params);
                if ($user->save($params)) {
                    /** @var \Phalcon\DiInterface $di */
                    $di = $this->getDI();
                    /** @var \Phalcon\Queue\Beanstalk $queue */
                    $queue = $this->getDI()->get(Services::QUEUE);
                    if ($di instanceof DiInterface) {
                        $job = new ConfirmationLetterJob($queue, $user->getId(), null);
                        $queue->put($job);
                    }

                    return $this->createOkResponse();
                }
                $messages = $user->getMessages();
            } else {
                $messages = $validator->getMessages();
            }

            if ($messages->count() > 0) {
                return $this->createErrorResponse($messages);
            }
        }

        $form = new UsersForm($user, $options);
        $form->renderForm();

        $view = new Simple();
        $template = $config->get('application')->viewsDir . '/users/profile.phtml';
        $html = $view->render($template, compact('form', 'messages'));

        return $this->createArrayResponse($html, 'html');
    }


    /**
     * @param $id
     * @return \Phalcon\Http\Response|null|array
     * @throws \RuntimeException
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function updateProfile($id)
    {
        $id = (int)$id;
        if (empty($id)) {
            return $this->createErrorResponse('Id is absent');
        }
        $user = Users::find($id)[0];
        if (!$user) {
            return $this->createErrorResponse('User not found');
        }
        $this->afterFind($user);

        $messages = [];

        $config = $this->getDI()->get(Services::CONFIG);

        /** @var UsersService $userService */
        $userService = $this->userService;
        $role = $userService->getRole();
        $options = [];
        if (\in_array($role, [AclRoles::SUPERADMIN, AclRoles::ADMIN], true)) {
            $options = ['admin' => true];
        }

        $config = $this->getDI()->get(Services::CONFIG);
        $uploadsDir = $config->hostName;

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
                        $fileName = uniqid('User_' . date('Y-m-d') . '_', false);
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
                        } catch (\RuntimeException $exception) {
                            $messages['image_not_created'] = $exception->getMessage();
                        }
                    }
                }
            }

            unset($params['fileName'], $params['role']);
            if ($image instanceof Images) {
                $params['avatar'] = $image->getId();
            }

            $validator = new UsersUpdateValidator();
            $res = $validator->validate($params);
            if ($res->count() === 0) {
                /** @var Users $user */
                $user->beforeUpdate();
                $this->transformModelBeforeSave($user);
                if ($user->save($params)) {
                    $form = new UsersForm($user, $options);
                    $form->setShow(false);
                    $form->renderForm();
                    $image = $user->getAvatar();
                    $imageTag =  '';
                    if ($image instanceof Images) {
                        $imageTag = '<image src="' . $uploadsDir . $image->getPath() .
                            $image->getFileName() . '" style="width: 100%;"/>';
                    }

                    $view = new Simple();
                    $template = $config->get('application')->viewsDir . '/users/profile.phtml';

                    $html = $view->render(
                        $template,
                        [
                            'form' => $form,
                            'image' => $imageTag,
                            'messages' => $messages,
                            'id' => $user->getId()
                        ]
                    );
                    return $this->createArrayResponse($html, 'html');
                }
            } else {
                $messages = $validator->getMessages();
            }

            if ($messages->count() > 0) {
                return $this->createErrorResponse($messages);
            }

            $user->refresh();
        }

        $form = new UsersForm($user, $options);

        $form->renderForm();
        $image = $user->getAvatar();
        $imageTag =  '';
        if ($image instanceof Images) {
            $imageTag = '<image src="' . $uploadsDir . $image->getPath() .
                $image->getFileName() . '" style="width: 100%;"/>';
        }

        $view = new Simple();
        $template = $config->get('application')->viewsDir . '/users/profile.phtml';

        $html = $view->render(
            $template,
            [
                'form' => $form,
                'image' => $imageTag,
                'messages' => $messages,
                'id' => $user->getId()
            ]
        );
        return $this->createArrayResponse($html, 'html');

    }


    /**
     * @param $id
     * @return Response
     * @throws Exception
     */
    public function deleteProfile($id): Response
    {
        $user = Users::findFirst((int)$id);

        $me = $this->userService->getDetails();

        if ($me) {
            return $this->createErrorResponse('Only for authorized users');
        }

        if ((int)$id !== $me->getId() && !in_array($this->userService->getRole(), AclRoles::ADMIN_ROLES, true)) {
            return $this->createErrorResponse('You have no permission for this operation');
        }

        if (!$user->delete()) {
            $mes = '';
            foreach ($user->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->createErrorResponse($mes);
        }

        return $this->createOkResponse();
    }


    /**
     * @return mixed
     * @throws \ReflectionException
     */
    public function profileLogin()
    {
        if ($this->request->isPost()) {
            $username = $this->request->getUsername();
            $password = $this->request->getPassword();

            try {
                $session = $this->authManager
                    ->loginWithUsernamePassword(UsernameAccountType::NAME, $username, $password);
            } catch (Exception $e) {
                throw new \RuntimeException($e->getMessage());
            }

            $transformer = new UsersTransformer();
            $transformer->setModelClass(Users::class);

            $user = Users::findFirst($session->getIdentity());
            if ($user instanceof Users) {
                $user->setLastLoginDate(date('Y-m-d H:i:s'));

                $this->transformModelBeforeSave($user);

                $user->save();
            }
            /** @var Users $user */
            $user = $this->createItemResponse($user, $transformer);

            $response = [
                'token' => $session->getToken(),
                'expires' => $session->getExpirationTime(),
                'user' => $user
            ];

            return $this->createArrayResponse($response, 'data');
        }
        $form = new LoginForm();
        $form->setFormId('login_form');
        $form->renderForm();


        $view = new Simple();

        $config = $this->getDI()->get(Services::CONFIG);

        $template = $config->get('application')->viewsDir . '/users/login.phtml';

        $html = $view->render(
            $template,
            [
                'form' => $form,
                'messages' => [],
            ]
        );
        return $this->createArrayResponse($html, 'html');
    }

    /**
     * @return mixed
     * @throws \ReflectionException
     */
    public function profileLoginRecovery()
    {
        $form = new RecoveryForm();
        $form->setFormId('login_recovery_form');
        $form->renderForm();
        try {
            $view = new Simple();

            $config = $this->getDI()->get(Services::CONFIG);

            $template = $config->get('application')->viewsDir . '/users/login_recovery.phtml';

            $html = $view->render(
                $template,
                [
                    'form' => $form,
                    'messages' => [],
                ]
            );
            return $this->createArrayResponse($html, 'html');
        } catch (\Exception $e) {
            return $this->createErrorResponse($e->getMessage());
        }
    }


    /**
     * @return mixed
     * @throws \ReflectionException
     */
    public function profileNewPassword()
    {
        $form = new RecoveryPasswordForm();
        $form->setFormId('new_password_form');
        $form->renderForm();
        try {
            $view = new Simple();

            $config = $this->getDI()->get(Services::CONFIG);

            $template = $config->get('application')->viewsDir . '/users/new_password.phtml';

            $html = $view->render(
                $template,
                [
                    'form' => $form,
                    'messages' => [],
                ]
            );
            return $this->createArrayResponse($html, 'html');
        } catch (\Exception $e) {
            return $this->createErrorResponse($e->getMessage());
        }
    }

    /**
     * @return mixed
     */
    public function subscribe()
    {
        if ($this->request->isPost()) {
            $params = $this->request->getPost();

            $email = $params['email'];
            $category = (int)$params['category'];

            $sub = new MailSubscription();
            $sub->setEmail($email);
            $sub->setCategoryId($category);

            try {
                $sub->save();
                return $this->createOkResponse();
            } catch (\RuntimeException $exception) {
                return $this->createErrorResponse($exception->getMessage());
            }
        }
        return $this->createErrorResponse('Wrong method');
    }

    /**
     * @return mixed
     */
    public function unsubscribe()
    {
        if ($this->request->isPost()) {
            $params = $this->request->getPost();

            $email = $params['email'];
            $category = (int)$params['category'];

            if (empty($email)) {
                return $this->createErrorResponse('Email not found');
            }

            if (isset($category)) {
                $condition = " email = '{$email}' AND category_id = {$category} ";
            } else {
                $condition = " email = '{$email}' ";
            }

            $sub = MailSubscription::findFirst($condition);

            if ($sub instanceof MailSubscription) {
                try {
                    $sub->delete();
                    return $this->createOkResponse();
                } catch (\RuntimeException $exception) {
                    return $this->createErrorResponse($exception->getMessage());
                }
            } else {
                return $this->createErrorResponse('Subscription not found');
            }


        }
        return $this->createErrorResponse('Wrong method');
    }


    /**
     * @param $item
     * @return mixed
     */
    protected function getFindResponse($item)
    {
        if (property_exists($item, 'birthday')) {
            $item->birthday = (new \DateTime($item->birthday))->format('Y-m-d');
        }

        return parent::getFindResponse($item);
    }
}
