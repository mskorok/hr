<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Model\Categories;
use League\Fractal\Resource\Collection;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class CategoriesTransformer
 * @package App\Transformers
 */
class CategoriesTransformer extends ModelTransformer
{
    /**
     * Transforms are automatically handled
     * based on your model when you extend ModelTransformer
     * and assign the modelClass property
     */


    public function __construct()
    {
        $this->modelClass = Categories::class;

        $this->availableIncludes = [
            'Subcategory',
            'MailSubscription'
        ];
    }

    /**
     * @param Categories $model
     * @return Collection
     */
    public function includeSubcategory(Categories $model): Collection
    {
        return $this->collection($model->getSubcategory(), new SubcategoryTransformer());
    }

    /**
     * @param Categories $model
     * @return Collection
     */
    public function includeMailSubscription(Categories $model): Collection
    {
        return $this->collection($model->getMailSubscription(), new MailSubscriptionTransformer());
    }
}
