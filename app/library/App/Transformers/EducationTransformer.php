<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Model\Education;
use League\Fractal\Resource\Item;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class EducationTransformer
 * @package App\Transformers
 */
class EducationTransformer extends ModelTransformer
{
    /**
     * Transforms are automatically handled
     * based on your model when you extend ModelTransformer
     * and assign the modelClass property
     */


    public function __construct()
    {
        $this->modelClass = Education::class;

        $this->availableIncludes = [
            'Users'
        ];
    }

    /**
     * @param Education $model
     * @return Item
     */
    public function includeUsers(Education $model): Item
    {
        return $this->item($model->getUsers(), new UsersTransformer());
    }
}
