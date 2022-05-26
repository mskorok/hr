<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 24.09.17
 * Time: 16:01
 */

namespace App\Traits;

use App\Constants\Services;
use Phalcon\Mvc\View;
use ReflectionClass;

/**
 * Trait RenderView
 * @package App\Traits
 */
trait RenderView
{
    protected $currentView;

    /**
     *
     */
    protected function getUserView(): View
    {
        $config = $this->getDI()->get(Services::CONFIG);
        $view = new View();
        $view->setLayoutsDir($config->application->layoutsDir);
        $view->setViewsDir($config->application->viewsDir);
        return $view;
    }


    /**
     * @param string $name
     * @param array|null $params
     * @param bool $var
     * @return string
     */
    protected function returnView(string $name, array $params = null, bool $var = false): string
    {
        $view = $this->getUserView();
        if (!empty($params)) {
            $view->setVars($params);
        }
        $reflect = new ReflectionClass($this);
        $className = $reflect->getShortName();
        $class = strtolower(str_replace('Controller', '', $className));
        $view->start();
        $view->render($class, $name);
        $view->finish();
        if ($var) {
            return $view->getContent();
        }
        echo $view->getContent();
        return '';
    }

    /**
     * @param string $name
     * @param array|null $params
     * @param bool $var
     * @return string
     */
    protected function returnViewWithEmptyLayout(string $name, array $params = null, bool $var = false): string
    {
        $view = $this->getUserView();
        $view->setLayout('empty');
        if (!empty($params)) {
            $view->setVars($params);
        }
        $reflect = new ReflectionClass($this);
        $className = $reflect->getShortName();
        $class = strtolower(str_replace('Controller', '', $className));
        $view->start();
        $view->render($class, $name);
        $view->finish();
        if ($var) {
            return $view->getContent();
        }
        echo $view->getContent();
        return '';
    }


    /**
     * @param string $string
     * @param bool $injection
     * @return string
     */
    protected function sanitize(string $string, bool $injection = false): string
    {
        $jsInjection = $injection ? '<forbidden JavaScript tags>' : '';
        $htmlInjection = $injection ? '<forbidden  HTML tags>' : '';
        $string = strip_tags($string);
        $string = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is',
            $jsInjection, $string);
        $string = preg_replace('/<iframe\b[^>]*>(.*?)<\/iframe>/is',
            $htmlInjection, $string);


        $string = addslashes($string);
        return htmlentities($string);
    }
}
