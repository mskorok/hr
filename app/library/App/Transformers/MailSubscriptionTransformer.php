<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Model\MailSubscription;
use League\Fractal\Resource\Collection;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class MailSubscriptionTransformer
 * @package App\Transformers
 */
class MailSubscriptionTransformer extends ModelTransformer
{
    /**
     * Transforms are automatically handled
     * based on your model when you extend ModelTransformer
     * and assign the modelClass property
     */


    public function __construct()
    {
        $this->modelClass = MailSubscription::class;

        $this->availableIncludes = [
            'Users',
            'Vacancies'
        ];
    }


    /**
     * @param MailSubscription $model
     * @return Collection
     */
    public function includeCategories(MailSubscription $model): Collection
    {
        return $this->collection($model->getCategories(), new CategoriesTransformer());
    }

}
