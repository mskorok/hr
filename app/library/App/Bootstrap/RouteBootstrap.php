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
use App\Controllers\InvitedController;
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

        $inviteController = new InvitedController();
        $inviteController->setDI($api->di);

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

        $api->get('/profile/show/{id:[0-9]+}', [$userController, 'showProfile']);
        $api->get('/profile/create', [$userController, 'addProfile']);
        $api->post('/profile/create', [$userController, 'addProfile']);
        $api->get('/profile/update/{id:[0-9]+}', [$userController, 'updateProfile']);
        $api->post('/profile/update/{id:[0-9]+}', [$userController, 'updateProfile']);
        $api->get('/profile/delete/{id:[0-9]+}', [$userController, 'deleteProfile']);
        $api->get('/login', [$userController, 'profileLogin']);
        $api->get('/password/new', [$userController, 'profileNewPassword']);
        $api->get('/password/recovery', [$userController, 'profileLoginRecovery']);

        $api->get('/resume/show/{id:[0-9]+}', [$resumeController, 'showResume']);
        $api->get('/resume/create', [$resumeController, 'addResume']);
        $api->post('/resume/create', [$resumeController, 'addResume']);
        $api->get('/resume/update/{id:[0-9]+}', [$resumeController, 'updateResume']);
        $api->post('/resume/update/{id:[0-9]+}', [$resumeController, 'updateResume']);
        $api->get('/resume/list/{page:[0-9]+}', [$resumeController, 'listAllResumes']);
        $api->get('/resume/invited/{page:[0-9]+}', [$resumeController, 'listInvited']);
        $api->get('/resume/user/list/{page:[0-9]+}', [$resumeController, 'listUserResumes']);
        $api->delete('/resume/delete/{id:[0-9]+}', [$resumeController, 'deleteResume']);
        $api->get('/resume/search', [$resumeController, 'searchResume']);

        $api->get('/resume/invite/{resumeId:[0-9]+}', [$inviteController, 'addInvited']);
        $api->get('/resume/remove/invited/{resume:[0-9]+}', [$inviteController, 'removeInvited']);

        $api->get('/vacancy/show/{id:[0-9]+}', [$vacancyController, 'showVacancy']);
        $api->get('/vacancy/create', [$vacancyController, 'addVacancy']);
        $api->post('/vacancy/create', [$vacancyController, 'addVacancy']);
        $api->get('/vacancy/update/{id:[0-9]+}', [$vacancyController, 'updateVacancy']);
        $api->post('/vacancy/update/{id:[0-9]+}', [$vacancyController, 'updateVacancy']);
        $api->get('/vacancy/list/{page:[0-9]+}', [$vacancyController, 'listAllVacancies']);
        $api->get('/vacancy/me/applied/{page:[0-9]+}', [$vacancyController, 'listMeApplied']);
        $api->get('/vacancy/applied/{page:[0-9]+}', [$vacancyController, 'listApplied']);
        $api->get('/vacancy/user/list/{page:[0-9]+}', [$vacancyController, 'listUserVacancies']);
        $api->delete('/vacancy/delete/{id:[0-9]+}', [$vacancyController, 'deleteVacancy']);
        $api->get('/vacancy/search', [$vacancyController, 'searchVacancy']);
        $api->get('/vacancy/apply/{vacancyId:[0-9]+}', [$vacancyController, 'apply']);


        $api->get('/experience/show/{id:[0-9]+}', [$experienceController, 'showExperience']);
        $api->get('/experience/create', [$experienceController, 'addExperience']);
        $api->post('/experience/create', [$experienceController, 'addExperience']);
        $api->get('/experience/update/{id:[0-9]+}', [$experienceController, 'updateExperience']);
        $api->post('/experience/update/{id:[0-9]+}', [$experienceController, 'updateExperience']);
        $api->get('/experience/user/list', [$experienceController, 'listUserExperiences']);
        $api->delete('/experience/delete/{id:[0-9]+}', [$experienceController, 'deleteExperience']);


        $api->get('/education/show/{id:[0-9]+}', [$educationController, 'showEducation']);
        $api->get('/education/create', [$educationController, 'addEducation']);
        $api->post('/education/create', [$educationController, 'addEducation']);
        $api->get('/education/update/{id:[0-9]+}', [$educationController, 'updateEducation']);
        $api->post('/education/update/{id:[0-9]+}', [$educationController, 'updateEducation']);
        $api->get('/education/user/list', [$educationController, 'listUserEducation']);
        $api->delete('/education/delete/{id:[0-9]+}', [$educationController, 'deleteEducation']);

        $api->get('/company/show/{id:[0-9]+}', [$companyController, 'showCompany']);
        $api->get('/company/list/{page:[0-9]+}', [$companyController, 'listUserCompanies']);
        $api->get('/company/create', [$companyController, 'addCompany']);
        $api->post('/company/create', [$companyController, 'addCompany']);
        $api->get('/company/update/{id:[0-9]+}', [$companyController, 'updateCompany']);
        $api->post('/company/update/{id:[0-9]+}', [$companyController, 'updateCompany']);
        $api->delete('/company/delete/{id:[0-9]+}', [$companyController, 'deleteCompany']);


        $api->get('/favorite/add/{vacancy:[0-9]+}', [$favoriteController, 'addFavorite']);
        $api->get('/favorite/remove/{vacancy:[0-9]+}', [$favoriteController, 'removeFavorite']);
        $api->get('/favorite/list/{page:[0-9]+}', [$favoriteController, 'listFavorites']);

        $api->get('/favorite-resume/add/{resume:[0-9]+}', [$favoriteResumeController, 'addFavorite']);
        $api->get('/favorite-resume/remove/{resume:[0-9]+}', [$favoriteResumeController, 'removeFavorite']);
        $api->get('/favorite-resume/list/{page:[0-9]+}', [$favoriteResumeController, 'listFavorites']);

        $api->get('/home', [$homeController, 'indexAction']);

    }
}
