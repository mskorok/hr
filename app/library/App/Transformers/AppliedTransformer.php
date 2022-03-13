<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Model\Applied;
use League\Fractal\Resource\Item;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class AppliedTransformer
 * @package App\Transformers
 */
class AppliedTransformer extends ModelTransformer
{
    /**
     * Transforms are automatically handled
     * based on your model when you extend ModelTransformer
     * and assign the modelClass property
     */


    public function __construct()
    {
        $this->modelClass = Applied::class;

        $this->availableIncludes = [
            'Users',
            'Vacancies'
        ];
    }

    /**
     * @param Applied $model
     * @return Item
     */
    public function includeUsers(Applied $model): Item
    {
        return $this->item($model->getUsers(), new UsersTransformer());
    }


    /**
     * @param Applied $model
     * @return Item
     */
    public function includeVacancies(Applied $model): Item
    {
        return $this->item($model->getVacancies(), new VacanciesTransformer());
    }
}
