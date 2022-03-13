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
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Text;

/**
 * Phalcon\Forms\Form
 *
 * This component allows to build forms using an object-oriented interface
 */
class CountriesForm extends BaseForm
{
    private static $counter = 0;

    /**
     * CountriesForm constructor.
     * @param null $entity
     * @param null $userOptions
     */
    public function __construct($entity = null, $userOptions = null)
    {
        $this->cnt = ++static::$counter;
        parent::__construct($entity, $userOptions);
    }

    /**
     * @param Countries|null $model
     * @param array|null $options
     */
    public function initialize(Countries $model = null, array $options = null): void
    {
        if (isset($options['cnt'])) {
            $this->cnt = $options['cnt'];
        }
        if (isset($options['admin'])) {
            $this->admin = (bool) $options['admin'];
        }

        $this->add(
            new Hidden('id', ['id' => 'id_model_Countries_counter_' . $this->cnt])
        );

        $name = new Text('name', [
            'class'   => 'form-control',
            'placeholder' => 'Name',
            'id' => 'name_model_Countries_counter_' . $this->cnt
        ]);
        $name->setLabel('name');
        $this->add($name);

        $short_name = new Text('short_name', [
            'class'   => 'form-control',
            'placeholder' => 'Short Name',
            'id' => 'short_name_model_Countries_counter_' . $this->cnt
        ]);
        $short_name->setLabel('Short Name');
        $this->add($short_name);
    }
}
