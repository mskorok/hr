<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Model\CompanyManager;
use League\Fractal\Resource\Item;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class CompanyManagerTransformer
 * @package App\Transformers
 */
class CompanyManagerTransformer extends ModelTransformer
{
    /**
     * Transforms are automatically handled
     * based on your model when you extend ModelTransformer
     * and assign the modelClass property
     */


    public function __construct()
    {
        $this->modelClass = CompanyManager::class;

        $this->availableIncludes = [
            'Companies',
            'Users'
        ];
    }

    /**
     * @param CompanyManager $model
     * @return Item
     */
    public function includeCompanies(CompanyManager $model): Item
    {
        return $this->item($model->getCompanies(), new CompaniesTransformer());
    }

    /**
     * @param CompanyManager $model
     * @return Item
     */
    public function includeUsers(CompanyManager $model): Item
    {
        return $this->item($model->getUsers(), new UsersTransformer());
    }
}
