<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Model\JobTypes;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class MailTransformer
 * @package App\Transformers
 */
class JobTypesTransformer extends ModelTransformer
{
    public function __construct()
    {
        $this->modelClass = JobTypes::class;
    }
}
