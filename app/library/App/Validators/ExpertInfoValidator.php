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
use Phalcon\Validation\Validator\Digit;
use Phalcon\Validation\Validator\StringLength;

/**
 * Phalcon\Validation
 *
 * Allows to validate data using custom or built-in validators
 */
class ExpertInfoValidator extends Validation
{
    public function initialize(): void
    {
        $this->add(
            [
                'level'
            ],
            new Alpha(
                [
                    'message' => ':field must contain only alpha characters'
                ]
            )
        );
        $this->add(
            [
                'skills',
                'level'
            ],
            new StringLength(
                [
                    'max'            => 255,
                    'min'            => 2,
                    'messageMaximum' => ':field must be no more then 255 chars',
                    'messageMinimum' => ':field must be more than 2 chars'
                ]
            )
        );

        $this->add(
            [
                'user_id'

            ],
            new Digit(
                [
                    'message' => ':field must be numeric'
                ]
            )
        );

        $this->add(
            [
                'level',
                'user_id',
                'skills'


            ],
            new PresenceOf(
                [
                    'message' => 'The :field is required'
                ]
            )
        );
    }
}
