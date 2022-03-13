<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 09.05.19
 * Time: 11:20
 */

namespace App\Transformers;

use App\Model\FavoriteResume;
use League\Fractal\Resource\Item;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class FavoriteResumeTransformer
 * @package App\Transformers
 */
class FavoriteResumeTransformer extends ModelTransformer
{

    /**
     * FavoritesTransformer constructor.
     */
    public function __construct()
    {
        $this->modelClass = FavoriteResume::class;

        $this->availableIncludes = [
            'Companies',
            'Resumes',
            'User'
        ];
    }

    /**
     * @param FavoriteResume $model
     * @return Item
     */
    public function includeCompanies(FavoriteResume $model): Item
    {
        return $this->item($model->getCompany(), new CompaniesTransformer());
    }

    /**
     * @param FavoriteResume $model
     * @return Item
     */
    public function includeResumes(FavoriteResume $model): Item
    {
        return $this->item($model->getResumes(), new ResumesTransformer());
    }

    /**
     * @param FavoriteResume $model
     * @return Item
     */
    public function includeUsers(FavoriteResume $model): Item
    {
        return $this->item($model->getUser(), new UsersTransformer());
    }
}