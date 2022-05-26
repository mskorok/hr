<?php
declare(strict_types=1);

namespace App\Bootstrap;

use App\BootstrapInterface;
use App\Constants\Services;
use App\Controllers\ArticlesController;
use App\Controllers\ArticlesTranslatedController;
use App\Controllers\CompaniesController;
use App\Controllers\DashboardController;
use App\Controllers\EducationController;
use App\Controllers\FavoriteResumeController;
use App\Controllers\FavoritesController;
use App\Controllers\HomeController;
use App\Controllers\ProfessionalExperienceController;
use App\Controllers\ResumesController;
use App\Controllers\UsersController;
use App\Controllers\VacanciesController;
use App\Forms\LoginForm;
use Phalcon\Config;
use Phalcon\DiInterface;
use PhalconRest\Api;

/**
 * Class RouteBootstrap
 * @package App\Bootstrap
 */
class RouteBootstrap implements BootstrapInterface
{
    /**
     * @param Api $api
     * @param DiInterface $di
     * @param Config $config
     */
    public function run(Api $api, DiInterface $di, Config $config)
    {
        $api->get('/', function () use ($api) {

            /** @var \Phalcon\Mvc\View\Simple $view */
            $view = $api->di->get(Services::VIEW);
            $form = new LoginForm();
            $form->setAction('/users/authenticate');
            $form->renderForm();

            return $view->render('general/index', ['form' => $form]);
        });

        $api->get('/proxy.html', function () use ($api, $config) {

            /** @var \Phalcon\Mvc\View\Simple $view */
            $view = $api->di->get(Services::VIEW);

            $view->setVar('client', $config->clientHostName);
            return $view->render('general/proxy');
        });

        $api->get('/documentation.html', function () use ($api, $config) {

            /** @var \Phalcon\Mvc\View\Simple $view */
            $view = $api->di->get(Services::VIEW);

            $view->setVar('title', $config->application->title);
            $view->setVar('description', $config->application->description);
            $view->setVar('documentationPath', $config->hostName . '/export/documentation.json');
            return $view->render('general/documentation');
        });

        $dashboardController = new DashboardController($api->getDI());

        $articlesController = new ArticlesController();
        $articlesController->setDI($api->di);

        $articlesTranslatedController = new ArticlesTranslatedController();
        $articlesTranslatedController->setDI($api->di);

        $userController = new UsersController();
        $userController->setDI($api->di);

        $resumeController = new ResumesController();
        $resumeController->setDI($api->di);

        $vacancyController = new VacanciesController();
        $vacancyController->setDI($api->di);

        $experienceController = new ProfessionalExperienceController();
        $experienceController->setDI($api->di);

        $educationController = new EducationController();
        $educationController->setDI($api->di);

        $companyController = new CompaniesController();
        $companyController->setDI($api->di);

        $favoriteController = new FavoritesController();
        $favoriteController->setDI($api->di);

        $favoriteResumeController = new FavoriteResumeController();
        $favoriteResumeController->setDI($api->di);

        $homeController = new HomeController();
        $homeController->setDI($api->di);

        $api->get('/confirm', [$userController, 'confirm']);

        $api->get('/new_password.html', [$userController, 'newPassword']);

        $api->get('/recovery.html', [$dashboardController, 'recovery']);

        /*********************  SITE ROUTES **************************************/

        $api->post('/subscribe/mail', [$userController, 'subscribe']);
        $api->post('/unsubscribe/mail', [$userController, 'subscribe']);


        $api->get('/articles-list', [$articlesController, 'listAllArticles']);
        $api->get('/search/articles', [$articlesController, 'searchArticle']);

        $api->get('/article-translated/{article}/{lang}', [$articlesTranslatedController, 'getTranslated']);

        $api->get('/profile/show/{id}', [$userController, 'showProfile']);
        $api->get('/profile/create', [$userController, 'addProfile']);
        $api->post('/profile/create', [$userController, 'addProfile']);
        $api->get('/profile/update/{id}', [$userController, 'updateProfile']);
        $api->post('/profile/update/{id}', [$userController, 'updateProfile']);
        $api->delete('/profile/delete/{id}', [$userController, 'deleteProfile']);
        $api->get('/login', [$userController, 'profileLogin']);
        $api->get('/password/new', [$userController, 'profileNewPassword']);
        $api->get('/password/recovery', [$userController, 'profileLoginRecovery']);

        $api->get('/resume/show/{id}', [$resumeController, 'showResume']);
        $api->get('/resume/create', [$resumeController, 'addResume']);
        $api->post('/resume/create', [$resumeController, 'addResume']);
        $api->get('/resume/update/{id}', [$resumeController, 'updateResume']);
        $api->post('/resume/update/{id}', [$resumeController, 'updateResume']);
        $api->get('/resume/list/{page}', [$resumeController, 'listAllResumes']);
        $api->get('/resume/invited/{page}', [$resumeController, 'listInvited']);
        $api->get('/resume/user/list/{page}', [$resumeController, 'listUserResumes']);
        $api->delete('/resume/delete/{id}', [$resumeController, 'deleteResume']);
        $api->get('/resume/search', [$resumeController, 'searchResume']);
        $api->get('/resume/invite/{user}/{resume}', [$resumeController, 'invite']);

        $api->get('/vacancy/show/{id}', [$vacancyController, 'showVacancy']);
        $api->get('/vacancy/create', [$vacancyController, 'addVacancy']);
        $api->post('/vacancy/create', [$vacancyController, 'addVacancy']);
        $api->get('/vacancy/update/{id}', [$vacancyController, 'updateVacancy']);
        $api->post('/vacancy/update/{id}', [$vacancyController, 'updateVacancy']);
        $api->get('/vacancy/list/{page}', [$vacancyController, 'listAllVacancies']);
        $api->get('/vacancy/applied/{page}', [$vacancyController, 'listApplied']);
        $api->get('/vacancy/user/list/{page}', [$vacancyController, 'listUserVacancies']);
        $api->delete('/vacancy/delete/{id}', [$vacancyController, 'deleteVacancy']);
        $api->get('/vacancy/search', [$vacancyController, 'searchVacancy']);
        $api->get('/vacancy/apply/{user}/{vacancy}', [$vacancyController, 'apply']);


        $api->get('/experience/show/{id}', [$experienceController, 'showExperience']);
        $api->get('/experience/create', [$experienceController, 'addExperience']);
        $api->post('/experience/create', [$experienceController, 'addExperience']);
        $api->get('/experience/update/{id}', [$experienceController, 'updateExperience']);
        $api->post('/experience/update/{id}', [$experienceController, 'updateExperience']);
        $api->get('/experience/user/list', [$experienceController, 'listUserExperiences']);
        $api->delete('/experience/delete/{id}', [$experienceController, 'deleteExperience']);


        $api->get('/education/show/{id}', [$educationController, 'showEducation']);
        $api->get('/education/create', [$educationController, 'addEducation']);
        $api->post('/education/create', [$educationController, 'addEducation']);
        $api->get('/education/update/{id}', [$educationController, 'updateEducation']);
        $api->post('/education/update/{id}', [$educationController, 'updateEducation']);
        $api->get('/education/user/list', [$educationController, 'listUserEducation']);
        $api->delete('/education/delete/{id}', [$educationController, 'deleteEducation']);

        $api->get('/company/show/{id}', [$companyController, 'showCompany']);
        $api->get('/company/list/{page}', [$companyController, 'listUserCompanies']);
        $api->get('/company/create', [$companyController, 'addCompany']);
        $api->post('/company/create', [$companyController, 'addCompany']);
        $api->get('/company/update/{id}', [$companyController, 'updateCompany']);
        $api->post('/company/update/{id}', [$companyController, 'updateCompany']);
        $api->delete('/company/delete/{id}', [$companyController, 'deleteCompany']);


        $api->get('/favorite/add/{user}/{vacancy}', [$favoriteController, 'addFavorite']);
        $api->get('/favorite/remove/{user}/{vacancy}', [$favoriteController, 'removeFavorite']);
        $api->get('/favorite/list/{page}', [$favoriteController, 'listFavorites']);

        $api->get('/favorite-resume/add/{user}/{resume}', [$favoriteResumeController, 'addFavorite']);
        $api->get('/favorite-resume/remove/{user}/{resume}', [$favoriteResumeController, 'removeFavorite']);
        $api->get('/favorite-resume/list/{page}', [$favoriteResumeController, 'listFavorites']);

        $api->get('/home', [$homeController, 'indexAction']);

    }
}
