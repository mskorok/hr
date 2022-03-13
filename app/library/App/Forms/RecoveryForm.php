<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 25.09.17
 * Time: 8:52
 */

namespace App\Forms;

use Phalcon\Forms\Element\Text;

/**
 * Phalcon\Forms\Form
 *
 * This component allows to build forms using an object-oriented interface
 */
class RecoveryForm extends BaseForm
{

    public static $counter = 0;

    /**
     * RecoveryForm constructor.
     * @param null $entity
     * @param null $userOptions
     */
    public function __construct($entity = null, $userOptions = null)
    {
        $this->cnt = ++static::$counter;
        parent::__construct($entity, $userOptions);
    }

    public function initialize(): void
    {
        $email = new Text('email', [
            'class'   => 'form-control',
            'placeholder' => 'Email'
        ]);
        $email->setLabel('Email');

        $this->add($email);
    }
}
