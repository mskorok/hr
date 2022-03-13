<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 28.10.17
 * Time: 14:46
 */

namespace App\Jobs;

use App\Constants\Message;
use Phalcon\Queue\Beanstalk;

/**
 * Class ThankYouLetterJob
 * @package App\Jobs
 */
class ThankYouLetterJob extends BaseLetterJob
{

    /**
     * ThankYouLetterJob constructor.
     * @param Beanstalk $queue
     * @param $id
     * @param $body
     */
    public function __construct(Beanstalk $queue, $id, $body)
    {
        parent::__construct($queue, $id, $body);

        $this->name = Message::THANK_YOU;
        $this->description = ' Thank you letter sent';
    }
}
