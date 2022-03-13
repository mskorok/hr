<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 25.09.17
 * Time: 8:52
 */

namespace App\Forms;

use App\Model\Companies;
use App\Model\Countries;
use App\Model\Users;
use Phalcon\Forms\Element\Email;
use Phalcon\Forms\Element\File;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;

/**
 * Phalcon\Forms\Form
 *
 * This component allows to build forms using an object-oriented interface
 */
class CompaniesForm extends BaseForm
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
     * CompaniesForm constructor.
     * @param null $entity
     * @param null $userOptions
     */
    public function __construct($entity = null, $userOptions = null)
    {
        $this->cnt = ++static::$counter;
        $this->imageRelatedField = 'avatar';
        parent::__construct($entity, $userOptions);
        $this->formId = 'company_form';
    }

    /**
     * @param Companies|null $model
     * @param array|null $options
     */
    public function initialize(Companies $model = null, array $options = null): void
    {
        if (isset($options['cnt'])) {
            $this->cnt = $options['cnt'];
        }
        if (isset($options['admin'])) {
            $this->admin = (bool)$options['admin'];
        }

        $this->add(
            new Hidden('id', ['id' => 'id_model_Companies_counter_' . $this->cnt])
        );

        $name = new Text('name', [
            'class' => 'form-control',
            'placeholder' => 'Name',
            'id' => 'name_model_Companies_counter_' . $this->cnt
        ]);
        $name->setLabel('Name');
        $name->setAttribute('required', 'required');
        $this->add($name);

        $description = new Text('description', [
            'class' => 'form-control',
            'placeholder' => 'Description',
            'id' => 'description_model_Companies_counter_' . $this->cnt
        ]);
        $description->setLabel('Description');
        $description->setAttribute('required', 'required');
        $this->add($description);

        $address = new Text('address', [
            'class' => 'form-control',
            'placeholder' => 'Address',
            'id' => 'address_model_Companies_counter_' . $this->cnt
        ]);
        $address->setLabel('Address');
        $address->setAttribute('required', 'required');
        $this->add($address);



        $phone = new Text('phone', [
            'class' => 'form-control',
            'placeholder' => 'Phone',
            'id' => 'phone_model_Companies_counter_' . $this->cnt
        ]);
        $phone->setLabel('Phone');
        $phone->setAttribute('required', 'required');
        $this->add($phone);

        $email = new Email('email', [
            'class' => 'form-control',
            'placeholder' => 'Email',
            'id' => 'email_model_Companies_counter_' . $this->cnt
        ]);
        $email->setLabel('Email');
        $this->add($email);

        $city = new Text('city', [
            'class' => 'form-control',
            'placeholder' => 'City',
            'id' => 'city_model_Companies_counter_' . $this->cnt
        ]);
        $city->setLabel('City');
        $city->setAttribute('required', 'required');
        $this->add($city);

        $country = new Select(
            'country',
            Countries::find(),
            [
                'using' => [
                    'id',
                    'name'
                ],
                'id' => 'country_model_Companies_counter_' . $this->cnt
            ]
        );
        $country->setLabel('Country');
        $country->setAttribute('class', 'form-control');
        $this->add($country);

        $reg = new Text('reg', [
            'class' => 'form-control',
            'placeholder' => 'Registration ID',
            'id' => 'reg_model_Companies_counter_' . $this->cnt
        ]);
        $reg->setLabel('Registration ID');
        $reg->setAttribute('required', 'required');
        $this->add($reg);

        $site = new Text('site', [
            'class' => 'form-control',
            'placeholder' => 'Site',
            'id' => 'site_model_Companies_counter_' . $this->cnt
        ]);
        $site->setLabel('Site');
        $site->setAttribute('required', 'required');
        $this->add($site);

        $requisites = new Text('requisites', [
            'class' => 'form-control',
            'placeholder' => 'Requisites',
            'id' => 'requisites_model_Companies_counter_' . $this->cnt
        ]);
        $requisites->setLabel('Requisites');
        $requisites->setAttribute('required', 'required');
        $this->add($requisites);

        $image = new File('fileName');
        $image->setLabel('Avatar');
        $image->setAttribute('class', '');
        $image->setAttribute('id', 'avatar_model_Companies_counter_' . $this->cnt);
        $this->add($image);

        if ($model instanceof Companies && $model->getId()) {
            if ($this->admin) {
                $status = new Select(
                    'status',
                    [
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'rejected' => 'Rejected',
                        'canceled' => 'Canceled'
                    ],
                    [
                        'id' => 'status_model_Companies_counter_' . $this->cnt
                    ]
                );
                $status->setLabel('Status');
                $status->setAttribute('class', 'form-control');
                $this->add($status);
            } else {
                $this->add(
                    new Hidden('status', ['id' => 'status_model_Companies_counter_' . $this->cnt,  'value' => $model->getStatus()])
                );
            }

        } else {
            $this->add(
                new Hidden('status', ['id' => 'status_model_Companies_counter_' . $this->cnt, 'value' => 'Pending'])
            );
        }
    }
}
