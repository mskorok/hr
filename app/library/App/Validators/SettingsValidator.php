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

/**
 * Phalcon\Validation
 *
 * Allows to validate data using custom or built-in validators
 */
class SettingsValidator extends Validation
{
    public function initialize(): void
    {
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

//        $this->add(
//            [
//                'stringData',
//            ],
//            new StringLength(
//                [
//                    'max'            => 255,
//                    'min'            => 2,
//                    'messageMaximum' => ':field must be no more then 255 chars',
//                    'messageMinimum' => ':field must be more than 2 chars',
//                ]
//            )
//        );
//
//        $this->add(
//            [
//                'integerData',
//                'boolData'
//
//            ],
//            new Digit(
//                [
//                    'message' => ':field must be numeric',
//                ]
//            )
//        );
    }
}
