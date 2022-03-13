<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Model\ExpertInfo;
use League\Fractal\Resource\Item;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class ExpertInfoTransformer
 * @package App\Transformers
 */
class ExpertInfoTransformer extends ModelTransformer
{
    /**
     * Transforms are automatically handled
     * based on your model when you extend ModelTransformer
     * and assign the modelClass property
     */


    public function __construct()
    {
        $this->modelClass = ExpertInfo::class;

        $this->availableIncludes = [
            'Users'
        ];
    }

    /**
     * @param ExpertInfo $model
     * @return Item
     */
    public function includeUsers(ExpertInfo $model): Item
    {
        return $this->item($model->getUsers(), new UsersTransformer());
    }
}
