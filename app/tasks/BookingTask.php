<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: mike
 * Date: 08.12.17
 * Time: 17:09
 */

use App\Constants\Services;
use App\Model\Users;
use App\Traits\Recipients;
use Phalcon\Cli\Task;
use Phalcon\DiInterface;
use Phalcon\Mvc\Model\Resultset\Simple;
use Phalcon\Queue\Beanstalk;

/**
 * {@inheritDoc}
 */
class BookingTask extends Task
{
    use Recipients;

    /**
     *
     * @throws \RuntimeException
     */
    public function monthInvoices()
    {
        $time = new \DateTime();
        $time->modify('-1 month');
        $timeString = $time->format('Y-m-d');
        $this->putMonthInvoiceJob($timeString);
    }


    /**
     * @param $timeString
     * @return bool
     * @throws \RuntimeException
     */
    protected function putMonthInvoiceJob($timeString): bool
    {
        /** @var \Phalcon\DiInterface $di */
        $di = $this->getDI();
        if (!($di instanceof DiInterface)) {
            throw new \RuntimeException('DI not found');
        }
        /** @var Beanstalk $queue */
        $queue = $this->getDI()->get(Services::QUEUE);

        /** @var Simple $users */
        $users = $this->_getEmployers();
        /** @var Users $user */
        foreach ($users as $user) {
            $body = [
                'recipient' => $user->getName().' '.$user->getSurname(),
                'to' => $user->getEmail(),
                'user' => $user->getId(),
                'item' => $user,
                'time' => $timeString
            ];
            $job = new \App\Jobs\MonthInvoicesLetterJob($queue, $user->getId(), $body);
            $queue->put($job);
        }
        return true;
    }
}
