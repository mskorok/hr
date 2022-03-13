<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 07.11.17
 * Time: 22:33
 */

namespace App\Transformers;

use App\Model\ProfessionalExperience;
use League\Fractal\Resource\Item;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class ProfessionalExperienceTransformer
 * @package App\Transformers
 */
class ProfessionalExperienceTransformer extends ModelTransformer
{
    public function __construct()
    {
        $this->modelClass = ProfessionalExperience::class;

        $this->availableIncludes = [
            'Users'
        ];
    }

    /**
     * Transforms are automatically handled
     * based on your model when you extend ModelTransformer
     * and assign the modelClass property
     */


    /**
     * @param ProfessionalExperience $model
     * @return Item
     */
    public function includePaymentCountry(ProfessionalExperience $model): Item
    {
        return $this->item($model->getUsers(), new UsersTransformer());
    }
}
