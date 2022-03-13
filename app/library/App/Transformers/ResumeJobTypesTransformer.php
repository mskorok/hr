<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Model\ResumeJobTypes;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class MailTransformer
 * @package App\Transformers
 */
class ResumeJobTypesTransformer extends ModelTransformer
{
    public function __construct()
    {
        $this->modelClass = ResumeJobTypes::class;
    }
}
