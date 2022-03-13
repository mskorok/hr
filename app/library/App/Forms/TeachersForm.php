<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 25.09.17
 * Time: 8:52
 */

namespace App\Forms;

use App\Model\Teachers;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Numeric;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\TextArea;

/**
 * Phalcon\Forms\Form
 *
 * This component allows to build forms using an object-oriented interface
 */
class TeachersForm extends BaseForm
{
    private static $counter = 0;

    /**
     * TeachersForm constructor.
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
     * @param Teachers $model
     * @param array|null $options
     */
    public function initialize(Teachers $model = null, array $options = null): void
    {
        if (isset($options['cnt'])) {
            $this->cnt = $options['cnt'];
        }
        if (isset($options['admin'])) {
            $this->admin = (bool) $options['admin'];
        }

        $this->add(
            new Hidden('id', ['id' => 'id_model_Teachers_counter_' . $this->cnt])
        );


        $rate = new Numeric('rate', [
            'class'   => 'form-control',
            'placeholder' => 'Rate',
            'id' => 'rate_model_Teachers_counter_' . $this->cnt
        ]);
        $rate->setLabel('Rate');
        $this->add($rate);

        $skills = new Text('skills', [
            'class'   => 'form-control',
            'placeholder' => 'Skills',
            'id' => 'skills_model_Teachers_counter_' . $this->cnt
        ]);
        $skills->setLabel('Skills');
        $this->add($skills);

        $title = new Text('title', [
            'class'   => 'form-control',
            'placeholder' => 'Title',
            'id' => 'title_model_Teachers_counter_' . $this->cnt
        ]);
        $title->setLabel('Title');
        $this->add($title);


        $desc = new Text('description', [
            'class'   => 'form-control',
            'placeholder' => 'Description',
            'id' => 'description_model_Teachers_counter_' . $this->cnt
        ]);
        $desc->setLabel('Description');
        $this->add($desc);


        $text = new TextArea('dtext', [
            'class'   => 'form-control',
            'placeholder' => 'Text',
            'row' => 5,
            'id' => 'text_model_Teachers_counter_' . $this->cnt
        ]);
        $text->setLabel('Text');
        $this->add($text);
    }
}
