<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 18.07.18
 * Time: 19:21
 */

namespace App\Jobs;

use App\Constants\Message;
use App\Constants\Services;
use App\Mail\MailService;
use Phalcon\Queue\Beanstalk;

/**
 * Class NotificationLetterJob
 * @package App\Jobs
 */
class NotificationLetterJob extends BaseLetterJob
{
    /**
     * NotificationLetterJob constructor.
     * @param Beanstalk $queue
     * @param $id
     * @param $body
     */
    public function __construct(Beanstalk $queue, $id, $body)
    {
        parent::__construct($queue, $id, $body);

        $this->name = Message::NOTIFICATION;
        $this->description = ' Notification letter sent';
    }

    public function execute(): void
    {
        $logPath = $this->di->get(Services::CONFIG)->beanstalk->log;
        $log = $logPath.'beanstalk.log';
        $errorLog = $logPath.'beanstalk-error.log';
        $body = $this->getBody();
        /** @var MailService $mailer */
        $mailer = $this->di->get(Services::MAIL);
        try {
            $mailer->sendAutoResponderLetter($body);
            file_put_contents($log, $this->getId(). $this->description . PHP_EOL, FILE_APPEND | LOCK_EX);
        } catch (\RuntimeException $exception) {
            file_put_contents($errorLog, $exception->getMessage().PHP_EOL, FILE_APPEND | LOCK_EX);
        }
    }
}
