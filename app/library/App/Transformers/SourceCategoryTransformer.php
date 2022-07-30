<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Model\SourceCategory;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class SourceCategoryTransformer
 * @package App\Transformers
 */
class SourceCategoryTransformer extends ModelTransformer
{
    /**
     * Transforms are automatically handled
     * based on your model when you extend ModelTransformer
     * and assign the modelClass property
     */


    public function __construct()
    {
        $this->modelClass = SourceCategory::class;

        $this->availableIncludes = [
            'Articles',
            'ArticleSource'
        ];
    }

    /**
     * @param SourceCategory $model
     * @return Collection
     */
    public function includeArticles(SourceCategory $model): Collection
    {
        return $this->collection($model->getArticles(), new ArticlesTransformer());
    }


    /**
     * @param SourceCategory $model
     * @return Item
     */
    public function includeArticleSource(SourceCategory $model): Item
    {
        return $this->item($model->getArticleSource(), new ArticleSourceTransformer());
    }
}
