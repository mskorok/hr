<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 25.09.17
 * Time: 8:52
 */

namespace App\Forms;

use Phalcon\Forms\Element\Password;

/**
 * Phalcon\Forms\Form
 *
 * This component allows to build forms using an object-oriented interface
 */
class RecoveryPasswordForm extends BaseForm
{

    public function initialize(): void
    {

        $password = new Password('password', [
            'class'   => 'form-control',
            'placeholder' => 'Password'
        ]);
        $password->setLabel('Password');

        $confirmPassword = new Password('confirmPassword', [
            'class'   => 'form-control',
            'placeholder' => 'confirmPassword'
        ]);
        $confirmPassword->setLabel('Confirm Password');

        $this->add($password);

        $this->add($confirmPassword);
    }
}
