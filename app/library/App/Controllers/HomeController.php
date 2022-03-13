<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Model\Articles;
use App\Model\Resumes;
use App\Model\Vacancies;
use PhalconRest\Mvc\Controllers\FractalController;

/**
 * Class HomeController
 * @package App\Controllers
 */
class HomeController extends FractalController
{
    /**
     * Index action
     *
     */
    public function indexAction()
    {
        $vacanciesCount = Vacancies::count();
        $resumesCount = Resumes::count();
        $articlesCount = Articles::count();
        $vacanciesCountriesCount = Vacancies::find(['columns' => 'distinct(country_id)']);

        return $this->createResponse(['articles' => $articlesCount, 'resumes' => $resumesCount, 'vacancies' => $vacanciesCount, 'countries' => count($vacanciesCountriesCount)]);
    }
}
