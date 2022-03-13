<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 25.09.17
 * Time: 8:52
 */

namespace App\Forms;

use App\Model\Languages;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\TextArea;

/**
 * Phalcon\Forms\Form
 *
 * This component allows to build forms using an object-oriented interface
 */
class LanguagesForm extends BaseForm
{
    private static $counter = 0;

    /**
     * ArticlesForm constructor.
     * @param null $entity
     * @param null $userOptions
     */
    public function __construct($entity = null, $userOptions = null)
    {
        $this->cnt = ++static::$counter;
        parent::__construct($entity, $userOptions);
    }

    /**
     * @param Languages $model
     * @param array|null $options
     */
    public function initialize(Languages $model = null, array $options = null): void
    {
        if (isset($options['cnt'])) {
            $this->cnt = $options['cnt'];
        }
        if (isset($options['admin'])) {
            $this->admin = (bool) $options['admin'];
        }

        $this->add(
            new Hidden('id', ['id' => 'id_model_Languages_counter_' . $this->cnt])
        );

        $name = new Text('name', [
            'class'   => 'form-control',
            'placeholder' => 'Name',
            'id' => 'name_model_Languages_counter_' . $this->cnt
        ]);
        $name->setLabel('Name');
        $this->add($name);

        $code = new Text('code', [
            'class'   => 'form-control',
            'placeholder' => 'Code',
            'id' => 'code_model_Languages_counter_' . $this->cnt
        ]);
        $code->setLabel('Code');
        $this->add($code);

        $codeShort = new TextArea('codeShort', [
            'class'   => 'form-control',
            'placeholder' => 'CodeShort',
            'row' => 5,
            'id' => 'code_short_model_Languages_counter_' . $this->cnt
        ]);

        $codeShort->setLabel('CodeShort');
        $this->add($codeShort);
    }
}
