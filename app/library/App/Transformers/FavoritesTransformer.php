<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 09.05.19
 * Time: 11:20
 */

namespace App\Transformers;

use App\Model\Favorites;
use League\Fractal\Resource\Item;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class FavoritesTransformer
 * @package App\Transformers
 */
class FavoritesTransformer extends ModelTransformer
{

    /**
     * FavoritesTransformer constructor.
     */
    public function __construct()
    {
        $this->modelClass = Favorites::class;

        $this->availableIncludes = [
            'Users',
            'Vacancies'
        ];
    }

    /**
     * @param Favorites $model
     * @return Item
     */
    public function includeCompanies(Favorites $model): Item
    {
        return $this->item($model->getUsers(), new UsersTransformer());
    }

    /**
     * @param Favorites $model
     * @return Item
     */
    public function includeVacancies(Favorites $model): Item
    {
        return $this->item($model->getVacancies(), new VacanciesTransformer());
    }
}