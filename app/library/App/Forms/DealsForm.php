<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 25.09.17
 * Time: 8:52
 */

namespace App\Forms;

use App\Model\Deals;
use App\Model\PartnerInfo;
use App\Model\Users;
use App\Model\Vacancies;
use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\TextArea;

/**
 * Phalcon\Forms\Form
 *
 * This component allows to build forms using an object-oriented interface
 */
class DealsForm extends BaseForm
{

    public static $counter = 0;

    /**
     * DealsForm constructor.
     * @param null $entity
     * @param null $userOptions
     */
    public function __construct($entity = null, $userOptions = null)
    {
        $this->cnt = ++static::$counter;
        parent::__construct($entity, $userOptions);
    }

    /**
     * @param Deals|null $deals
     * @param array|null $options
     */
    public function initialize(Deals $deals = null, array $options = null): void
    {
        if (isset($options['cnt'])) {
            $this->cnt = $options['cnt'];
        }
        if (isset($options['show'])) {
            $this->show = (bool) $options['show'];
        }
        $this->add(
            new Hidden('id', ['id' => 'id_model_Deals_counter_' . $this->cnt])
        );

        $partner =  new Select(
            'partner_id',
            PartnerInfo::find(),
            [
                'using' => [
                    'id',
                    'info'
                ],
                'id' => 'partner_id_model_Deals_counter_' . $this->cnt
            ]
        );

        $partner->setLabel('Partner');
        $partner->setAttribute('class', 'form-control');

        $this->add($partner);

        $user = new Select(
            'user_id',
            Users::find(),
            [
                'using' => [
                    'id',
                    'name'
                ],
                'id' => 'user_id_model_Deals_counter_' . $this->cnt
            ]
        );

        $user->setLabel('Manager');
        $user->setAttribute('class', 'form-control');

        $this->add($user);

        $vacancy = new Select(
            'vacancy_id',
            Vacancies::find(),
            [
                'using' => [
                    'id',
                    'name'
                ],
                'id' => 'vacancy_id_model_Deals_counter_' . $this->cnt
            ]
        );

        $vacancy->setLabel('Vacancy');
        $vacancy->setAttribute('class', 'form-control');

        $this->add($vacancy);

        $success = new Check('success');
        $success->setLabel('Success');
        $success->addFilter('bool');
        $success->setAttribute('id', 'success_model_Deals_counter_' . $this->cnt);
        $this->add($success);

        $description = new TextArea('description', [
            'class'   => 'form-control',
            'placeholder' => 'Description',
            'rows' => 5
        ]);
        $description->setLabel('Description');

        $this->add($description);
    }
}
