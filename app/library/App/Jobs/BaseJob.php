<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 30.10.17
 * Time: 16:23
 */

namespace App\Jobs;

use Phalcon\Queue\Beanstalk\Job;

/**
 * Class BaseJob
 * @package App\Jobs
 */
abstract class BaseJob extends Job
{
    protected $di;

    /**
     * @return mixed
     */
    abstract public function execute();

    /**
     * @return mixed
     */
    public function getDi()
    {
        return $this->di;
    }

    /**
     * @param mixed $di
     */
    public function setDi($di): void
    {
        $this->di = $di;
    }
}