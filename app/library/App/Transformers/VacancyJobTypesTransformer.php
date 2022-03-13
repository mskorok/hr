<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Model\VacancyJobTypes;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class MailTransformer
 * @package App\Transformers
 */
class VacancyJobTypesTransformer extends ModelTransformer
{
    public function __construct()
    {
        $this->modelClass = VacancyJobTypes::class;
    }
}
