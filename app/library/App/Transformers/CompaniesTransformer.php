<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Model\Companies;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class CompaniesTransformer
 * @package App\Transformers
 */
class CompaniesTransformer extends ModelTransformer
{
    /**
     * Transforms are automatically handled
     * based on your model when you extend ModelTransformer
     * and assign the modelClass property
     */


    public function __construct()
    {
        $this->modelClass = Companies::class;

        $this->availableIncludes = [
            'CompanyManager',
            'CompanySubscription',
            'Countries',
            'Subscriptions',
            'Vacancies',
            'Users',
            'Type',
            'Images',
            'Invited',
            'Candidates',
            'Payments'
        ];
    }

    /**
     * @param Companies $model
     * @return Collection
     */
    public function includeCandidates(Companies $model): Collection
    {
        return $this->collection($model->getCandidates(), new CompanyManagerTransformer());
    }

    /**
     * @param Companies $model
     * @return Collection
     */
    public function includeCompanyManager(Companies $model): Collection
    {
        return $this->collection($model->getCompanyManager(), new CompanyManagerTransformer());
    }

    /**
     * @param Companies $model
     * @return Collection
     */
    public function includeInvited(Companies $model): Collection
    {
        return $this->collection($model->getInvited(), new InvitedTransformer());
    }

    /**
     * @param Companies $model
     * @return Collection
     */
    public function includeCompanySubscription(Companies $model): Collection
    {
        return $this->collection($model->getCompanySubscription(), new CompanySubscriptionTransformer());
    }

    /**
     * @param Companies $model
     * @return Item
     */
    public function includeCountries(Companies $model): Item
    {
        return $this->item($model->getCountries(), new CountriesTransformer());
    }

    /**
     * @param Companies $model
     * @return Collection
     */
    public function includePayments(Companies $model): Collection
    {
        return $this->collection($model->getPayments(), new PaymentsTransformer());
    }

    /**
     * @param Companies $model
     * @return Collection
     */
    public function includeSubscriptions(Companies $model): Collection
    {
        return $this->collection($model->getSubscriptions(), new SubscriptionsTransformer());
    }

    /**
     * @param Companies $model
     * @return Collection
     */
    public function includeVacancies(Companies $model): Collection
    {
        return $this->collection($model->getVacancies(), new VacanciesTransformer());
    }

    /**
     * @param Companies $model
     * @return Collection
     */
    public function includeUsers(Companies $model): Collection
    {
        return $this->collection($model->getUsers(), new UsersTransformer());
    }

    /**
     * @param Companies $model
     * @return Item
     */
    public function includeImages(Companies $model): Item
    {
        return $this->item($model->getImages(), new ImagesTransformer());
    }

    /**
     * @param Companies $model
     * @return Item
     */
    public function includeType(Companies $model): Item
    {
        return $this->item($model->getCompanyType(), new CompanyTypeTransformer());
    }
}
