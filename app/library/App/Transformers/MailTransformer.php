<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Model\Mail;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class MailTransformer
 * @package App\Transformers
 */
class MailTransformer extends ModelTransformer
{
    public function __construct()
    {
        $this->modelClass = Mail::class;
    }
}
