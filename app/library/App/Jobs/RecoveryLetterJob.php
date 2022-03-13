<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 28.10.17
 * Time: 14:46
 */

namespace App\Jobs;

use App\Constants\Services;
use App\Mail\MailService;
use App\Model\Users;

/**
 * Class RecoveryLetterJob
 * @package App\Jobs
 */
class RecoveryLetterJob extends BaseJob
{
    public function execute(): void
    {
        $logPath = $this->di->get(Services::CONFIG)->beanstalk->log;
        $log = $logPath.'beanstalk.log';
        $errorLog = $logPath.'beanstalk-error.log';
        $id = $this->getId();
        $user = Users::findFirst((int) $id);
        if ($user instanceof Users) {
            /** @var MailService $mailer */
            $mailer = $this->di->get(Services::MAIL);
            try {
                $mailer->sendRecoveryLetter($user);
                file_put_contents($log, $this->getId().' recovery letter sent'.PHP_EOL, FILE_APPEND | LOCK_EX);
            } catch (\RuntimeException $exception) {
                file_put_contents($errorLog, $exception->getMessage().PHP_EOL, FILE_APPEND | LOCK_EX);
            }
        } else {
            file_put_contents($errorLog, 'user not found'.PHP_EOL, FILE_APPEND | LOCK_EX);
        }
    }
}
