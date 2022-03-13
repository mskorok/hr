<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Model\Resumes;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class ResumesTransformer
 * @package App\Transformers
 */
class ResumesTransformer extends ModelTransformer
{
    /**
     * Transforms are automatically handled
     * based on your model when you extend ModelTransformer
     * and assign the modelClass property
     */


    public function __construct()
    {
        $this->modelClass = Resumes::class;

        $this->availableIncludes = [
            'JobTypes',
            'ResumeJobTypes',
            'Users',
            'Uploaded',
            'Experience',
            'Education',
            'Avatar',
            'Favorites',
            'FavoredCompanies',
            'Invited',
            'Deals'
        ];
    }

    /**
     * @param Resumes $model
     * @return Collection
     */
    public function includeAvatar(Resumes $model): Collection
    {
        return $this->collection($model->getAvatar(), new ImagesTransformer());
    }

    /**
     * @param Resumes $model
     * @return Collection
     */
    public function includeDeals(Resumes $model): Collection
    {
        return $this->collection($model->getDeals(), new DealsTransformer());
    }

    /**
     * @param Resumes $model
     * @return Collection
     */
    public function includeEducation(Resumes $model): Collection
    {
        return $this->collection($model->getEducation(), new EducationTransformer());
    }

    /**
     * @param Resumes $model
     * @return Collection
     */
    public function includeExperience(Resumes $model): Collection
    {
        return $this->collection($model->getExperience(), new ProfessionalExperienceTransformer());
    }

    /**
     * @param Resumes $model
     * @return Collection
     */
    public function includeFavorites(Resumes $model): Collection
    {
        return $this->collection($model->getFavorites(), new FavoriteResumeTransformer());
    }

    /**
     * @param Resumes $model
     * @return Collection
     */
    public function includeFavoredCompanies(Resumes $model): Collection
    {
        return $this->collection($model->getFavoredCompanies(), new CompaniesTransformer());
    }

    /**
     * @param Resumes $model
     * @return Collection
     */
    public function includeJobTypes(Resumes $model): Collection
    {
        return $this->collection($model->getJobTypes(), new JobTypesTransformer());
    }

    /**
     * @param Resumes $model
     * @return Collection
     */
    public function includeInvited(Resumes $model): Collection
    {
        return $this->collection($model->getInvited(), new InvitedTransformer());
    }

    /**
     * @param Resumes $model
     * @return Collection
     */
    public function includeResumeJobTypes(Resumes $model): Collection
    {
        return $this->collection($model->getResumeJobTypes(), new ResumeJobTypesTransformer());
    }

    /**
     * @param Resumes $model
     * @return Item
     */
    public function includeUsers(Resumes $model): Item
    {
        return $this->item($model->getUsers(), new UsersTransformer());
    }

    /**
     * @param Resumes $model
     * @return Item
     */
    public function includeUploaded(Resumes $model): Item
    {
        return $this->item($model->getUploaded(), new ImagesTransformer());
    }
}
