<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Model\Invited;
use League\Fractal\Resource\Item;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class InvitedTransformer
 * @package App\Transformers
 */
class InvitedTransformer extends ModelTransformer
{
    /**
     * Transforms are automatically handled
     * based on your model when you extend ModelTransformer
     * and assign the modelClass property
     */


    public function __construct()
    {
        $this->modelClass = Invited::class;

        $this->availableIncludes = [
            'Users',
            'Companies',
            'Resumes'
        ];
    }

    /**
     * @param Invited $model
     * @return Item
     */
    public function includeUsers(Invited $model): Item
    {
        return $this->item($model->getUsers(), new UsersTransformer());
    }

    /**
     * @param Invited $model
     * @return Item
     */
    public function includeCompanies(Invited $model): Item
    {
        return $this->item($model->getCompanies(), new CompaniesTransformer());
    }

    /**
     * @param Invited $model
     * @return Item
     */
    public function includeResumes(Invited $model): Item
    {
        return $this->item($model->getResumes(), new ResumesTransformer());
    }
}
