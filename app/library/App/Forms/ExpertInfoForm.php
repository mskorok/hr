<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 25.09.17
 * Time: 8:52
 */

namespace App\Forms;

use App\Model\ExpertInfo;
use App\Model\Users;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;

/**
 * Phalcon\Forms\Form
 *
 * This component allows to build forms using an object-oriented interface
 */
class ExpertInfoForm extends BaseForm
{

    public static $counter = 0;

    /**
     * ExpertInfoForm constructor.
     * @param null $entity
     * @param null $userOptions
     */
    public function __construct($entity = null, $userOptions = null)
    {
        $this->cnt = ++static::$counter;
        parent::__construct($entity, $userOptions);
    }

    /**
     * @param ExpertInfo|null $expertInfo
     * @param array|null $options
     */
    public function initialize(ExpertInfo $expertInfo = null, array $options = null): void
    {
        if (isset($options['cnt'])) {
            $this->cnt = $options['cnt'];
        }
        if (isset($options['show'])) {
            $this->show = (bool) $options['show'];
        }
        $this->add(
            new Hidden(
                'id',
                [
                    'id' => 'id_model_ExpertInfo_counter_' . $this->cnt,
                    'value' => $expertInfo ? $expertInfo->getId() : null
                ]
            )
        );

        $users =  new Select(
            'user_id',
            Users::find(),
            [
                'using' => [
                    'id',
                    'username'
                ],
                'id' => 'user_id_model_ExpertInfo_counter_' . $this->cnt,
                'value' => $expertInfo ? $expertInfo->getUserId() : null
            ]
        );

        $users->setLabel('User');
        $users->setAttribute('class', 'form-control');

        $this->add($users);


        $level =  new Select(
            'level',
            [
                'Diamond' => 'Diamond',
                'Gold' => 'Gold',
                'Silver' => 'Silver',
                'Bronze' => 'Bronze'
            ],
            [
                'id' => 'level_model_ExpertInfo_counter_' . $this->cnt,
                'value' => $expertInfo ? $expertInfo->getLevel() : null
            ]
        );

        $level->setLabel('Level');
        $this->add($level);

        $skills = new Text('skills', [
            'class'   => 'form-control',
            'placeholder' => 'Skills',
            'id' => 'skills_model_ExpertInfo_counter_' . $this->cnt,
            'value' => $expertInfo ? $expertInfo->getSkills() : null
        ]);
        $skills->setLabel('Skills');
        $this->add($skills);
    }
}
