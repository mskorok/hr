<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 07.11.17
 * Time: 22:33
 */

namespace App\Transformers;

use App\Model\Payments;
use League\Fractal\Resource\Item;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class PaymentsTransformer
 * @package App\Transformers
 */
class PaymentsTransformer extends ModelTransformer
{
    public function __construct()
    {
        $this->modelClass = Payments::class;

        $this->availableIncludes = [
            'Companies',
            'Users',
            'CompanySubscriptions',
            'UserSubscriptions'
        ];
    }

    /**
     * Transforms are automatically handled
     * based on your model when you extend ModelTransformer
     * and assign the modelClass property
     */

    /**
     * @param Payments $model
     * @return Item
     */
    public function includeCompanies(Payments $model): Item
    {
        return $this->item($model->getCompanies(), new CompaniesTransformer());
    }

    /**
     * @param Payments $model
     * @return Item
     */
    public function includeUsers(Payments $model): Item
    {
        return $this->item($model->getUsers(), new UsersTransformer());
    }

    /**
     * @param Payments $model
     * @return Item
     */
    public function includeCompanySubscriptions(Payments $model): Item
    {
        return $this->item($model->getCompanySubscriptions(), new CompanySubscriptionTransformer());
    }

    /**
     * @param Payments $model
     * @return Item
     */
    public function includeUserSubscriptions(Payments $model): Item
    {
        return $this->item($model->getUserSubscriptions(), new UserSubscriptionTransformer());
    }
}
