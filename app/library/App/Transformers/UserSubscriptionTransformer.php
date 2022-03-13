<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Model\UserSubscription;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class UserSubscriptionTransformer
 * @package App\Transformers
 */
class UserSubscriptionTransformer extends ModelTransformer
{
    /**
     * Transforms are automatically handled
     * based on your model when you extend ModelTransformer
     * and assign the modelClass property
     */


    public function __construct()
    {
        $this->modelClass = UserSubscription::class;

        $this->availableIncludes = [
            'Users',
            'Payments',
            'Subscriptions'
        ];
    }

    /**
     * @param UserSubscription $model
     * @return Item
     */
    public function includeUsers(UserSubscription $model): Item
    {
        return $this->item($model->getUsers(), new UsersTransformer());
    }

    /**
     * @param UserSubscription $model
     * @return Collection
     */
    public function includePayments(UserSubscription $model): Collection
    {
        return $this->collection($model->getPayments(), new PaymentsTransformer());
    }

    /**
     * @param UserSubscription $model
     * @return Item
     */
    public function includeSubscriptions(UserSubscription $model): Item
    {
        return $this->item($model->getSubscriptions(), new SubscriptionsTransformer());
    }
}
