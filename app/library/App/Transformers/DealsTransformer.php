<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Model\Deals;
use League\Fractal\Resource\Item;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class DealsTransformer
 * @package App\Transformers
 */
class DealsTransformer extends ModelTransformer
{
    /**
     * Transforms are automatically handled
     * based on your model when you extend ModelTransformer
     * and assign the modelClass property
     */


    public function __construct()
    {
        $this->modelClass = Deals::class;

        $this->availableIncludes = [
            'Candidates',
            'Companies',
            'Resumes',
            'Vacancies'
        ];
    }

    /**
     * @param Deals $model
     * @return Item
     */
    public function includeCandidates(Deals $model): Item
    {
        return $this->item($model->getCandidates(), new CandidatesTransformer());
    }

    /**
     * @param Deals $model
     * @return Item
     */
    public function includeCompanies(Deals $model): Item
    {
        return $this->item($model->getCompanies(), new CompaniesTransformer());
    }

    /**
     * @param Deals $model
     * @return Item
     */
    public function includeResumes(Deals $model): Item
    {
        return $this->item($model->getResumes(), new ResumesTransformer());
    }

    /**
     * @param Deals $model
     * @return Item
     */
    public function includeVacancies(Deals $model): Item
    {
        return $this->item($model->getVacancies(), new VacanciesTransformer());
    }
}
