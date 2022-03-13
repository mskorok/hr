<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 30.10.17
 * Time: 16:23
 */

namespace App\Jobs;

use App\Constants\Message;
use App\Constants\Services;
use App\Mail\MailService;
use App\Model\Users;

/**
 * Class BaseLetterJob
 * @package App\Jobs
 */
class BaseLetterJob extends BaseJob
{
    protected $name = Message::THANK_YOU;

    protected $description = ' Thank you letter sent';

    /**
     *
     * $queue = $this->getDI()->get(Services::QUEUE);
     * $id = 1;
     *
     * $body = null;
     *
     * $job = new ConfirmationLetterJob($queue, $id, $body);
     * $queue->put($job);
     */
    public function execute()
    {
        $logPath = $this->di->get(Services::CONFIG)->beanstalk->log;
        $log = $logPath.'beanstalk.log';
        $errorLog = $logPath.'beanstalk-error.log';
        $id = $this->getId();
        $body = $this->getBody();
        $user = Users::findFirst((int) $id);
        if ($user instanceof Users) {
            /** @var MailService $mailer */
            $mailer = $this->di->get(Services::MAIL);
            try {
                $mailer->sendLetter($user, $this->name, $body);
                file_put_contents($log, $this->getId(). $this->description . PHP_EOL, FILE_APPEND | LOCK_EX);
            } catch (\RuntimeException $exception) {
                file_put_contents($errorLog, $exception->getMessage().PHP_EOL, FILE_APPEND | LOCK_EX);
            }
        } else {
            file_put_contents($errorLog, 'user not found'.PHP_EOL, FILE_APPEND | LOCK_EX);
        }
    }
}
