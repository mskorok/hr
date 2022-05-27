<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Model\Users;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class UsersTransformer
 * @package App\Transformers
 */
class UsersTransformer extends ModelTransformer
{
    /**
     * Transforms are automatically handled
     * based on your model when you extend ModelTransformer
     * and assign the modelClass property
     */


    public function __construct()
    {
        $this->modelClass = Users::class;

        $this->availableIncludes = [
            'Comments',
            'CompanyManager',
            'Education',
            'ExpertInfo',
            'FavoriteVacancies',
            'Favorites',
            'FavoriteResume',
            'ProfessionalExperiences',
            'Recipients',
            'Resumes',
            'Senders',
            'UserSubscription',
            'Images',
            'Companies',
            'Payments',
            'Subscriptions',
            'Teachers',
            'Countries',
            'Applied',
            'Invitations',
            'Invited',
            'AppliedVacancies',
            'FavoriteResumes'
        ];
    }

    /**
     * @return array
     */
    protected function excludedProperties(): array
    {
        $excluded = parent::excludedProperties();
        return array_merge($excluded, ['password']);
    }

    /**
     * @param Users $model
     * @return Collection
     */
    public function includeApplied(Users $model): Collection
    {
        return $this->collection($model->getApplied(), new AppliedTransformer());
    }

    /**
     * @param Users $model
     * @return Collection
     */
    public function includeComments(Users $model): Collection
    {
        return $this->collection($model->getComments(), new CommentsTransformer());
    }

    /**
     * @param Users $model
     * @return Collection
     */
    public function includeCompanyManager(Users $model): Collection
    {
        return $this->collection($model->getCompanyManager(), new CompanyManagerTransformer());
    }

    /**
     * @param Users $model
     * @return Collection
     */
    public function includeEducation(Users $model): Collection
    {
        return $this->collection($model->getEducation(), new EducationTransformer());
    }

    /**
     * @param Users $model
     * @return Item
     */
    public function includeExpertInfo(Users $model): Item
    {
        return $this->item($model->getExpertInfo(), new ExpertInfoTransformer());
    }

    /**
     * @param Users $model
     * @return Item
     */
    public function includeFavorites(Users $model): Item
    {
        return $this->item($model->getFavorites(), new FavoritesTransformer());
    }

    /**
     * @param Users $model
     * @return Collection
     */
    public function includeFavoriteVacancies(Users $model): Collection
    {
        return $this->collection($model->getFavoriteVacancies(), new VacanciesTransformer());
    }

    /**
     * @param Users $model
     * @return Collection
     */
    public function includeFavoriteResume(Users $model): Collection
    {
        return $this->collection($model->getFavoriteResume(), new ResumesTransformer());
    }

    /**
     * @param Users $model
     * @return Collection
     */
    public function includeFavoriteResumes(Users $model): Collection
    {
        return $this->collection($model->getFavoriteResumes(), new ResumesTransformer());
    }

    /**
     * @param Users $model
     * @return Collection
     */
    public function includeInvitations(Users $model): Collection
    {
        return $this->collection($model->getInvitations(), new CompaniesTransformer());
    }

    /**
     * @param Users $model
     * @return Collection
     */
    public function includeInvited(Users $model): Collection
    {
        return $this->collection($model->getInvited(), new InvitedTransformer());
    }

    /**
     * @param Users $model
     * @return Collection
     */
    public function includeProfessionalExperiences(Users $model): Collection
    {
        return $this->collection($model->getProfessionalExperiences(), new ProfessionalExperienceTransformer());
    }

    /**
     * @param Users $model
     * @return Collection
     */
    public function includeRecipients(Users $model): Collection
    {
        return $this->collection($model->getRecipients(), new MessagesTransformer());
    }

    /**
     * @param Users $model
     * @return Collection
     */
    public function includeResumes(Users $model): Collection
    {
        return $this->collection($model->getResumes(), new ResumesTransformer());
    }

    /**
     * @param Users $model
     * @return Collection
     */
    public function includeUserSubscription(Users $model): Collection
    {
        return $this->collection($model->getUserSubscription(), new UserSubscriptionTransformer());
    }

    /**
     * @param Users $model
     * @return Collection
     */
    public function includeSenders(Users $model): Collection
    {
        return $this->collection($model->getSenders(), new MessagesTransformer());
    }

    /**
     * @param Users $model
     * @return Item
     */
    public function includeCountries(Users $model): Item
    {
        return $this->item($model->getCountries(), new CountriesTransformer());
    }

    /**
     * @param Users $model
     * @return Item
     */
    public function includeImages(Users $model): Item
    {
        return $this->item($model->getImages(), new ImagesTransformer());
    }

    /**
     * @param Users $model
     * @return Item
     */
    public function includeTeachers(Users $model): Item
    {
        return $this->item($model->getTeachers(), new TeachersTransformer());
    }

    /**
     * @param Users $model
     * @return Collection
     */
    public function includeCompanies(Users $model): Collection
    {
        return $this->collection($model->getCompanies(), new CompaniesTransformer());
    }

    /**
     * @param Users $model
     * @return Collection
     */
    public function includePayments(Users $model): Collection
    {
        return $this->collection($model->getPayments(), new PaymentsTransformer());
    }

    /**
     * @param Users $model
     * @return Collection
     */
    public function includeSubscriptions(Users $model): Collection
    {
        return $this->collection($model->getSubscriptions(), new SubscriptionsTransformer());
    }

    /**
     * @param Users $model
     * @return Collection
     */
    public function includeAppliedVacancies(Users $model): Collection
    {
        return $this->collection($model->getAppliedVacancies(), new VacanciesTransformer());
    }
}
