<?php
declare(strict_types=1);

namespace App;

/**
 * Class Bootstrap
 * @package App
 */
class Bootstrap
{
    protected $_executables;

    /**
     * Bootstrap constructor.
     * @param mixed ...$executables
     */
    public function __construct(...$executables)
    {
        $this->_executables = $executables;
    }

    /**
     * @param mixed ...$args
     */
    public function run(...$args): void
    {
        foreach ($this->_executables as $executable) {
            \call_user_func_array([$executable, 'run'], $args);
        }
    }
}
