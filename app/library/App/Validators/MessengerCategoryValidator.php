<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 25.09.17
 * Time: 6:32
 */

namespace App\Validators;

use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Alpha;

/**
 * Phalcon\Validation
 *
 * Allows to validate data using custom or built-in validators
 */
class MessengerCategoryValidator extends Validation
{
    public function initialize(): void
    {
        $this->add(
            [
                'name'
            ],
            new Alpha(
                [
                    'message' => ':field must contain only alpha characters'
                ]
            )
        );

        $this->add(
            [
                'name'
            ],
            new PresenceOf(
                [
                    'message' => 'The :field is required'
                ]
            )
        );
    }
}
