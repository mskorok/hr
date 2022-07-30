<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Model\Articles;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class ArticlesTransformer
 * @package App\Transformers
 */
class ArticlesTransformer extends ModelTransformer
{
    /**
     * Transforms are automatically handled
     * based on your model when you extend ModelTransformer
     * and assign the modelClass property
     */


    public function __construct()
    {
        $this->modelClass = Articles::class;

        $this->availableIncludes = [
            'ArticleImages',
            'Comments',
            'Images',
            'Image',
            'Links',
            'Language',
            'Subcategory',
            'SourceCategory',
            'Tags',
        ];
    }

    /**
     * @param Articles $model
     * @return Collection
     */
    public function includeArticleImages(Articles $model): Collection
    {
        return $this->collection($model->getArticleImages(), new ArticleImagesTransformer());
    }

    /**
     * @param Articles $model
     * @return Item
     */
    public function includeImage(Articles $model): Item
    {
        return $this->item($model->getImage(), new ImagesTransformer());
    }

    /**
     * @param Articles $model
     * @return Item
     */
    public function includeSubcategory(Articles $model): Item
    {
        return $this->item($model->getSubcategory(), new SubcategoryTransformer());
    }

    /**
     * @param Articles $model
     * @return Item
     */
    public function includeSourceCategory(Articles $model): Item
    {
        return $this->item($model->getSourceCategory(), new SourceCategoryTransformer());
    }

    /**
     * @param Articles $model
     * @return Item
     */
    public function includeLanguage(Articles $model): Item
    {
        return $this->item($model->getLanguage(), new LanguagesTransformer());
    }

    /**
     * @param Articles $model
     * @return Collection
     */
    public function includeComments(Articles $model): Collection
    {
        return $this->collection($model->getComments(), new CommentsTransformer());
    }

    /**
     * @param Articles $model
     * @return Collection
     */
    public function includeImages(Articles $model): Collection
    {
        return $this->collection($model->getImages(), new ImagesTransformer());
    }

    /**
     * @param Articles $model
     * @return Collection
     */
    public function includeTag(Articles $model): Collection
    {
        return $this->collection($model->getTags(), new TagTransformer());
    }
}
