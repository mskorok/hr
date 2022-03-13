<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Model\Languages;
use League\Fractal\Resource\Collection;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class LanguagesTransformer
 * @package App\Transformers
 */
class LanguagesTransformer extends ModelTransformer
{
    /**
     * Transforms are automatically handled
     * based on your model when you extend ModelTransformer
     * and assign the modelClass property
     */


    public function __construct()
    {
        $this->modelClass = Languages::class;

        $this->availableIncludes = [
            'ArticlesTranslated',
            'Articles'
        ];
    }

    /**
     * @param Languages $model
     * @return Collection
     */
    public function includeArticles(Languages $model): Collection
    {
        return $this->collection($model->getArticles(), new ArticlesTransformer());
    }

    /**
     * @param Languages $model
     * @return Collection
     */
    public function includeArticlesTranslated(Languages $model): Collection
    {
        return $this->collection($model->getArticlesTranslated(), new ArticlesTranslatedTransformer());
    }
}
