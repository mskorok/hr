<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Model\Subscriptions;
use League\Fractal\Resource\Collection;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class SubscriptionsTransformer
 * @package App\Transformers
 */
class SubscriptionsTransformer extends ModelTransformer
{
    /**
     * Transforms are automatically handled
     * based on your model when you extend ModelTransformer
     * and assign the modelClass property
     */


    public function __construct()
    {
        $this->modelClass = Subscriptions::class;

        $this->availableIncludes = [
            'CompanySubscription',
            'Companies',
            'UserSubscription',
            'Users'
        ];
    }

    /**
     * @param Subscriptions $model
     * @return Collection
     */
    public function includeCompanySubscription(Subscriptions $model): Collection
    {
        return $this->collection($model->getCompanySubscription(), new CompanySubscriptionTransformer());
    }

    /**
     * @param Subscriptions $model
     * @return Collection
     */
    public function includeCompanies(Subscriptions $model): Collection
    {
        return $this->collection($model->getCompanies(), new CompaniesTransformer());
    }

    /**
     * @param Subscriptions $model
     * @return Collection
     */
    public function includeUserSubscription(Subscriptions $model): Collection
    {
        return $this->collection($model->getUserSubscription(), new UserSubscriptionTransformer());
    }

    /**
     * @param Subscriptions $model
     * @return Collection
     */
    public function includeUsers(Subscriptions $model): Collection
    {
        return $this->collection($model->getUsers(), new UsersTransformer());
    }
}
