<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Model\Notifications;
use League\Fractal\Resource\Collection;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class NotificationsTransformer
 * @package App\Transformers
 */
class NotificationsTransformer extends ModelTransformer
{
    /**
     * Transforms are automatically handled
     * based on your model when you extend ModelTransformer
     * and assign the modelClass property
     */


    public function __construct()
    {
        $this->modelClass = Notifications::class;

        $this->availableIncludes = [
            'Creator'
        ];
    }

    /**
     * @param Notifications $model
     * @return Collection
     */
    public function includeCreator(Notifications $model): Collection
    {
        return $this->collection($model->getCreator(), new UsersTransformer());
    }
}
