<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 23.09.17
 * Time: 11:49
 */

namespace App\Controllers;

use App\Constants\Services;
use App\Forms\RecoveryForm;
use Phalcon\DI\Injectable;

/**
 * {@inheritDoc}
 */
class DashboardController extends Injectable
{

    /**
     * DashboardController constructor.
     * @param $di
     */
    public function __construct($di)
    {
        $this->setDI($di);
    }

    /**
     *
     * @throws \ReflectionException
     */
    public function recovery(): string
    {
        /** @var \Phalcon\Mvc\View\Simple $view */
        $view = $this->getDI()->get(Services::VIEW);
        $form = new RecoveryForm();
        $form->setAction('/users/recovery');
        $form->renderForm();
        return $view->render('general/recovery', ['form' => $form]);
    }
}
