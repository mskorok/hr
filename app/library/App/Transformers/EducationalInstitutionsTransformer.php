<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Model\EducationalInstitutions;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class EducationalInstitutionsTransformer
 * @package App\Transformers
 */
class EducationalInstitutionsTransformer extends ModelTransformer
{
    /**
     * Transforms are automatically handled
     * based on your model when you extend ModelTransformer
     * and assign the modelClass property
     */


    public function __construct()
    {
        $this->modelClass = EducationalInstitutions::class;

        $this->availableIncludes = [
            'Countries',
            'EducationInstitutionLevel',
            'EducationLevel'
        ];
    }

    /**
     * @param EducationalInstitutions $model
     * @return Item
     */
    public function includeCountries(EducationalInstitutions $model): Item
    {
        return $this->item($model->getCountries(), new CountriesTransformer());
    }


    /**
     * @param EducationalInstitutions $model
     * @return Collection
     */
    public function includeEducationInstitutionLevel(EducationalInstitutions $model): Collection
    {
        return $this->collection($model->getEducationInstitutionLevel(), new EducationInstitutionLevelTransformer());
    }


    /**
     * @param EducationalInstitutions $model
     * @return Collection
     */
    public function includeCompanies(EducationalInstitutions $model): Collection
    {
        return $this->collection($model->getLevels(), new EducationLevelTransformer());
    }
}
