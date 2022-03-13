<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 09.05.19
 * Time: 10:49
 */

namespace App\Transformers;


use App\Model\CompanySubscription;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class CompanySubscriptionTransformer
 * @package App\Transformers
 */
class CompanySubscriptionTransformer extends ModelTransformer
{

    /**
     * CompanySubscriptionTransformer constructor.
     */
    public function __construct()
    {
        $this->modelClass = CompanySubscription::class;

        $this->availableIncludes = [
            'Companies',
            'Payments',
            'Subscriptions'
        ];
    }

    /**
     * @param CompanySubscription $model
     * @return Item
     */
    public function includeCompanies(CompanySubscription $model): Item
    {
        return $this->item($model->getCompanies(), new CompaniesTransformer());
    }

    /**
     * @param CompanySubscription $model
     * @return Collection
     */
    public function includePayments(CompanySubscription $model): Collection
    {
        return $this->collection($model->getPayments(), new PaymentsTransformer());
    }


    /**
     * @param CompanySubscription $model
     * @return Item
     */
    public function includeSubscriptions(CompanySubscription $model): Item
    {
        return $this->item($model->getSubscriptions(), new SubscriptionsTransformer());
    }
}