<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Model\EducationLevel;
use League\Fractal\Resource\Collection;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class EducationLevelTransformer
 * @package App\Transformers
 */
class EducationLevelTransformer extends ModelTransformer
{
    /**
     * Transforms are automatically handled
     * based on your model when you extend ModelTransformer
     * and assign the modelClass property
     */


    public function __construct()
    {
        $this->modelClass = EducationLevel::class;

        $this->availableIncludes = [
            'EducationInstitutionLevel',
        ];
    }
    /**
     * @param EducationLevel $model
     * @return Collection
     */
    public function includeEducationInstitutionLevel(EducationLevel $model): Collection
    {
        return $this->collection($model->getEducationInstitutionLevel(), new EducationInstitutionLevelTransformer());
    }
}
