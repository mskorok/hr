<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 25.09.17
 * Time: 8:52
 */

namespace App\Forms;

use App\Model\ArticleImages;
use App\Model\Articles;
use App\Model\Images;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Select;

/**
 * Phalcon\Forms\Form
 *
 * This component allows to build forms using an object-oriented interface
 */
class ArticleImagesForm extends BaseForm
{

    public static $counter = 0;

    /**
     * ArticleImagesForm constructor.
     * @param null $entity
     * @param null $userOptions
     */
    public function __construct($entity = null, $userOptions = null)
    {
        $this->cnt = ++static::$counter;
        parent::__construct($entity, $userOptions);
    }

    /**
     * @param ArticleImages|null $articleImages
     * @param array|null $options
     */
    public function initialize(ArticleImages $articleImages = null, array $options = null): void
    {
        if (isset($options['cnt'])) {
            $this->cnt = $options['cnt'];
        }
        if (isset($options['show'])) {
            $this->show = (bool) $options['show'];
        }
        $this->add(
            new Hidden('id', ['id' => 'id_model_ArticleImages_counter_' . $this->cnt])
        );

        $article =  new Select(
            'article_id',
            Articles::find(),
            [
                'using' => [
                    'id',
                    'title'
                ],
                'id' => 'article_id_model_ArticleImages_counter_' . $this->cnt
            ]
        );

        $article->setLabel('Article');
        $article->setAttribute('class', 'form-control');

        $this->add($article);

        $image = new Select(
            'image_id',
            Images::find(),
            [
                'using' => [
                    'id',
                    'fileName'
                ],
                'id' => 'image_id_model_ArticleImages_counter_' . $this->cnt
            ]
        );

        $image->setLabel('Image');
        $image->setAttribute('class', 'form-control');

        $this->add($image);
    }
}
