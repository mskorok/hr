<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 25.09.17
 * Time: 8:52
 */

namespace App\Forms;

use App\Model\Schedule;
use App\Model\Teachers;
use Phalcon\Forms\Element\Date;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Numeric;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Filter;

/**
 * Phalcon\Forms\Form
 *
 * This component allows to build forms using an object-oriented interface
 */
class ScheduleForm extends BaseForm
{
    private static $counter = 0;

    /**
     * ScheduleForm constructor.
     * @param null $entity
     * @param null $userOptions
     */
    public function __construct($entity = null, $userOptions = null)
    {
        $this->cnt = ++static::$counter;
        parent::__construct($entity, $userOptions);
        $this->formId = 'resume_form';
    }

    /**
     * @param Schedule $model
     * @param array|null $options
     */
    public function initialize(Schedule $model = null, array $options = null): void
    {
        if (isset($options['cnt'])) {
            $this->cnt = $options['cnt'];
        }
        if (isset($options['admin'])) {
            $this->admin = (bool) $options['admin'];
        }

        $this->add(
            new Hidden('id', ['id' => 'id_model_Schedule_counter_' . $this->cnt])
        );



        $date = new Date('date', [
            'class'   => 'form-control',
            'placeholder' => 'Birthday',
            'id' => 'date_model_Schedule_counter_' . $this->cnt
        ]);
        $date->setLabel('date');
        $date->setDefault((new \DateTime())->format('Y-m-d H:i:s'));

        $filter = new Filter();
        $filter->add('date', static function ($date) {
            return (new \DateTime($date))->format('Y-m-d H:i:s');
        });

        if ($model && $model->getDate() !== null) {
            $date->setDefault((new \DateTime($model->getDate()))->format('Y-m-d H:i:s'));

            $dateField = $filter->sanitize($model->getDate(), 'date');
            $model->setDate($dateField);
            $date->addValidator(new \Phalcon\Validation\Validator\Date())->addFilter('date');
        }
        $this->add($date);

        $desc = new Text('description', [
            'class'   => 'form-control',
            'placeholder' => 'Description',
            'id' => 'description_model_Schedule_counter_' . $this->cnt
        ]);
        $desc->setLabel('Description');
        $this->add($desc);
    }
}
