<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 25.09.17
 * Time: 8:52
 */

namespace App\Forms;

use App\Model\Payments;
use Phalcon\Filter;
use Phalcon\Forms\Element\Date;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Numeric;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;

/**
 * Phalcon\Forms\Form
 *
 * This component allows to build forms using an object-oriented interface
 */
class PaymentsForm extends BaseForm
{
    private static $counter = 0;

    /**
     * PaymentsForm constructor.
     * @param null $entity
     * @param null $userOptions
     */
    public function __construct($entity = null, $userOptions = null)
    {
        $this->cnt = ++static::$counter;
        parent::__construct($entity, $userOptions);
    }

    /**
     * @param Payments|null $model
     * @param array|null $options
     */
    public function initialize(Payments $model = null, array $options = null): void
    {
        if (isset($options['cnt'])) {
            $this->cnt = $options['cnt'];
        }
        if (isset($options['admin'])) {
            $this->admin = (bool) $options['admin'];
        }

        $this->add(
            new Hidden('id', ['id' => 'id_model_Payments_counter_' . $this->cnt])
        );

        $title = new Text('title', [
            'class'   => 'form-control',
            'placeholder' => 'title',
            'id' => 'title_model_Payments_counter_' . $this->cnt
        ]);
        $title->setLabel('title');
        $this->add($title);

        $amount = new Numeric('amount', [
            'class'   => 'form-control',
            'placeholder' => 'Amount',
            'id' => 'amount_model_Payments_counter_' . $this->cnt
        ]);
        $amount->setLabel('Amount');
        $this->add($amount);


        $bank = new Text('bank', [
            'class'   => 'form-control',
            'placeholder' => 'Bank',
            'id' => 'bank_model_Payments_counter_' . $this->cnt
        ]);
        $bank->setLabel('Bank');
        $this->add($bank);

        $date = new Date('date', [
            'class'   => 'form-control',
            'placeholder' => 'Date',
            'id' => 'date_model_Payments_counter_' . $this->cnt
        ]);
        $date->setLabel('Date');
        $date->setDefault((new \DateTime())->format('Y-m-d'));

        $filter = new Filter();
        $filter->add('date', static function ($date) {
            return (new \DateTime($date))->format('Y-m-d');
        });

        if ($model && !empty($model->getDate())) {
            $date->setDefault((new \DateTime($model->getDate()))->format('Y-m-d'));

            $dateField = $filter->sanitize($model->getDate(), 'date');
            $model->setDate($dateField);
            $date->addValidator(new \Phalcon\Validation\Validator\Date())->addFilter('date');
        }

        $this->add($date);


        $currency =  new Select(
            'currency',
            [
                'usd' => 'USD',
                'euro' => 'EURO',
                'rupee' => 'Rupee',
                'rubl' => 'Rubl'
            ],
            [
                'id' => 'currency_model_Payments_counter_' . $this->cnt
            ]
        );
        $currency->setLabel('Currency');
        $this->add($currency);
    }
}
