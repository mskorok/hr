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
use App\Model\Users;
use App\Model\UserSubscription;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Select;

/**
 * Phalcon\Forms\Form
 *
 * This component allows to build forms using an object-oriented interface
 */
class UserSubscriptionForm extends BaseForm
{

    public static $counter = 0;

    /**
     * UserSubscriptionForm constructor.
     * @param null $entity
     * @param null $userOptions
     */
    public function __construct($entity = null, $userOptions = null)
    {
        $this->cnt = ++static::$counter;
        parent::__construct($entity, $userOptions);
    }

    /**
     * @param UserSubscription|null $userSubscription
     * @param array|null $options
     */
    public function initialize(UserSubscription $userSubscription = null, array $options = null): void
    {
        if (isset($options['cnt'])) {
            $this->cnt = $options['cnt'];
        }
        if (isset($options['show'])) {
            $this->show = (bool) $options['show'];
        }
        $this->add(
            new Hidden('id', ['id' => 'id_model_UserSubscription_counter_' . $this->cnt])
        );

        $subscription =  new Select(
            'subscription_id',
            Subscriptions::find(),
            [
                'using' => [
                    'id',
                    'title'
                ],
                'id' => 'subscription_id_model_UserSubscription_counter_' . $this->cnt
            ]
        );

        $subscription->setLabel('Subscription');
        $subscription->setAttribute('class', 'form-control');

        $this->add($subscription);

        $user = new Select(
            'user_id',
            Users::find(),
            [
                'using' => [
                    'id',
                    'name'
                ],
                'id' => 'user_id_model_UserSubscription_counter_' . $this->cnt
            ]
        );

        $user->setLabel('User');
        $user->setAttribute('class', 'form-control');

        $this->add($user);
    }
}
