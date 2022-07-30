<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Model\ArticleSource;
use League\Fractal\Resource\Collection;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class ArticleSourceTransformer
 * @package App\Transformers
 */
class ArticleSourceTransformer extends ModelTransformer
{
    /**
     * Transforms are automatically handled
     * based on your model when you extend ModelTransformer
     * and assign the modelClass property
     */


    public function __construct()
    {
        $this->modelClass = ArticleSource::class;

        $this->availableIncludes = [
            'SourceCategory',
        ];
    }

    /**
     * @param ArticleSource $model
     * @return Collection
     */
    public function includeSourceCategory(ArticleSource $model): Collection
    {
        return $this->collection($model->getSourceCategory(), new SourceCategoryTransformer());
    }
}
