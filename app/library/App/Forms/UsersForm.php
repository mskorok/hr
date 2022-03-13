<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 25.09.17
 * Time: 8:52
 */

namespace App\Forms;

use App\Model\Countries;
use App\Model\Users;
use Phalcon\Filter;
use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Date;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Email;
use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\File;

/**
 * Phalcon\Forms\Form
 *
 * This component allows to build forms using an object-oriented interface
 */
class UsersForm extends BaseForm
{
    private static $counter = 0;

    /**
     * @return string
     */
    protected function getMultipart(): string
    {
        return 'enctype="multipart/form-data"';
    }

    /**
     * UserForm constructor.
     * @param null $entity
     * @param null $userOptions
     */
    public function __construct($entity = null, $userOptions = null)
    {
        $this->cnt = ++static::$counter;
        $this->imageRelatedField = 'avatar';
        parent::__construct($entity, $userOptions);
    }

    /**
     * @param Users|null $model
     * @param array|null $options
     */
    public function initialize(Users $model = null, array $options = null): void
    {

        $this->cnt = (bool)($options['cnt'] ?? false);

        $this->admin = (bool) ($options['admin'] ?? false);

        $this->show = (bool) ($options['show'] ?? false);

        $this->add(
            new Hidden('id', ['id' => 'id_model_Users_counter_' . $this->cnt])
        );

        $firstName = new Text('name', [
            'class'   => 'form-control',
            'placeholder' => 'First Name',
            'id' => 'firstName_model_Users_counter_' . $this->cnt
        ]);
        $firstName->setLabel('Name&nbsp;<span style="color:red">*</span>');

        if ($this->show) {
            $firstName->setAttribute('disabled', 'disabled');
        }
        $this->add($firstName);

        $lastName = new Text('surname', [
            'class'   => 'form-control',
            'placeholder' => 'Last Name',
            'id' => 'lastName_model_Users_counter_' . $this->cnt
        ]);
        $lastName->setLabel('Surname&nbsp;<span style="color:red">*</span>');

        if ($this->show) {
            $lastName->setAttribute('disabled', 'disabled');
        }
        $this->add($lastName);

        $username = new Text('username', [
            'class'   => 'form-control',
            'placeholder' => 'username',
            'id' => 'username_model_Users_counter_' . $this->cnt
        ]);

        if ($this->show) {
            $username->setAttribute('disabled', 'disabled');
        }

        $username->setLabel('Username&nbsp;<span style="color:red">*</span>');
        $this->add($username);

        $email = new Email('email', [
            'class'   => 'form-control',
            'placeholder' => 'Email',
            'id' => 'email_model_Users_counter_' . $this->cnt
        ]);

        if ($this->show) {
            $email->setAttribute('disabled', 'disabled');
        }
        $email->setLabel('Email');
        $this->add($email);

//        $emailConfirmed = new Check('emailConfirmed');
//        $emailConfirmed->setLabel('Email Confirmed');
//        $emailConfirmed->addFilter('bool');
//        $emailConfirmed->setAttribute('id', 'emailConfirmed_model_Users_counter_' . $this->cnt);
//        $this->add($emailConfirmed);

        $aboutMe = new Text('about_me', [
            'class'   => 'form-control',
            'placeholder' => 'about me',
            'id' => 'about_me_model_Users_counter_' . $this->cnt
        ]);

        $aboutMe->setLabel('About me&nbsp;<span style="color:red">*</span>');

        if ($this->show) {
            $aboutMe->setAttribute('disabled', 'disabled');
        }
        $this->add($aboutMe);


        $gender =  new Select(
            'gender',
            [
                'male' => 'Male',
                'female' => 'Female',
                'not defined' => 'Not defined'
            ],
            [
                'id' => 'gender_model_Users_counter_' . $this->cnt
            ]
        );

        $gender->setLabel('Gender');
        if ($this->show) {
            $gender->setAttribute('disabled', 'disabled');
        }

        $gender->setAttribute('class', 'form-control');
        $this->add($gender);

        $role =  new Select(
            'role',
            [
                'manager' => 'Manager',
                'superadmin' => 'Superadmin',
                'employer' => 'Employer',
                'applicant' => 'Applicant',
                'admin' => 'Admin',
                'expert' => 'Expert',
                'author' => 'Author',
                'partner' => 'Partner'
            ],
            [
                'id' => 'role_model_Users_counter_' . $this->cnt
            ]
        );

        if ($this->show) {
            $role->setAttribute('disabled', 'disabled');
        }

        $role->setAttribute('class', 'form-control');

        $role->setLabel('Role');

        $status =  new Select(
            'status',
            [
                'pending' => 'Pending',
                'confirmed' => 'Confirmed',
                'rejected' => 'Rejected',
                'canceled' => 'Canceled'
            ],
            [
                'id' => 'status_model_Users_counter_' . $this->cnt
            ]
        );
        if ($this->show) {
            $status->setAttribute('disabled', 'disabled');
        }
        $status->setAttribute('class', 'form-control');
        $status->setLabel('Status');


        if (!($model instanceof Users) || empty($model->getId())) {
            $password = new Password('password', [
                'class'   => 'form-control',
                'placeholder' => 'Password'
            ]);
            $password->setLabel('Password&nbsp;<span style="color:red">*</span>');

            $confirmPassword = new Password('confirmPassword', [
                'class'   => 'form-control',
                'placeholder' => 'confirmPassword'
            ]);
            $confirmPassword->setLabel('Confirm Password&nbsp;<span style="color:red">*</span>');

            $this->add($password);

            $this->add($confirmPassword);
        } elseif ($this->admin) {
            $this->add($role);
            $this->add($status);
        }

        $date = new Date('birthday', [
            'class'   => 'form-control',
            'placeholder' => 'Birthday',
            'id' => 'birthday_model_Users_counter_' . $this->cnt
        ]);
        $date->setLabel('Birthday');
        $date->setDefault((new \DateTime())->format('Y-m-d'));

        $filter = new Filter();
        $filter->add('date', static function ($date) {
            return (new \DateTime($date))->format('Y-m-d');
        });

        if ($model && $model->getBirthday() !== null) {
            $date->setDefault((new \DateTime($model->getBirthday()))->format('Y-m-d'));

            $dateField = $filter->sanitize($model->getBirthday(), 'date');
            $model->setBirthday($dateField);
            $date->addValidator(new \Phalcon\Validation\Validator\Date())->addFilter('date');
        }
        if ($this->show) {
            $date->setAttribute('disabled', 'disabled');
        }
        $this->add($date);

        $github = new Text('github', [
            'class'   => 'form-control',
            'placeholder' => 'Github',
            'id' => 'github_model_Users_counter_' . $this->cnt
        ]);
        $github->setLabel('Github');

        if ($this->show) {
            $github->setAttribute('disabled', 'disabled');
        }
        $this->add($github);

        $linkedIn = new Text('linkedIn', [
            'class'   => 'form-control',
            'placeholder' => 'LinkedIn',
            'id' => 'linkedIn_model_Users_counter_' . $this->cnt
        ]);
        $linkedIn->setLabel('LinkedIn');

        if ($this->show) {
            $linkedIn->setAttribute('disabled', 'disabled');
        }
        $this->add($linkedIn);


        $fb = new Text('fb', [
            'class'   => 'form-control',
            'placeholder' => 'Facebook',
            'id' => 'fb_model_Users_counter_' . $this->cnt
        ]);
        $fb->setLabel('Facebook');

        if ($this->show) {
            $fb->setAttribute('disabled', 'disabled');
        }
        $this->add($fb);
        
        $hh = new Text('hh', [
            'class'   => 'form-control',
            'placeholder' => 'Head Hunter',
            'id' => 'hh_model_Users_counter_' . $this->cnt
        ]);
        $hh->setLabel('Head Hunter');

        if ($this->show) {
            $hh->setAttribute('disabled', 'disabled');
        }
        $this->add($hh);
        
        $phone = new Text('phone', [
            'class'   => 'form-control',
            'placeholder' => 'Phone',
            'id' => 'phone_model_Users_counter_' . $this->cnt
        ]);
        $phone->setLabel('Phone');

        if ($this->show) {
            $phone->setAttribute('disabled', 'disabled');
        }
        $this->add($phone);

        $skype = new Text('skype', [
            'class'   => 'form-control',
            'placeholder' => 'Skype',
            'id' => 'skype_model_Users_counter_' . $this->cnt
        ]);
        $skype->setLabel('Skype');

        if ($this->show) {
            $skype->setAttribute('disabled', 'disabled');
        }
        $this->add($skype);

        $image = new File('fileName');
//        $image->setLabel('Avatar');
        $image->setAttribute('class', 'hidden');
        $image->setAttribute('id', 'avatar_model_Users_counter_' . $this->cnt);
        if ($this->show) {
            $image->setAttribute('disabled', 'disabled');
        }
        $this->add($image);

        $country =  new Select(
            'country',
            Countries::find(),
            [
                'using' => [
                    'id',
                    'name'
                ],
                'id' => 'country_model_Users_counter_' . $this->cnt
            ]
        );
        $country->setLabel('Country');
        $country->setAttribute('class', 'form-control');

        if ($this->show) {
            $country->setAttribute('disabled', 'disabled');
        }
        $this->add($country);

        $city = new Text('city', [
            'class'   => 'form-control',
            'placeholder' => 'City',
            'id' => 'city_model_Users_counter_' . $this->cnt
        ]);
        $city->setLabel('City');

        if ($this->show) {
            $city->setAttribute('disabled', 'disabled');
        }
        $this->add($city);

        $address = new Text('address', [
            'class'   => 'form-control',
            'placeholder' => 'Address',
            'id' => 'address_model_Users_counter_' . $this->cnt
        ]);
        $address->setLabel('Address');

        if ($this->show) {
            $address->setAttribute('disabled', 'disabled');
        }
        $this->add($address);

        $language = new Text('language', [
            'class'   => 'form-control',
            'placeholder' => 'Language',
            'id' => 'language_model_Users_counter_' . $this->cnt
        ]);
        $language->setLabel('Language');

        if ($this->show) {
            $language->setAttribute('disabled', 'disabled');
        }
        $this->add($language);
    }
}
