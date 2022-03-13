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
use App\Model\PartnerInfo;
use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\TextArea;

/**
 * Phalcon\Forms\Form
 *
 * This component allows to build forms using an object-oriented interface
 */
class PartnerInfoForm extends BaseForm
{
    private static $counter = 0;

    /**
     * PartnerInfoForm constructor.
     * @param null $entity
     * @param null $userOptions
     */
    public function __construct($entity = null, $userOptions = null)
    {
        $this->cnt = ++static::$counter;
        parent::__construct($entity, $userOptions);
        $this->formId = 'partner_form';
    }

    /**
     * @param PartnerInfo|null $model
     * @param array|null $options
     */
    public function initialize(PartnerInfo $model = null, array $options = null): void
    {
        if (isset($options['cnt'])) {
            $this->cnt = $options['cnt'];
        }
        if (isset($options['admin'])) {
            $this->admin = (bool) $options['admin'];
        }

        $this->add(
            new Hidden('id', ['id' => 'id_model_PartnerInfo_counter_' . $this->cnt])
        );

        $name = new Text('name', [
            'class'   => 'form-control',
            'placeholder' => 'Name',
            'id' => 'name_model_PartnerInfo_counter_' . $this->cnt
        ]);
        $name->setLabel('Name');
        $this->add($name);

        $description = new TextArea('description', [
            'class'   => 'form-control',
            'placeholder' => 'Description',
            'row' => 5,
            'id' => 'description_model_PartnerInfo_counter_' . $this->cnt
        ]);
        $description->setLabel('Description');
        $this->add($description);

        $company = new Text('company', [
            'class'   => 'form-control',
            'placeholder' => 'Company',
            'id' => 'company_model_PartnerInfo_counter_' . $this->cnt
        ]);
        $company->setLabel('Company');
        $this->add($company);

        $city = new Text('city', [
            'class'   => 'form-control',
            'placeholder' => 'City',
            'id' => 'city_model_PartnerInfo_counter_' . $this->cnt
        ]);
        $city->setLabel('City');
        $this->add($city);

        $country = new Select(
            'country_id',
            Countries::find(),
            [
                'using' => [
                    'id',
                    'name'
                ],
                'id' => 'country_id_model_PartnerInfo_counter_' . $this->cnt
            ]
        );

        $country->setLabel('Country');
        $country->setAttribute('class', 'form-control');

        $this->add($country);

        $address = new Text('address', [
            'class'   => 'form-control',
            'placeholder' => 'Address',
            'id' => 'address_model_PartnerInfo_counter_' . $this->cnt
        ]);
        $address->setLabel('Address');
        $this->add($address);


        $phone = new Text('phone', [
            'class'   => 'form-control',
            'placeholder' => 'Phone',
            'id' => 'phone_model_PartnerInfo_counter_' . $this->cnt
        ]);
        $phone->setLabel('Phone');
        $this->add($phone);


        $email = new Text('email', [
            'class'   => 'form-control',
            'placeholder' => 'Email',
            'id' => 'email_model_PartnerInfo_counter_' . $this->cnt
        ]);
        $email->setLabel('Email');
        $this->add($email);

        $approved = new Check('approved');
        $approved->setLabel('Approved');
        $approved->addFilter('bool');
        $approved->setAttribute('id', 'approved_model_PartnerInfo_counter_' . $this->cnt);
        $this->add($approved);

        $requisites = new TextArea('requisites', [
            'class'   => 'form-control',
            'placeholder' => 'Requisites',
            'row' => 5,
            'id' => 'requisites_model_PartnerInfo_counter_' . $this->cnt
        ]);
        $requisites->setLabel('Requisites');
        $this->add($requisites);

        $level =  new Select(
            'level',
            [
                'high' => 'High',
                'middle' => 'Middle',
                'low' => 'Low',
                'rejected' => 'Rejected'
            ],
            [
                'id' => 'level_model_PartnerInfo_counter_' . $this->cnt
            ]
        );
        $level->setLabel('Level');
        $this->add($level);
    }
}
