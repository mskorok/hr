<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Model\Vacancies;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class VacanciesTransformer
 * @package App\Transformers
 */
class VacanciesTransformer extends ModelTransformer
{
    /**
     * Transforms are automatically handled
     * based on your model when you extend ModelTransformer
     * and assign the modelClass property
     */


    public function __construct()
    {
        $this->modelClass = Vacancies::class;

        $this->availableIncludes = [
            'Applicants',
            'Companies',
            'CompanyAvatar',
            'Favorites',
            'JobTypes',
            'VacancyJobTypes',
            'Country',
            'Applied',
            'Deals'

        ];
    }

    /**
     * @param Vacancies $model
     * @return Collection
     */
    public function includeApplied(Vacancies $model): Collection
    {
        return $this->collection($model->getApplied(), new AppliedTransformer());
    }

    /**
     * @param Vacancies $model
     * @return Collection
     */
    public function includeApplicants(Vacancies $model): Collection
    {
        return $this->collection($model->getApplicants(), new UsersTransformer());
    }

    /**
     * @param Vacancies $model
     * @return Item
     */
    public function includeCountry(Vacancies $model): Item
    {
        return $this->item($model->getCountries(), new CountriesTransformer());
    }

    /**
     * @param Vacancies $model
     * @return Collection
     */
    public function includeCompanyAvatar(Vacancies $model): Collection
    {
        return $this->collection($model->getCompanyAvatar(), new ImagesTransformer());
    }

    /**
     * @param Vacancies $model
     * @return Item
     */
    public function includeCompanies(Vacancies $model): Item
    {
        return $this->item($model->getCompanies(), new CompaniesTransformer());
    }

    /**
     * @param Vacancies $model
     * @return Item
     */
    public function includeDeals(Vacancies $model): Item
    {
        return $this->item($model->getDeals(), new DealsTransformer());
    }

    /**
     * @param Vacancies $model
     * @return Collection
     */
    public function includeFavorites(Vacancies $model): Collection
    {
        return $this->collection($model->getFavorites(), new FavoritesTransformer());
    }

    /**
     * @param Vacancies $model
     * @return Collection
     */
    public function includeJobTypes(Vacancies $model): Collection
    {
        return $this->collection($model->getJobTypes(), new JobTypesTransformer());
    }

    /**
     * @param Vacancies $model
     * @return Collection
     */
    public function includeVacancyJobTypes(Vacancies $model): Collection
    {
        return $this->collection($model->getVacancyJobTypes(), new VacancyJobTypesTransformer());
    }
}
