<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Model\Settings;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class SettingsTransformer
 * @package App\Transformers
 */
class SettingsTransformer extends ModelTransformer
{

    public function __construct()
    {
        $this->modelClass = Settings::class;
    }
}
