<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 25.09.17
 * Time: 8:52
 */

namespace App\Forms;

use App\Model\Subcategory;
use Phalcon\Forms\Element\File;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\TextArea;

/**
 * Phalcon\Forms\Form
 *
 * This component allows build forms using an object-oriented interface
 */
class SubcategoryForm extends BaseForm
{
    private static $counter = 0;

    /**
     * SubcategoryForm constructor.
     * @param null $entity
     * @param null $userOptions
     */
    public function __construct($entity = null, $userOptions = null)
    {
        $this->cnt = ++static::$counter;
        parent::__construct($entity, $userOptions);
    }

    /**
     * @param Subcategory|null $model
     * @param array|null $options
     */
    public function initialize(Subcategory $model = null, array $options = null): void
    {
        if (isset($options['cnt'])) {
            $this->cnt = $options['cnt'];
        }
        if (isset($options['admin'])) {
            $this->admin = (bool) $options['admin'];
        }

        $this->add(
            new Hidden('id', ['id' => 'id_model_Subcategory_counter_' . $this->cnt])
        );

        $title = new Text('title', [
            'class'   => 'form-control',
            'placeholder' => 'Title',
            'id' => 'title_model_Subcategory_counter_' . $this->cnt
        ]);
        $title->setLabel('Title');
        $this->add($title);

        $description = new Text('description', [
            'class'   => 'form-control',
            'placeholder' => 'Description',
            'id' => 'description_model_Subcategory_counter_' . $this->cnt
        ]);
        $description->setLabel('Description');
        $this->add($description);

        $text = new TextArea('text', [
            'class'   => 'form-control',
            'placeholder' => 'Text',
            'row' => 5,
            'id' => 'text_model_Subcategory_counter_' . $this->cnt
        ]);

        $text->setLabel('Text');
        $this->add($text);

        $link = new Text('link', [
            'class'   => 'form-control',
            'placeholder' => 'Link',
            'id' => 'link_model_Subcategory_counter_' . $this->cnt
        ]);
        $link->setLabel('Link');
        $this->add($link);

        $image = new File('fileName');
//        $image->setLabel('Image');
        $image->setAttribute('class', 'hidden');
        $image->setAttribute('id', 'images_model_Subcategory_counter_' . $this->cnt);
        if ($this->show) {
            $image->setAttribute('disabled', 'disabled');
        }
        $this->add($image);
    }
}
