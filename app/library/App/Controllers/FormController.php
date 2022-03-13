<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Constants\AclRoles;
use App\Constants\Services;
use App\Forms\ArticlesForm;
use App\Forms\CommentsForm;
use App\Forms\BaseForm;
use App\Forms\CompaniesForm;
use App\Forms\DealsForm;
use App\Forms\EducationForm;
use App\Forms\ExpertInfoForm;
use App\Forms\LoginForm;
use App\Forms\MailForm;
use App\Forms\ImagesForm;
use App\Forms\MessagesForm;
use App\Forms\PaymentsForm;
use App\Forms\RecoveryForm;
use App\Forms\RecoveryPasswordForm;
use App\Forms\ResumesForm;
use App\Forms\SkillsForm;
use App\Forms\SubscriptionsForm;
use App\Forms\UserSubscriptionForm;
use App\Forms\VacanciesForm;
use App\Forms\UsersForm;


use App\Model\Articles;
use App\Model\Comments;
use App\Model\Companies;
use App\Model\CompanySubscription;
use App\Model\CompanyManager;
use App\Model\Countries;
use App\Model\Deals;
use App\Model\Education;
use App\Model\ExpertInfo;
use App\Model\Favorites;
use App\Model\Images;
use App\Model\Mail;
use App\Model\Messages;
use App\Model\Payments;
use App\Model\ProfessionalExperience;
use App\Model\Resumes;
use App\Model\Skills;
use App\Model\Subscriptions;
use App\Model\Vacancies;
use App\Model\UserSubscription;
use App\Model\Users;

use App\Traits\Ajax;
use App\Traits\RenderView;
use App\Traits\SearchByRoles;
use App\User\Service;

use App\Validators\ImagesValidator;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Resultset\Simple;
use Phalcon\Validation;

/**
 * Class FormController
 * @package App\Controllers
 */
class FormController extends ControllerBase
{
    use RenderView, SearchByRoles;

    /**
     * @param $class
     * @param $counter
     * @param bool $show
     * @return mixed
     * @throws \ReflectionException
     */
    public function getForm($class, $counter, $show = true)
    {
        if (empty($class)) {
            return $this->createErrorResponse('Model not exist '. $class);
        }

        if (!is_numeric($counter) || (int) $counter < 0) {
            return $this->createErrorResponse('Counter not exist '. $counter);
        }

        $counter = (int) $counter;
        ++$counter;

        $show = (bool) $show;

        $id = $class.'_form_counter_'.$counter;

        $formClass = 'App\\Forms\\'.$class.'Form';
        $class = 'App\\Model\\'.$class;
        $model = new $class();
        if ($this->request->isPost()) {
            $params = $this->request->getPost();

            if (isset($params['setKey'], $params['key'])) {
                $method = $params['setKey'];
                $model->$method((int)$params['key']);
            }
        }

        /** @var BaseForm $form */
        $form = new $formClass($model, ['cnt' => $counter, 'show' => $show]);
        $form->setCnt($counter);
        $form->setFormId($id);
        $form->renderForm();
        echo $form->html;
    }

    /**
     * @return mixed
     * @throws \RuntimeException
     * @throws \ReflectionException
     * @throws \PhalconApi\Exception
     */
    public function createMainForm()
    {
        if ($this->request->isPost()) {
            $params = $this->request->getPost();

            if (!array_key_exists('model', $params)) {
                return $this->createErrorResponse('Model not exist');
            }

            if (array_key_exists('counter', $params)
                && is_numeric($params['counter'])
                && (int) $params['counter'] >= 0
            ) {
                $counter = (int) $params['counter'];
                ++$counter;
            } else {
                $counter = null;
            }


            if (array_key_exists('stringData', $params) && $params['stringData'] === '') {
                unset($params['stringData']);
            }

            if (array_key_exists('integerData', $params) && $params['integerData'] === '') {
                unset($params['integerData']);
            }

            if (array_key_exists('boolData', $params) && $params['boolData'] === '') {
                unset($params['boolData']);
            }
//TODO check
//            if (isset($params['provider_id'])) {
//                $provider = Users::findFirst((int)$params['provider_id']);
//                if ($provider instanceof Users) {
//                    $pending = (int)$provider->getPending();
//                    if ($pending !== 0) {
//                        throw new \RuntimeException('Provider is not approved!');
//                    }
//                    $role = $provider->getRole();
//                    if (!\in_array($role, [AclRoles::ACTIVITY_PROVIDER, AclRoles::JOURDAY_PROVIDER], true)) {
//                        throw new \RuntimeException('Provider must have Activity or Jourday Provider role!');
//                    }
//                    if ($role === AclRoles::ACTIVITY_PROVIDER) {
//                        $params['beneficiary_id'] = null;
//                    }
//                    if ($role === AclRoles::JOURDAY_PROVIDER && (!isset($params['beneficiary_id'])
//                            || $params['beneficiary_id'] === ''
//                            || $params['beneficiary_id'] === 'null')
//                    ) {
//                        $params['beneficiary_id'] = $this->getAdminUserId();
//                    }
//                } else {
//                    throw new \RuntimeException('Provider is not found!');
//                }
//            }



//            if (array_key_exists('beneficiary_id', $params)) {
//                if ($params['beneficiary_id'] === '') {
//                    $params['beneficiary_id'] = null;
//                } elseif (is_numeric($params['beneficiary_id'])) {
//                    $params['beneficiary_id'] = (int) $params['beneficiary_id'];
//                } else {
//                    $user = Users::findFirst([
//                        'email = "' . $params['beneficiary_id'] . '"'
//                    ]);
//                    if ($user instanceof Users) {
//                        $params['beneficiary_id'] =  $user->getId();
//                    } else {
//                        $params['beneficiary_id'] = null;
//                    }
//                }
//            }

//            if (array_key_exists('affiliate', $params)) {
//                if ($params['affiliate'] === '') {
//                    $params['affiliate'] = null;
//                } elseif (is_numeric($params['affiliate'])) {
//                    $params['affiliate'] = (int) $params['affiliate'];
//                } else {
//                    $user = User::findFirst([
//                        'email = "' . $params['affiliate'] . '"'
//                    ]);
//                    if ($user instanceof User) {
//                        $params['affiliate'] =  $user->getId();
//                    } else {
//                        $params['affiliate'] = null;
//                    }
//                }
//            }

            if (array_key_exists('recipient', $params)) {
                if ($params['recipient'] === '') {
                    $params['recipient'] = null;
                } elseif (is_numeric($params['recipient'])) {
                    $params['recipient'] = (int) $params['recipient'];
                } else {
                    $user = Users::findFirst([
                        'email = "' . $params['recipient'] . '"'
                    ]);
                    if ($user instanceof Users) {
                        $params['recipient'] =  $user->getId();
                    } else {
                        $params['recipient'] = null;
                    }
                }
            }

            $show = true;

            if (array_key_exists('show', $params)) {
                if ($params['show'] === '') {
                    $params['show'] = true;
                } elseif ((int) $params['show'] === 0) {
                    $params['show'] = false;
                } else {
                    $params['show'] = true;
                }
                $show = $params['show'];
            }

            $class = 'App\\Model\\'.$params['model'];
            $validatorClass = 'App\\Validators\\' . $params['model']  . 'Validator';
            $formClass = $formClass = 'App\\Forms\\' . $params['model'] . 'Form';

            if (isset($params['id']) && !empty($params['id'])) {
                $model = $class::findFirst((int)$params['id']);
            } else {
                $model = new $class();
            }
            /** @var Model $model */
            unset($params['model']);
            if (!$model) {
                return $this->createErrorResponse('Model not found');
            }

            $formId = $params['formId'] ?? 'main_form';

            /** @var Validation $validator */
            $validator = new $validatorClass();
            if (!$validator) {
                return $this->createErrorResponse('validator not found');
            }
            $res = $validator->validate($params);
            if ($res->count() === 0) {
                $model->save($params);
                $model->refresh();
                /** @var Service $userService */
                $userService = $this->userService;
                $role = $userService->getRole();
                if (!$role) {
                    throw new \RuntimeException('User must be authorized');
                }
                $user = $userService->getDetails();
                $userId = $user instanceof Users ? $user->getId() : null;
                $option = [
                    'show' => $show,
                    'cnt' => $counter,
                    'role' => $role
                ];
                //TODO check
//                if ($role === AclRoles::AFFILIATE) {
//                    $option = [
//                        'show' => $show,
//                        'cnt' => $counter,
//                        'role' => $role,
//                        'affiliate' => $userId
//                    ];
//                }
//                if (\in_array($role, [AclRoles::ACTIVITY_PROVIDER, AclRoles::JOURDAY_PROVIDER], true)) {
//                    $option = [
//                        'show' => $show,
//                        'cnt' => $counter,
//                        'role' => $role,
//                        'provider' => $userId
//                    ];
//                }

                /** @var BaseForm $form */
                $form = new $formClass($model, $option);
                $form->setShow($show);
                $form->setFormId($formId);
                $form->renderForm();
                $html = $form->html;
                return $this->createArrayResponse(['success' => $html], 'api-key');
            }
            $messages = [];
            foreach ($res as $message) {
                $messages[] = $message->getMessage();
            }
            return $this->createErrorResponse($messages);
        }
    }

    /**
     * @return mixed
     * @throws \ReflectionException
     */
    public function createForm()
    {
        if ($this->request->isPost()) {
            $params = $this->request->getPost();

            if (array_key_exists('counter', $params)
                && is_numeric($params['counter'])
                && (int) $params['counter'] >= 0
            ) {
                $counter = (int) $params['counter'];
                ++$counter;
            } else {
                $counter = null;
            }


            if (!array_key_exists('model', $params)) {
                return $this->createErrorResponse('Model not exist');
            }

            if (!array_key_exists('relatedId', $params)) {
                return $this->createErrorResponse('relatedId not exist');
            }
            $class = 'App\\Model\\'.$params['model'];
            $validatorClass = 'App\\Validators\\' . $params['model'] . 'Validator';
            $formClass = $formClass = 'App\\Forms\\' . $params['model'] . 'Form';
            $relatedId = $params['relatedId'];
            unset($params['relatedId'], $params['model']);
            /** @var Model $model */
            if (isset($params['id']) && !empty($params['id'])) {
                $model = $class::findFirst((int)$params['id']);
            } else {
                $model = new $class($params);
            }

            $show = true;

            if (array_key_exists('show', $params)) {
                if ($params['show'] === '') {
                    $params['show'] = true;
                } elseif ((int) $params['show'] === 0) {
                    $params['show'] = false;
                } else {
                    $params['show'] = true;
                }
                $show = $params['show'];
            }

            $option = ['show' => $show];
            if ($counter !== null) {
                $option = ['cnt' => $counter, 'show' => $show];
            }

            if (!$model) {
                return $this->createErrorResponse('Model not found');
            }

            $rel = property_exists($model, $relatedId) ? $model->$relatedId : null;

            $rel = $rel !== 'null' ? $rel : null;

            if (!$rel) {
                return $this->createErrorResponse('Related ID is empty');
            }

            /** @var Validation $validator */
            $validator = new $validatorClass();
            if (!$validator) {
                return $this->createErrorResponse('validator not found');
            }

            $res = $validator->validate($params);
            if ($res->count() === 0) {
                /** @var Model $model */
                $model->save();
                $model->refresh();
                /** @var BaseForm $form */
                $form = new $formClass($model, $option);
                $form->setShow(false);
                $form->renderForm();
                $html = $form->html;
                return $this->createArrayResponse(['success' => $html], 'api-key');
            }
            $messages = [];
            foreach ($res as $message) {
                $messages[] = $message->getMessage();
            }
            return $this->createErrorResponse($messages);
        }
    }


    /**
     * @return mixed
     * @throws \ReflectionException
     */
    public function createRelatedForm()
    {
        if ($this->request->isPost()) {
            $params = $this->request->getPost();

            if (array_key_exists('counter', $params)
                && is_numeric($params['counter'])
                && (int) $params['counter'] >= 0
            ) {
                $counter = (int) $params['counter'];
                ++$counter;
            } else {
                $counter = null;
            }

            $show = true;

            if (array_key_exists('show', $params)) {
                if ($params['show'] === '') {
                    $params['show'] = true;
                } elseif ((int) $params['show'] === 0) {
                    $params['show'] = false;
                } else {
                    $params['show'] = true;
                }
                $show = $params['show'];
            }

            $option = ['show' => $show];
            if ($counter !== null) {
                $option = ['cnt' => $counter, 'show' => $show];
            }

            if (!array_key_exists('model', $params)) {
                return $this->createErrorResponse('Model not exist');
            }

            if (!array_key_exists('related', $params)) {
                return $this->createErrorResponse('Related Model not exist');
            }

            if (!array_key_exists('mainField', $params) || empty($params['mainField'])) {
                return $this->createErrorResponse('Main Field not exist');
            }

            if (!array_key_exists('mainId', $params) || empty($params['mainId'])) {
                return $this->createErrorResponse('Main Id not exist');
            }
            if (!array_key_exists('modelField', $params) || empty($params['modelField'])) {
                return $this->createErrorResponse('Model Field not exist');
            }

            $relatedModel = null;

            $modelField = $params['modelField'];

            $mainId = $params['mainId'];

            $mainField = $params['mainField'];

            $relatedClass = 'App\\Model\\'.$params['related'];



            $class = 'App\\Model\\'.$params['model'];
            $validatorClass = 'App\\Validators\\' . $params['model'] . 'Validator';
            $formClass = $formClass = 'App\\Forms\\' . $params['model'] . 'Form';
            unset($params['mainField'], $params['modelField'], $params['mainId'], $params['related'], $params['model']);
            /** @var Model $model */
            if (isset($params['id']) && !empty($params['id'])) {
                $model = $class::findFirst((int)$params['id']);
                if ($model instanceof Model) {
                    $condition = $modelField . ' = ' . $model->getId() . ' AND ' . $mainField . ' = ' . $mainId;
                    /** @var Model $relatedModel */
                    $relatedModel = $relatedClass::findFirst([$condition]);
                } else {
                    $model = new $class();
                }
            } else {
                $model = new $class();
            }

            if (!$model) {
                return $this->createErrorResponse('Model not found');
            }



            /** @var Validation $validator */
            $validator = new $validatorClass();
            if (!$validator) {
                return $this->createErrorResponse('validator not found');
            }

            $res = $validator->validate($params);
            if ($res->count() === 0) {
                $model->save($params);

                $data[$mainField] = $mainId;
                $data[$modelField] = $model->getId();
                /** @var Model $relatedModel */
                if ($relatedModel) {
                    $relArray = explode('_', $modelField);
                    $relMethod = 'set';
                    foreach ($relArray as $item) {
                        $relMethod .= ucfirst($item);
                    }
                    $relatedModel->$relMethod($model->getId());
                } else {
                    $relatedModel = new $relatedClass($data);
                    if (!$relatedModel) {
                        return $this->createErrorResponse('Related model not created');
                    }
                }



                try {
                    $relatedModel->save();
                    $relatedModel->refresh();
                } catch (\RuntimeException $exception) {
                    return $this->createErrorResponse('the error 1 '.$exception->getMessage());
                }

                /** @var BaseForm $form */
                $form = new $formClass($model, $option);
                $form->renderForm();
                $html = $form->html;
                return $this->createArrayResponse(
                    ['success' => $html],
                    'api-key'
                );
            }
            $messages = [];
            foreach ($res as $message) {
                $messages[] = $message->getMessage();
            }
            return $this->createErrorResponse($messages);
        }
    }

    /**
     * @return mixed
     * @throws \ReflectionException
     */
    public function createRelatedImageForm()
    {
        if ($this->request->isPost()) {
            $params = $this->request->getPost();


            if (array_key_exists('counter', $params)
                && is_numeric($params['counter'])
                && (int) $params['counter'] >= 0
            ) {
                $counter = (int) $params['counter'];
                ++$counter;
            } else {
                $counter = null;
            }

            $show = true;

            if (array_key_exists('show', $params)) {
                if ($params['show'] === '') {
                    $params['show'] = true;
                } elseif ((int) $params['show'] === 0) {
                    $params['show'] = false;
                } else {
                    $params['show'] = true;
                }
                $show = $params['show'];
            }

            $option = ['show' => $show];
            if ($counter !== null) {
                $option = ['cnt' => $counter, 'show' => $show];
            }

            if (!array_key_exists('model', $params)) {
                return $this->createErrorResponse('Model not exist');
            }

            if (!array_key_exists('related', $params)) {
                return $this->createErrorResponse('Related not exist');
            }

            if (!array_key_exists('mainField', $params) || empty($params['mainField'])) {
                return $this->createErrorResponse('Main Field not exist');
            }

            if (!array_key_exists('mainId', $params) || empty($params['mainId'])) {
                return $this->createErrorResponse('Main Id not exist');
            }
            if (!array_key_exists('modelField', $params) || empty($params['modelField'])) {
                return $this->createErrorResponse('Model Field not exist');
            }

            $showControls = array_key_exists('showControls', $params) && !empty($params['showControls']);

            $relatedModel = null;

            $related = $params['related'];

            $modelField = $params['modelField'];

            $mainId = $params['mainId'];

            $mainField = $params['mainField'];

            unset($params['mainField'], $params['modelField'], $params['mainId']);

            $relatedClass = 'App\\Model\\'.$related;
            $relatedValidatorClass = 'App\\Validators\\' . $related . 'Validator';

            $config = $this->getDI()->get(Services::CONFIG);

            if (isset($params['id']) && !empty($params['id'])) {
                $image = Images::findFirst((int)$params['id']);
                if ($image instanceof Images) {
                    $condition = $modelField . ' = ' . $image->getId() . ' AND ' . $mainField . ' = ' . $mainId;
                    /** @var Model $relatedModel */
                    $relatedModel = $relatedClass::findFirst([$condition]);
                } else {
                    $image = new Images();
                }
            } else {
                $image = new Images();
            }

            if (!$image) {
                return $this->createErrorResponse('Model not found');
            }

            if ($this->request->hasFiles(true)) {
                $uploadDir = $config->application->uploadDir;

                $messages = [];
                if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755) && !is_dir($uploadDir)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $uploadDir));
                }

                $images = [];
                $i = 0;
                /** @var \Phalcon\Http\Request\File $file */
                foreach ($this->request->getUploadedFiles(true) as $file) {
                    ++$i;
                    if ($i > 1) {
                        continue;
                    }
                    if ($file->getKey() === 'fileName') {
                        $fileName = uniqid('Image_related_'.date('Y-m-d').'_', false);
                        $fileName .= '.'.$file->getExtension();
                        try {
                            $file->moveTo($uploadDir . $fileName);
                            $image->setFileName($fileName);
                            $image->setPath('/uploads/');
                            $data = $image->toArray();
                            $imageValidator = new ImagesValidator();

                            /** @var \Phalcon\Validation\Message\Group $res */
                            $res = $imageValidator->validate($data);

                            if ($res->count() === 0) {
                                if ($image->save()) {
                                    $images[] = $image;
                                }
                            } else {
                                $array = $imageValidator->getMessages();
                                /** @var Validation\Message $message */
                                foreach ($array as $message) {
                                    $messages[] = $message->getMessage();
                                }
                            }
                        } catch (\RuntimeException $e) {
                            $messages[] = $e->getMessage();
                        }
                    }
                }

                unset($image);
                $formImages = [];
                if (\count($images) > 0) {
                    $data = [];
                    foreach ($images as $image) {
                        if ($image instanceof Images && $image->getId() !== null) {
                            $data[$mainField] = (int) $mainId;
                            $data[$modelField] = $image->getId();

                            /** @var Model $relatedModel */
                            if ($relatedModel) {
                                $relArray = explode('_', $modelField);
                                $relMethod = 'set';
                                foreach ($relArray as $item) {
                                    $relMethod .= ucfirst($item);
                                }
                                $relatedModel->$relMethod($image->getId());
                            } else {
                                $relatedModel = new $relatedClass($data);
                                if (!$relatedModel) {
                                    return $this->createErrorResponse('Related model not created');
                                }
                            }

                            /** @var Validation $validator */
                            $validator = new $relatedValidatorClass();

                            $res = $validator->validate($data);

                            if (($res->count() === 0) && $relatedModel->save()) {
                                $formImages[] = $image;
                            } else {
                                $array = $relatedModel->getMessages();
                                foreach ($array as $message) {
                                    $messages[] = $message->getMessage();
                                }
                            }
                        }
                    }
                }


                if (\count($formImages) > 0) {
                    $html = '';
                    foreach ($formImages as $image) {
                        /** @var BaseForm $form */
                        $form = new ImagesForm($image, $option);
                        $form->setIsImage(true);
                        if ($showControls) {
                            $form->setShowImage(true);
                        }
                        $form->renderImageForm();
                        $html .= $form->html;
                    }
                    return $this->createArrayResponse(
                        ['success' => $html, 'message' => $messages],
                        'api-key'
                    );
                }
                return $this->createErrorResponse($messages);
            }

            return $this->createErrorResponse('$_FILES is empty');
        }
    }



    /**
     * @return mixed
     * @throws \ReflectionException
     */
    public function createWithImageForm()
    {
        if ($this->request->isPost()) {
            $params = $this->request->getPost();


            if (array_key_exists('counter', $params)
                && is_numeric($params['counter'])
                && (int) $params['counter'] >= 0
            ) {
                $counter = (int) $params['counter'];
                ++$counter;
            } else {
                $counter = null;
            }

            $show = true;

            if (array_key_exists('show', $params)) {
                if ($params['show'] === '') {
                    $params['show'] = true;
                } elseif ((int) $params['show'] === 0) {
                    $params['show'] = false;
                } else {
                    $params['show'] = true;
                }
                $show = $params['show'];
            }

            $option = ['show' => $show];
            if ($counter !== null) {
                $option = ['cnt' => $counter, 'show' => $show];
            }

            if (!array_key_exists('model', $params)) {
                return $this->createErrorResponse('Model not exist');
            }

            if (!array_key_exists('relatedId', $params)) {
                return $this->createErrorResponse('relatedId not exist');
            }
            $class = 'App\\Model\\'.$params['model'];
            $validatorClass = 'App\\Validators\\' . $params['model'] . 'Validator';
            $formClass = $formClass = 'App\\Forms\\' . $params['model'] . 'Form';
            $mainField = $params['relatedId'];
            unset($params['relatedId'], $params['model']);
            /** @var Model $model */

            $config = $this->getDI()->get(Services::CONFIG);
            $messages = [];
            $imageField = null;
            if (array_key_exists('imageField', $params) && $this->request->hasFiles(true)) {
                $imageField = $params['imageField'];
                unset($params['imageField']);
                $uploadDir = $config->application->uploadDir;

                if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755) && !is_dir($uploadDir)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $uploadDir));
                }

                $images = [];
                $image = null;
                /** @var \Phalcon\Http\Request\File $file */
                foreach ($this->request->getUploadedFiles(true) as $file) {
                    if ($file->getKey() === 'fileName') {
                        $fileName = uniqid('Image_'.date('Y-m-d').'_', false);
                        $fileName .= '.'.$file->getExtension();
                        try {
                            $file->moveTo($uploadDir . $fileName);
                            $image = new Images();
                            $image->setFileName($fileName);
                            $image->setPath('/uploads/');
                            $data = $image->toArray();
                            $imageValidator = new ImagesValidator();

                            /** @var \Phalcon\Validation\Message\Group $res */
                            $res = $imageValidator->validate($data);

                            if ($res->count() === 0) {
                                if ($image->save()) {
                                    $images[] = $image;
                                }
                            } else {
                                $array = $imageValidator->getMessages();
                                /** @var Validation\Message $message */
                                foreach ($array as $message) {
                                    $messages[] = $message->getMessage();
                                }
                            }
                        } catch (\RuntimeException $e) {
                            $messages[] = $e->getMessage();
                        }
                    }
                }
                if (\count($images) > 0 && $images[0] instanceof Images) {
                    $params[$imageField] = $images[0]->getId();
                }
            }


            /** @var Validation $validator */
            $validator = new $validatorClass();
            if (!$validator) {
                return $this->createErrorResponse('validator not found');
            }
            $res = $validator->validate($params);
            if (isset($params['id']) && !empty($params['id'])) {
                $model = $class::findFirst((int)$params['id']);
                $model->assign($params);
            } else {
                $model = new $class($params);
            }

            if (!$model) {
                return $this->createErrorResponse('Model not found');
            }

            $rel = property_exists($model, $mainField) ? $model->$mainField : null;

            $rel = $rel !== 'null' ? $rel : null;

            if (!$rel) {
                return $this->createErrorResponse('Related ID is empty');
            }
            if ($res->count() === 0) {
                /** @var Model $model */
                $model->save();
                $model->refresh();
                /** @var BaseForm $form */
                $form = new $formClass($model, $option);
                $form->renderForm($imageField);
                $html = $form->html;
                return $this->createArrayResponse(['success' => $html], 'api-key');
            }
            $messages = [];
            foreach ($res as $message) {
                $messages[] = $message->getMessage();
            }
            return $this->createErrorResponse($messages);
        }
    }

    /**
     * @return mixed
     */
    public function deleteRelated()
    {
        if ($this->request->isPost()) {
            $params = $this->request->getPost();
            if (!array_key_exists('model', $params) || empty($params['model'])) {
                return $this->createErrorResponse('Model not exist');
            }

            if (!array_key_exists('related', $params) || empty($params['related'])) {
                return $this->createErrorResponse('Related Model not exist');
            }

            if (!array_key_exists('modelId', $params) || empty($params['modelId'])) {
                return $this->createErrorResponse('Model Id not exist');
            }

            if (!array_key_exists('mainId', $params) || empty($params['mainId'])) {
                return $this->createErrorResponse('Main Id not exist');
            }

            if (!array_key_exists('modelField', $params) || empty($params['modelField'])) {
                return $this->createErrorResponse('Model field not exist');
            }
            if (!array_key_exists('mainField', $params) || empty($params['mainField'])) {
                return $this->createErrorResponse('Main field not exist');
            }

            $class = $params['model'];
            $relatedClass = $params['related'];
            $modelId = (int) $params['modelId'];
            $mainId = (int) $params['mainId'];
            $modelField = $params['modelField'];
            $mainField = $params['mainField'];



            $model = 'App\\Model\\'.$class;
            $relatedModel = 'App\\Model\\'.$relatedClass;

            $condition = $modelField . ' = ' . $modelId . ' AND ' . $mainField . ' = ' . $mainId;

            $related = $relatedModel::findFirst([$condition]);

            $entity = $model::findFirst($modelId);

            if ($related instanceof Model && $entity instanceof Model) {
                $messages = [];
                if ($related->delete()) {
                    if ($entity->delete()) {
                        return $this->createOkResponse();
                    }
                    foreach ($entity->getMessages() as $message) {
                        $messages[] = $message->getMessage();
                    }
                    return $this->createArrayResponse($messages, 'model_error');
                }
                foreach ($related->getMessages() as $message) {
                    $messages[] = $message->getMessage();
                }
                return $this->createArrayResponse($messages, 'related_error');
            }
//            return $this->createArrayResponse(
//                [
//                    'model' => serialize($entity),
//                    'related' => serialize($related),
//                    'message' => 'Something went wrong'
//                ],
//                'key_error'
//            );
            return $this->createErrorResponse('Something went wrong');
        }
    }
}
