<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 25.09.17
 * Time: 8:52
 */

namespace App\Forms;

use App\Model\Education;
use Phalcon\Filter;
use Phalcon\Forms\Element\Date;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;

/**
 * Phalcon\Forms\Form
 *
 * This component allows to build forms using an object-oriented interface
 */
class EducationForm extends BaseForm
{
    private static $counter = 0;

    /**
     * EducationForm constructor.
     * @param null $entity
     * @param null $userOptions
     */
    public function __construct($entity = null, $userOptions = null)
    {
        $this->cnt = ++static::$counter;
        parent::__construct($entity, $userOptions);
    }

    /**
     * @param Education|null $model
     * @param array|null $options
     */
    public function initialize(Education $model = null, array $options = null): void
    {
        if (isset($options['cnt'])) {
            $this->cnt = $options['cnt'];
        }
        if (isset($options['admin'])) {
            $this->admin = (bool) $options['admin'];
        }

        $this->add(
            new Hidden('id', ['id' => 'id_model_Education_counter_' . $this->cnt])
        );

        $name = new Text('name', [
            'class'   => 'form-control',
            'placeholder' => 'Name',
            'id' => 'name_model_Education_counter_' . $this->cnt
        ]);
        $name->setLabel('Name');
        $this->add($name);

        $specialization = new Text('specialization', [
            'class'   => 'form-control',
            'placeholder' => 'Specialization',
            'id' => 'specialization_model_Education_counter_' . $this->cnt
        ]);
        $specialization->setLabel('Specialization');
        $this->add($specialization);

        $level =  new Select(
            'level',
            [
                'Doctor' => 'Doctor',
                'Master' => 'Master',
                'Bachelor' => 'Bachelor',
                'High School Diploma' => 'High School Diploma',
                'College' => 'College',
                'GED' => 'GED',
                'Middle School' => 'Middle School',
                'No education' => 'No education',
                'Other' => 'Other'
            ],
            [
                'id' => 'level_model_Education_counter_' . $this->cnt
            ]
        );
        $level->setLabel('Level');
        $this->add($level);

        $start = new Date('start', [
            'class'   => 'form-control',
            'placeholder' => 'Start',
            'id' => 'start_model_Education_counter_' . $this->cnt
        ]);
        $start->setLabel('start');
        $start->setDefault((new \DateTime())->format('Y-m-d'));

        $filter = new Filter();
        $filter->add('date', function ($date) {
            return (new \DateTime($date))->format('Y-m-d');
        });

        if ($model && !empty($model->getStart())) {
            $start->setDefault((new \DateTime($model->getStart()))->format('Y-m-d'));

            $dateField = $filter->sanitize($model->getStart(), 'date');
            $model->setStart($dateField);
            $start->addValidator(new \Phalcon\Validation\Validator\Date())->addFilter('date');
        }
        $this->add($start);


        $finish = new Date('finish', [
            'class'   => 'form-control',
            'placeholder' => 'Finish',
            'id' => 'finish_model_Education_counter_' . $this->cnt
        ]);
        $finish->setLabel('Finish');
        $finish->setDefault((new \DateTime())->format('Y-m-d'));

        $filter = new Filter();
        $filter->add('date', function ($date) {
            return (new \DateTime($date))->format('Y-m-d');
        });

        if ($model && !empty($model->getFinish())) {
            $finish->setDefault((new \DateTime($model->getFinish()))->format('Y-m-d'));

            $dateField = $filter->sanitize($model->getFinish(), 'date');
            $model->setFinish($dateField);
            $finish->addValidator(new \Phalcon\Validation\Validator\Date())->addFilter('date');
        }
        $this->add($finish);
    }
}
