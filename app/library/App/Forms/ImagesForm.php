<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 25.09.17
 * Time: 8:52
 */

namespace App\Forms;

use App\Model\Images;
use Phalcon\Forms\Element\File;
use Phalcon\Forms\Element\Hidden;

/**
 * Phalcon\Forms\Form
 *
 * This component allows to build forms using an object-oriented interface
 */
class ImagesForm extends BaseForm
{
    public static $counter = 0;

    /**
     * ImagesForm constructor.
     * @param null $entity
     * @param null $userOptions
     */
    public function __construct($entity = null, $userOptions = null)
    {
        $this->cnt = ++static::$counter;
        $this->isImage = true;
        parent::__construct($entity, $userOptions);
    }

    /**
     * @param Images|null $image
     * @param array|null $options
     */
    public function initialize(Images $image = null, array $options = null)
    {
        if (isset($options['cnt'])) {
            $this->cnt = $options['cnt'];
        }
        if (isset($options['show'])) {
            $this->show = (bool) $options['show'];
        }

        $img = new File('fileName');
        $img->setLabel('Image');
        $img->setAttribute('class', '');
        $img->setAttribute('id', 'fileName_model_Image_counter_' . $this->cnt);
        $this->add($img);

        $this->add(
            new Hidden('id', ['id' => 'id_model_Image_counter_' . $this->cnt])
        );

        $this->add(
            new Hidden(
                'csrf',
                ['id' => 'csrf_model_Image_counter_' . $this->cnt]
            )
        );
    }
}
