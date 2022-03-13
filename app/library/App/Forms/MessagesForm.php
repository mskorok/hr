<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 04.03.18
 * Time: 8:52
 */

namespace App\Forms;

use App\Model\Messages;
use App\Model\MessengerCategory;
use App\Model\Users;
use Phalcon\Filter;
use Phalcon\Forms\Element\Date;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\TextArea;

/**
 * Phalcon\Forms\Form
 *
 * This component allows to build forms using an object-oriented interface
 */
class MessagesForm extends BaseForm
{
    public static $counter = 0;

    /**
     * MessageForm constructor.
     * @param null $entity
     * @param null $userOptions
     */
    public function __construct($entity = null, $userOptions = null)
    {
        $this->cnt = ++static::$counter;
        parent::__construct($entity, $userOptions);
    }

    /**
     * @param Messages|null $message
     * @param array|null $options
     */
    public function initialize(Messages $message = null, array $options = null): void
    {
        if (isset($options['cnt'])) {
            $this->cnt = $options['cnt'];
        }
        if (isset($options['show'])) {
            $this->show = (bool) $options['show'];
        }

        $this->add(
            new Hidden('id', ['id' => 'id_model_Messages_counter_' . $this->cnt])
        );

        $this->add(
            new Hidden('parent')
        );

        $this->add(
            new Hidden(
                'csrf'
            )
        );

//        $parent = new Numeric('parent', [
//            'class'   => 'form-control',
//            'placeholder' => 'Parent',
//        ]);
//        $parent->setLabel('Parent');
//
//        $this->add($parent);

//        $sender = new Numeric('sender', [
//            'class'   => 'form-control',
//            'placeholder' => 'Sender',
//        ]);
//        $sender->setLabel('Sender');
//
//        $this->add($sender);
//
//        $recipient = new Numeric('recipient', [
//            'class'   => 'form-control',
//            'placeholder' => 'Recipient',
//        ]);
//        $recipient->setLabel('Recipient');
//
//        $this->add($recipient);

        $title = new Text('title', [
            'class'   => 'form-control',
            'placeholder' => 'Title'
        ]);
        $title->setLabel('Title');

        $this->add($title);


        if ($this->show) {
            $content = new TextArea('content', [
                'class'   => 'form-control',
                'rows' => 9
            ]);
            $content->setLabel('Content');

            $this->add($content);


            /** @var Users $send */
            $send = $message ? $message->getReceiver() : null;

            if ($send instanceof Users) {
                $sender = new Text('senders', [
                    'class'   => 'form-control',
                    'placeholder' => 'Sender',
                    'value' => $send->getName() . ' ' . $send->getSurname()
                ]);
                $sender->setLabel('Sender');

                $this->add($sender);

                $this->add(
                    new Hidden('sender')
                );
            } else {
                $sender =  new Select(
                    'sender',
                    Users::find(),
                    [
                        'using' => [
                            'id',
                            'email'
                        ]
                    ]
                );
                $sender->setLabel('Sender');
                $sender->setAttribute('class', 'form-control');

                $this->add($sender);
            }

            $rec = $message ? $message->getAddresser() : null;

            if ($rec instanceof Users) {
                $recipient = new Text('rec', [
                    'class'   => 'form-control',
                    'placeholder' => 'Recipient',
                    'value' => $rec->getName() . ' ' . $send->getSurname()
                ]);
                $recipient->setLabel('Recipient');

                $this->add($recipient);
                $this->add(
                    new Hidden('recipient')
                );
            } else {
                $recipient =  new Select(
                    'recipient',
                    Users::find(),
                    [
                        'using' => [
                            'id',
                            'email'
                        ]
                    ]
                );
                $recipient->setLabel('Recipient');
                $recipient->setAttribute('class', 'form-control');

                $this->add($recipient);
            }

            $filter = new Filter();
            $filter->add('date', function ($date) {
                return (new \DateTime($date))->format('Y-m-d');
            });

            $category =  new Select(
                'category',
                MessengerCategory::find(),
                [
                    'using' => [
                        'id',
                        'name'
                    ]
                ]
            );
            $category->setLabel('Category');
            $category->setAttribute('class', 'form-control');

            $this->add($category);

            $sendMethod =  new Select(
                'sendMethod',
                [
                    'site' => 'Site'
                ]
            );
            $sendMethod->setLabel('Send Method');
            $sendMethod->setAttribute('class', 'form-control');

            $this->add($sendMethod);

            $status =  new Select(
                'status',
                [
                    'sent' => 'Sent',
                    'read' => 'Read'
                ]
            );
            $status->setLabel('Status');
            $status->setAttribute('class', 'form-control');

            $this->add($status);

            $supportStatus =  new Select(
                'supportStatus',
                [
                    'open' => 'Open',
                    'closed' => 'Closed',
                    'progress' => 'Progress',
                    'not support' => 'Not Support'
                ]
            );
            $supportStatus->setLabel('Support Status');
            $supportStatus->setAttribute('class', 'form-control');

            $this->add($supportStatus);

            $sentDate = new Date('sentDate', [
                'class'   => 'form-control',
                'placeholder' => 'Sent Date'
            ]);
            $sentDate->setLabel('Sent Date');

            if ($message && !empty($message->getSentDate())) {
                $sentDate->setDefault((new \DateTime($message->getSentDate()))->format('Y-m-d'));

                $dateField = $filter->sanitize($message->getSentDate(), 'date');
                $message->setSentDate($dateField);
                $sentDate->addValidator(new \Phalcon\Validation\Validator\Date())->addFilter('date');
            }

            $this->add($sentDate);

            $readDate = new Date('readDate', [
                'class'   => 'form-control',
                'placeholder' => 'Read Date'
            ]);
            $readDate->setLabel('Read Date');

            if ($message && !empty($message->getReadDate())) {
                $readDate->setDefault((new \DateTime($message->getReadDate()))->format('Y-m-d'));

                $dateField = $filter->sanitize($message->getReadDate(), 'date');
                $message->setReadDate($dateField);
                $readDate->addValidator(new \Phalcon\Validation\Validator\Date())->addFilter('date');
            }

            $this->add($readDate);
        } else {
            $this->add(
                new Hidden('sender')
            );

            $this->add(
                new Hidden(
                    'sentDate',
                    [
                        'value' => (new \DateTime())->format('Y-m-d')
                    ]
                )
            );

            $this->add(
                new Hidden(
                    'sendMethod',
                    [
                        'value' => 'site'
                    ]
                )
            );

            $this->add(
                new Hidden(
                    'status',
                    [
                        'value' => 'sent'
                    ]
                )
            );

//            $recipient = new Text('recipient', [
//                'class'   => 'form-control',
//                'placeholder' => 'Recipient',
//            ]);
//            $recipient->setLabel('Recipient');
//
//            $this->add($recipient);

            $users = Users::find();
            $options = [];
            foreach ($users as $user) {
                $options[] = [
                    $user->getId() => $user->getName() . ' ' . $user->getSurname()
                ];
            }

            $recipient =  new Select(
                'recipient',
                $options
            );
            $recipient->setLabel('Recipient');
            $recipient->setAttribute('class', 'form-control');

            $this->add($recipient);

            $content = new TextArea('content', [
                'class'   => 'form-control',
                'rows' => 9
            ]);
            $content->setLabel('Content');

            $this->add($content);

            $category =  new Select(
                'category',
                MessengerCategory::find(),
                [
                    'using' => [
                        'id',
                        'name'
                    ]
                ]
            );
            $category->setLabel('Category');
            $category->setAttribute('class', 'form-control');
            $this->add($category);

            $supportStatus =  new Select(
                'supportStatus',
                [
                    'open' => 'Open',
                    'not support' => 'Not Support'
                ]
            );

            $supportStatus->setLabel('Support Status');
            $supportStatus->setAttribute('class', 'form-control');

            $this->add($supportStatus);
        }
    }
}
