<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Model\Tag;
use League\Fractal\Resource\Collection;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class TagTransformer
 * @package App\Transformers
 */
class TagTransformer extends ModelTransformer
{
    /**
     * Transforms are automatically handled
     * based on your model when you extend ModelTransformer
     * and assign the modelClass property
     */


    public function __construct()
    {
        $this->modelClass = Tag::class;

        $this->availableIncludes = [
            'Articles'
        ];
    }

    /**
     * @param Tag $model
     * @return Collection
     */
    public function includeArticles(Tag $model): Collection
    {
        return $this->collection($model->getArticles(), new ArticlesTransformer());
    }
}
