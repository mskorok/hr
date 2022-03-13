<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Model\PartnerInfo;
use League\Fractal\Resource\Collection;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class PartnersTransformer
 * @package App\Transformers
 */
class PartnerInfoTransformer extends ModelTransformer
{
    /**
     * Transforms are automatically handled
     * based on your model when you extend ModelTransformer
     * and assign the modelClass property
     */


    public function __construct()
    {
        $this->modelClass = PartnerInfo::class;

        $this->availableIncludes = [
            'Companies'
        ];
    }

    /**
     * @param PartnerInfo $model
     * @return Collection
     */
    public function includeCompanies(PartnerInfo $model): Collection
    {
        return $this->collection($model->getCompanies(), new CompaniesTransformer());
    }
}
