<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Model\ArticleImages;
use League\Fractal\Resource\Item;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class ArticleImagesTransformer
 * @package App\Transformers
 */
class ArticleImagesTransformer extends ModelTransformer
{
    /**
     * Transforms are automatically handled
     * based on your model when you extend ModelTransformer
     * and assign the modelClass property
     */


    public function __construct()
    {
        $this->modelClass = ArticleImages::class;

        $this->availableIncludes = [
            'Articles',
            'Images'
        ];
    }

    /**
     * @param ArticleImages $model
     * @return Item
     */
    public function includeArticles(ArticleImages $model): Item
    {
        return $this->item($model->getArticles(), new ArticlesTransformer());
    }

    /**
     * @param ArticleImages $model
     * @return Item
     */
    public function includeImages(ArticleImages $model): Item
    {
        return $this->item($model->getImages(), new ImagesTransformer());
    }
}
