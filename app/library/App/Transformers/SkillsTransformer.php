<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Model\Skills;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class SkillsTransformer
 * @package App\Transformers
 */
class SkillsTransformer extends ModelTransformer
{

    public function __construct()
    {
        $this->modelClass = Skills::class;
    }
}
