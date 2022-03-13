<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 25.09.17
 * Time: 8:52
 */

namespace App\Forms;

use App\Model\Articles;
use App\Model\Comments;
use App\Model\Users;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;

/**
 * Phalcon\Forms\Form
 *
 * This component allows to build forms using an object-oriented interface
 */
class CommentsForm extends BaseForm
{
    private static $counter = 0;

    /**
     * CommentsForm constructor.
     * @param null $entity
     * @param null $userOptions
     */
    public function __construct($entity = null, $userOptions = null)
    {
        $this->cnt = ++static::$counter;
        parent::__construct($entity, $userOptions);
        $this->formId = 'comments_form';
    }

    /**
     * @param Comments|null $model
     * @param array|null $options
     */
    public function initialize(Comments $model = null, array $options = null): void
    {
        if (isset($options['cnt'])) {
            $this->cnt = $options['cnt'];
        }
        if (isset($options['admin'])) {
            $this->admin = (bool) $options['admin'];
        }

        $this->add(
            new Hidden('id', ['id' => 'id_model_Comments_counter_' . $this->cnt])
        );

        $title = new Text('title', [
            'class'   => 'form-control',
            'placeholder' => 'Title',
            'id' => 'title_model_Comments_counter_' . $this->cnt
        ]);
        $title->setLabel('Title');
        $this->add($title);

        $text = new Text('text', [
            'class'   => 'form-control',
            'placeholder' => 'Text',
            'id' => 'text_model_Comments_counter_' . $this->cnt
        ]);
        $text->setLabel('Text');
        $this->add($text);


        $article =  new Select(
            'article_id',
            Articles::find(),
            [
                'using' => [
                    'id',
                    'title'
                ],
                'id' => 'article_id_model_Comments_counter_' . $this->cnt
            ]
        );
        $article->setLabel('Article');
        $article->setAttribute('class', 'form-control');
        $this->add($article);

        $user =  new Select(
            'user_id',
            Users::find(),
            [
                'using' => [
                    'id',
                    'name'
                ],
                'id' => 'user_id_model_Comments_counter_' . $this->cnt
            ]
        );
        $user->setLabel('User');
        $user->setAttribute('class', 'form-control');
        $this->add($user);


        $parent =  new Select(
            'parent_id',
            Comments::find(),
            [
                'using' => [
                    'id',
                    'title'
                ],
                'id' => 'parent_id_model_Comments_counter_' . $this->cnt
            ]
        );
        $parent->setLabel('Parent');
        $parent->setAttribute('class', 'form-control');
        $this->add($parent);
    }
}
