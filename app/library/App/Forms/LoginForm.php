<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 25.09.17
 * Time: 8:52
 */

namespace App\Forms;

use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Text;

/**
 * Phalcon\Forms\Form
 *
 * This component allows to build forms using an object-oriented interface
 */
class LoginForm extends BaseForm
{

    public function initialize(): void
    {

        $name = new Text('username', [
            'class'   => 'form-control',
            'placeholder' => 'Username'
        ]);
        $name->setAttribute('required', 'required');
        $name->setLabel('Name');

        $password = new Password('password', [
            'class'   => 'form-control',
            'placeholder' => 'Password'
        ]);
        $password->setLabel('Password');
        $password->setAttribute('required', 'required');

        $remember = new Check('remember');
        $remember->setLabel('Remember');

        $this->add($name);

        $this->add($password);

        $this->add($remember);
    }
}
