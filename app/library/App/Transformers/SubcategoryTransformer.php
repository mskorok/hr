<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Model\Subcategory;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class SubcategoryTransformer
 * @package App\Transformers
 */
class SubcategoryTransformer extends ModelTransformer
{
    /**
     * Transforms are automatically handled
     * based on your model when you extend ModelTransformer
     * and assign the modelClass property
     */


    public function __construct()
    {
        $this->modelClass = Subcategory::class;

        $this->availableIncludes = [
            'Category',
            'images',
            'Articles',
            'Countries'
        ];
    }

    /**
     * @param Subcategory $model
     * @return Collection
     */
    public function includeIArticles(Subcategory $model): Collection
    {
        return $this->collection($model->getArticles(), new ArticlesTransformer());
    }

    /**
     * @param Subcategory $model
     * @return Item
     */
    public function includeCategories(Subcategory $model): Item
    {
        return $this->item($model->getCategories(), new CategoriesTransformer());
    }

    /**
     * @param Subcategory $model
     * @return Item
     */
    public function includeImages(Subcategory $model): Item
    {
        return $this->item($model->getImages(), new ImagesTransformer());
    }

    /**
     * @param Subcategory $model
     * @return Item
     */
    public function includeCountries(Subcategory $model): Item
    {
        return $this->item($model->getCountries(), new CountriesTransformer());
    }
}
