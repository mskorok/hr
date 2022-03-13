<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Model\Owner;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class OwnerTransformer
 * @package App\Transformers
 */
class OwnerTransformer extends ModelTransformer
{
    /**
     * Transforms are automatically handled
     * based on your model when you extend ModelTransformer
     * and assign the modelClass property
     */


    public function __construct()
    {
        $this->modelClass = Owner::class;

        $this->availableIncludes = [
        ];
    }
}
