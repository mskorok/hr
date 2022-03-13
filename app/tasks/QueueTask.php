<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: mike
 * Date: 28.10.17
 * Time: 17:09
 */

use App\Constants\Services;
use Phalcon\Cli\Task;

/**
 * {@inheritDoc}
 */
class QueueTask extends Task
{
    public function executeAction()
    {
        /** @var \Phalcon\Queue\Beanstalk $queue */
        $queue = $this->getDI()->get(Services::QUEUE);


        while (true) {
            /** @var \Phalcon\Queue\Beanstalk\Job $res */
            while (($res = $queue->peekReady()) !== false) {
                /** @var \App\Jobs\BaseJob $job */
                $job = $res->getBody();
                if ($job instanceof \App\Jobs\BaseJob) {
                    $job->setDi($this->getDI());
                    $job->execute();
                }
                $res->delete();
                echo time().PHP_EOL;
                sleep(2);
            }
        }
    }
}
