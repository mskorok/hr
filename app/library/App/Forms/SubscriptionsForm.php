<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 25.09.17
 * Time: 8:52
 */

namespace App\Forms;

use App\Model\Subscriptions;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Text;

/**
 * Phalcon\Forms\Form
 *
 * This component allows to build forms using an object-oriented interface
 */
class SubscriptionsForm extends BaseForm
{
    private static $counter = 0;

    /**
     * SubscriptionsForm constructor.
     * @param null $entity
     * @param null $userOptions
     */
    public function __construct($entity = null, $userOptions = null)
    {
        $this->cnt = ++static::$counter;
        parent::__construct($entity, $userOptions);
    }

    /**
     * @param Subscriptions|null $model
     * @param array|null $options
     */
    public function initialize(Subscriptions $model = null, array $options = null): void
    {
        if (isset($options['cnt'])) {
            $this->cnt = $options['cnt'];
        }
        if (isset($options['admin'])) {
            $this->admin = (bool) $options['admin'];
        }

        $this->add(
            new Hidden('id', ['id' => 'id_model_Subscriptions_counter_' . $this->cnt])
        );

        $title = new Text('title', [
            'class'   => 'form-control',
            'placeholder' => 'Title',
            'id' => 'title_model_Subscriptions_counter_' . $this->cnt
        ]);
        $title->setLabel('Title');
        $this->add($title);

        $description = new Text('description', [
            'class'   => 'form-control',
            'placeholder' => 'Description',
            'id' => 'description_model_Subscriptions_counter_' . $this->cnt
        ]);
        $description->setLabel('Description');
        $this->add($description);
    }
}
