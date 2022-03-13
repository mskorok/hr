<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 08.05.19
 * Time: 11:58
 */

namespace App\Controllers;

use App\Traits\RenderView;

/**
 * Class AdminController
 * @package App\Controllers
 */
class AdminController  extends ControllerBase
{
    use RenderView;
    /**
     * Index action
     * @throws \ReflectionException
     */
    public function index()
    {
        $this->returnView('index');
    }
}