<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Model\CompanyType;
use League\Fractal\Resource\Collection;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class CompanyTypeTransformer
 * @package App\Transformers
 */
class CompanyTypeTransformer extends ModelTransformer
{
    /**
     * Transforms are automatically handled
     * based on your model when you extend ModelTransformer
     * and assign the modelClass property
     */


    public function __construct()
    {
        $this->modelClass = CompanyType::class;

        $this->availableIncludes = [
            'Companies',
        ];
    }

    /**
     * @param CompanyType $model
     * @return Collection
     */
    public function includeCompanies(CompanyType $model): Collection
    {
        return $this->collection($model->getCompanies(), new CompaniesTransformer());
    }
}
