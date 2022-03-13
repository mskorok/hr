<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 25.09.17
 * Time: 8:52
 */

namespace App\Forms;

use App\Model\Mail;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\TextArea;

/**
 * Phalcon\Forms\Form
 *
 * This component allows to build forms using an object-oriented interface
 */
class MailForm extends BaseForm
{

    public static $counter = 0;

    /**
     * MailForm constructor.
     * @param null $entity
     * @param null $userOptions
     */
    public function __construct($entity = null, $userOptions = null)
    {
        $this->cnt = ++static::$counter;
        parent::__construct($entity, $userOptions);
    }

    /**
     * @param Mail|null $mail
     * @param array|null $options
     */
    public function initialize(Mail $mail = null, array $options = null): void
    {
        if (isset($options['cnt'])) {
            $this->cnt = $options['cnt'];
        }
        if (isset($options['show'])) {
            $this->show = (bool) $options['show'];
        }

        $this->add(
            new Hidden('id', ['id' => 'id_model_Mail_counter_' . $this->cnt])
        );

        $this->add(
            new Hidden(
                'csrf'
            )
        );

        $name = new Text('name', [
            'class'   => 'form-control',
            'placeholder' => 'Name'
        ]);
        $name->setLabel('Name');

        $this->add($name);

        $from = new Text('mailFrom', [
            'class'   => 'form-control',
            'placeholder' => 'Mail From'
        ]);
        $from->setLabel('Mail From');

        $this->add($from);

        $to = new Text('mailTo', [
            'class'   => 'form-control',
            'placeholder' => 'Mail To'
        ]);
        $to->setLabel('Mail To');

        $this->add($to);

        $subject = new Text('subject', [
            'class'   => 'form-control',
            'placeholder' => 'Subject'
        ]);
        $subject->setLabel('Subject');

        $this->add($subject);

        $text = new TextArea('text', [
            'class'   => 'form-control',
            'placeholder' => 'Text',
            'rows' => 5,
        ]);
        $text->setLabel('Text');

        $this->add($text);
    }
}
