<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Model\MessengerCategory;
use League\Fractal\Resource\Collection;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class MessengerCategoryTransformer
 * @package App\Transformers
 */
class MessengerCategoryTransformer extends ModelTransformer
{

    public function __construct()
    {
        $this->modelClass = MessengerCategory::class;

        $this->availableIncludes = [
            'Messages'
        ];
    }

    /**
     * @param MessengerCategory $model
     * @return Collection
     */
    public function includeMessages(MessengerCategory $model): Collection
    {
        return $this->collection($model->getMessages(), new MessagesTransformer());
    }
}
