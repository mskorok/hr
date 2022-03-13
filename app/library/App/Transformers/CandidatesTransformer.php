<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Model\Candidates;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class CandidatesTransformer
 * @package App\Transformers
 */
class CandidatesTransformer extends ModelTransformer
{
    /**
     * Transforms are automatically handled
     * based on your model when you extend ModelTransformer
     * and assign the modelClass property
     */


    public function __construct()
    {
        $this->modelClass = Candidates::class;

        $this->availableIncludes = [
            'Companies', 'Deals'
        ];
    }

    /**
     * @param Candidates $model
     * @return Item
     */
    public function includeCompanies(Candidates $model): Item
    {
        return $this->item($model->getCompanies(), new CompaniesTransformer());
    }

    /**
     * @param Candidates $model
     * @return Collection
     */
    public function includeDeals(Candidates $model): Collection
    {
        return $this->collection($model->getDeals(), new DealsTransformer());
    }
}
