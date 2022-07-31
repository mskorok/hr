<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Model\ArticleSource;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
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
            'Articles',
            'Language',
            'SourceCategory',
        ];
    }

    /**
     * @param ArticleSource $model
     * @return Collection
     */
    public function includeArticles(ArticleSource $model): Collection
    {
        return $this->collection($model->getArticles(), new ArticlesTransformer());
    }

    /**
     * @param ArticleSource $model
     * @return Item
     */
    public function includeLanguage(ArticleSource $model): Item
    {
        return $this->item($model->getLanguage(), new LanguagesTransformer());
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
