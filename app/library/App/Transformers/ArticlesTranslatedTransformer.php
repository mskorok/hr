<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Model\ArticlesTranslated;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class ArticlesTranslatedTransformer
 * @package App\Transformers
 */
class ArticlesTranslatedTransformer extends ModelTransformer
{
    /**
     * Transforms are automatically handled
     * based on your model when you extend ModelTransformer
     * and assign the modelClass property
     */


    public function __construct()
    {
        $this->modelClass = ArticlesTranslated::class;

        $this->availableIncludes = [
            'Article',
            'Language'
        ];
    }

    /**
     * @param ArticlesTranslated $model
     * @return Collection
     */
    public function includeArticle(ArticlesTranslated $model): Collection
    {
        return $this->collection($model->getArticle(), new ArticlesTransformer());
    }

    /**
     * @param ArticlesTranslated $model
     * @return Item
     */
    public function includeLanguage(ArticlesTranslated $model): Item
    {
        return $this->item($model->getLanguage(), new LanguagesTransformer());
    }
}
