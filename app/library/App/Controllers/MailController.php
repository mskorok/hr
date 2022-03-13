<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Forms\MailForm;
use App\Model\Mail;
use App\Validators\MailValidator;
use App\Traits\RenderView;

/**
 * Class MailController
 * @package App\Controllers
 */
class MailController extends ControllerBase
{
    use RenderView;

    public static $encodedFields = [
        'name',
        'mailFrom',
        'mailTo',
        'subject',
        'text'
    ];

    /**
     *
     * @throws \ReflectionException
     */
    public function wpAdd()
    {
        $model = new Mail();
        $messages = [];

        $form = new MailForm($model);
        $form->setFormId('Mail_create_form');
        $form->setShow(false);

        $form->renderComplexAddForm();
        $this->returnView('wp_add', compact('form', 'messages'));
    }

    /**
     * @param $id
     * @return \Phalcon\Http\Response
     * @throws \ReflectionException
     */
    public function wpEdit($id)
    {
        $id = (int) $id;
        if (empty($id)) {
            return $this->createErrorResponse('ID not exist');
        }
        $model = Mail::findFirst($id);
        if (!$model) {
            return $this->createErrorResponse('Mail not found');
        }
        $messages = [];

        $form = new MailForm($model);
        $form->setFormId('Mail_edit_form');
        $form->setShow(false);

        $form->renderComplexEditForm();
        $this->returnView('wp_edit', compact('form', 'messages'));
    }

    /**
     * @param $id
     * @return \Phalcon\Http\Response
     * @throws \ReflectionException
     */
    public function wpShow($id)
    {
        $id = (int) $id;

        if (empty($id)) {
            return $this->createErrorResponse('ID not exist');
        }
        $model = Mail::findFirst($id);
        if (!$model) {
            return $this->createErrorResponse('Mail not found');
        }
        $messages = [];


        try {
            $form = new MailForm($model);
        } catch (\Exception $exception) {
            return $this->createErrorResponse($exception->getMessage());
        }
        $form->setFormId('Mail_show_form');

        $form->renderComplexShowForm();
        $this->returnView('wp_edit', compact('form', 'messages'));
    }

    /**
     * @param $data
     * @param $isUpdate
     * @return bool
     */
    protected function postDataValid($data, $isUpdate): bool
    {
        $validator = new MailValidator();
        $res = $validator->validate($data);
        $this->messages = $validator->getMessages();
        return $res->count() === 0;
    }

    /**
     * @param $id
     * @throws \RuntimeException
     * @throws \PhalconApi\Exception
     */
    protected function beforeHandleRemove($id)
    {
        $admin = $this->isAdminUser();
        if (!$admin) {
            throw new \RuntimeException('Only admin has permission to remove Mail');
        }
    }
}
