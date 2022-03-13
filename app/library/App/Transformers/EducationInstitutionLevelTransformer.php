<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Model\EducationInstitutionLevel;
use League\Fractal\Resource\Item;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class EducationInstitutionLevelTransformer
 * @package App\Transformers
 */
class EducationInstitutionLevelTransformer extends ModelTransformer
{
    /**
     * Transforms are automatically handled
     * based on your model when you extend ModelTransformer
     * and assign the modelClass property
     */


    public function __construct()
    {
        $this->modelClass = EducationInstitutionLevel::class;

        $this->availableIncludes = [
            'EducationalInstitutions',
            'EducationLevel',
        ];
    }

    /**
     * @param EducationInstitutionLevel $model
     * @return Item
     */
    public function includeEducationalInstitutions(EducationInstitutionLevel $model): Item
    {
        return $this->item($model->getEducationalInstitutions(), new EducationalInstitutionsTransformer());
    }


    /**
     * @param EducationInstitutionLevel $model
     * @return Item
     */
    public function includeEducationLevel(EducationInstitutionLevel $model): Item
    {
        return $this->item($model->getEducationLevel(), new EducationLevelTransformer());
    }
}
