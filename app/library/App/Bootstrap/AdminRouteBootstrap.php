<?php
declare(strict_types=1);

namespace App\Bootstrap;

use App\BootstrapInterface;
use App\Controllers\AdminController;
use App\Controllers\ArticleImagesController;
use App\Controllers\ArticlesController;
use App\Controllers\CommentsController;
use App\Controllers\CompaniesController;
use App\Controllers\CompanyManagerController;
use App\Controllers\CountriesController;
use App\Controllers\DealsController;
use App\Controllers\EducationController;
use App\Controllers\ExpertInfoController;
use App\Controllers\ImagesController;
use App\Controllers\PartnerInfoController;
use App\Controllers\PaymentsController;
use App\Controllers\ProfessionalExperienceController;
use App\Controllers\ResumesController;
use App\Controllers\SkillsController;
use App\Controllers\SubcategoryController;
use App\Controllers\SubscriptionsController;
use App\Controllers\UsersController;
use App\Controllers\UserSubscriptionController;
use App\Controllers\VacanciesController;
use Phalcon\Config;
use Phalcon\DiInterface;
use PhalconRest\Api;

/**
 * Class AdminRouteBootstrap
 * @package App\Bootstrap
 */
class AdminRouteBootstrap implements BootstrapInterface
{
    /**
     * @param Api $api
     * @param DiInterface $di
     * @param Config $config
     */
    public function run(Api $api, DiInterface $di, Config $config): void
    {

        /***************   Admin ROUTES   *************************/

        $adminController = new AdminController();
        $adminController->setDI($api->di);

        $articlesController = new ArticlesController();
        $articlesController->setDI($api->di);

        $articleImagesController = new ArticleImagesController();
        $articleImagesController->setDI($api->di);

        $commentsController = new CommentsController();
        $commentsController->setDI($api->di);

        $companiesController = new CompaniesController();
        $companiesController->setDI($api->di);

        $companyManagerController = new CompanyManagerController();
        $companyManagerController->setDI($api->di);

        $countriesController = new CountriesController();
        $countriesController->setDI($api->di);

        $dealsController = new DealsController();
        $dealsController->setDI($api->di);

        $educationController = new EducationController();
        $educationController->setDI($api->di);

        $expertInfoController = new ExpertInfoController();
        $expertInfoController->setDI($api->di);

        $imagesController = new ImagesController();
        $imagesController->setDI($api->di);

        $partnersController = new PartnerInfoController();
        $partnersController->setDI($api->di);

        $paymentsController = new PaymentsController();
        $paymentsController->setDI($api->di);

        $professionalExperienceController = new ProfessionalExperienceController();
        $professionalExperienceController->setDI($api->di);

        $resumesController = new ResumesController();
        $resumesController->setDI($api->di);

        $skillsController = new SkillsController();
        $skillsController->setDI($api->di);

        $subcategoryController = new SubcategoryController();
        $subcategoryController->setDI($api->di);

        $subscriptionsController = new SubscriptionsController();
        $subscriptionsController->setDI($api->di);

        $userSubscriptionController = new UserSubscriptionController();
        $userSubscriptionController->setDI($api->di);

        $usersController = new UsersController();
        $usersController->setDI($api->di);

        $vacanciesController = new VacanciesController();
        $vacanciesController->setDI($api->di);


        /************ ROUTES *********************************/



        $api->get('/admin/articles/index', [$articlesController, 'indexAction']);

        $api->get('/admin/articles/new', [$articlesController, 'newAction']);

        $api->post('/admin/articles/create', [$articlesController, 'createAction']);

        $api->get('/admin/articles/edit/{id}', [$articlesController, 'editAction']);

        $api->post('/admin/articles/save', [$articlesController, 'saveAction']);

        $api->post('/admin/articles/search', [$articlesController, 'searchAction']);

        $api->get('/admin/articles/delete/{id}', [$articlesController, 'saveAction']);

        $api->get('/admin/articles/list', [$articlesController, 'listAction']);

        $api->get('/admin/articles', [$articlesController, 'indexAction']);

        #--------------------------------------------------------------------------------------------#

        $api->get('/admin/article_images/index', [$articleImagesController, 'indexAction']);

        $api->get('/admin/article_images/new', [$articleImagesController, 'newAction']);

        $api->post('/admin/article_images/create', [$articleImagesController, 'createAction']);

        $api->get('/admin/article_images/edit/{id}', [$articleImagesController, 'editAction']);

        $api->post('/admin/article_images/save', [$articleImagesController, 'saveAction']);

        $api->post('/admin/article_images/search', [$articleImagesController, 'searchAction']);

        $api->get('/admin/article_images/list', [$articleImagesController, 'listAction']);

        $api->get('/admin/article_images/delete/{id}', [$articleImagesController, 'saveAction']);

        $api->get('/admin/article_images', [$articleImagesController, 'indexAction']);

        #--------------------------------------------------------------------------------------------#

        $api->get('/admin/comments/index', [$commentsController, 'indexAction']);

        $api->get('/admin/comments/new', [$commentsController, 'newAction']);

        $api->post('/admin/comments/create', [$commentsController, 'createAction']);

        $api->get('/admin/comments/edit/{id}', [$commentsController, 'editAction']);

        $api->post('/admin/comments/save', [$commentsController, 'saveAction']);

        $api->post('/admin/comments/search', [$commentsController, 'searchAction']);

        $api->get('/admin/comments/list', [$commentsController, 'listAction']);

        $api->get('/admin/comments/delete/{id}', [$commentsController, 'saveAction']);

        $api->get('/admin/comments', [$commentsController, 'indexAction']);


        #--------------------------------------------------------------------------------------------#

        $api->get('/admin/companies/index', [$companiesController, 'indexAction']);

        $api->get('/admin/companies/new', [$companiesController, 'newAction']);

        $api->post('/admin/companies/create', [$companiesController, 'createAction']);

        $api->get('/admin/companies/edit/{id}', [$companiesController, 'editAction']);

        $api->post('/admin/companies/save', [$companiesController, 'saveAction']);

        $api->post('/admin/companies/search', [$companiesController, 'searchAction']);

        $api->get('/admin/companies/list', [$companiesController, 'listAction']);

        $api->get('/admin/companies/delete/{id}', [$companiesController, 'saveAction']);

        $api->get('/admin/companies', [$companiesController, 'indexAction']);


        #--------------------------------------------------------------------------------------------#

        $api->get('/admin/company_manager/index', [$companyManagerController, 'indexAction']);

        $api->get('/admin/company_manager/new', [$companyManagerController, 'newAction']);

        $api->post('/admin/company_manager/create', [$companyManagerController, 'createAction']);

        $api->get('/admin/company_manager/edit/{id}', [$companyManagerController, 'editAction']);

        $api->post('/admin/company_manager/save', [$companyManagerController, 'saveAction']);

        $api->post('/admin/company_manager/search', [$companyManagerController, 'searchAction']);

        $api->get('/admin/company_manager/list', [$companyManagerController, 'listAction']);

        $api->get('/admin/company_manager/delete/{id}', [$companyManagerController, 'saveAction']);

        $api->get('/admin/company_manager', [$companyManagerController, 'indexAction']);

        #--------------------------------------------------------------------------------------------#

        $api->get('/admin/countries/index', [$countriesController, 'indexAction']);

        $api->get('/admin/countries/new', [$countriesController, 'newAction']);

        $api->post('/admin/countries/create', [$countriesController, 'createAction']);

        $api->get('/admin/countries/edit/{id}', [$countriesController, 'editAction']);

        $api->post('/admin/countries/save', [$countriesController, 'saveAction']);

        $api->post('/admin/countries/search', [$countriesController, 'searchAction']);

        $api->get('/admin/countries/list', [$countriesController, 'listAction']);

        $api->get('/admin/countries/delete/{id}', [$countriesController, 'saveAction']);

        $api->get('/admin/countries', [$countriesController, 'indexAction']);

        #--------------------------------------------------------------------------------------------#

        $api->get('/admin/deals/index', [$dealsController, 'indexAction']);

        $api->get('/admin/deals/new', [$dealsController, 'newAction']);

        $api->post('/admin/deals/create', [$dealsController, 'createAction']);

        $api->get('/admin/deals/edit/{id}', [$dealsController, 'editAction']);

        $api->post('/admin/deals/save', [$dealsController, 'saveAction']);

        $api->post('/admin/deals/search', [$dealsController, 'searchAction']);

        $api->get('/admin/deals/list', [$dealsController, 'listAction']);

        $api->get('/admin/deals/delete/{id}', [$dealsController, 'saveAction']);

        $api->get('/admin/deals', [$dealsController, 'indexAction']);


        #--------------------------------------------------------------------------------------------#

        $api->get('/admin/education/index', [$educationController, 'indexAction']);

        $api->get('/admin/education/new', [$educationController, 'newAction']);

        $api->post('/admin/education/create', [$educationController, 'createAction']);

        $api->get('/admin/education/edit/{id}', [$educationController, 'editAction']);

        $api->post('/admin/education/save', [$educationController, 'saveAction']);

        $api->post('/admin/education/search', [$educationController, 'searchAction']);

        $api->get('/admin/education/list', [$educationController, 'listAction']);

        $api->get('/admin/education/delete/{id}', [$educationController, 'saveAction']);

        $api->get('/admin/education', [$educationController, 'indexAction']);

        #--------------------------------------------------------------------------------------------#


        $api->get('/admin/expert_info/index', [$expertInfoController, 'indexAction']);

        $api->get('/admin/expert_info/new', [$expertInfoController, 'newAction']);

        $api->post('/admin/expert_info/create', [$expertInfoController, 'createAction']);

        $api->get('/admin/expert_info/edit/{id}', [$expertInfoController, 'editAction']);

        $api->post('/admin/expert_info/save', [$expertInfoController, 'saveAction']);

        $api->post('/admin/expert_info/search', [$expertInfoController, 'searchAction']);

        $api->get('/admin/expert_info/list', [$expertInfoController, 'listAction']);

        $api->get('/admin/expert_info/delete/{id}', [$expertInfoController, 'saveAction']);

        $api->get('/admin/expert_info', [$expertInfoController, 'indexAction']);

        #--------------------------------------------------------------------------------------------#

        $api->get('/admin/images/index', [$imagesController, 'indexAction']);

        $api->get('/admin/images/new', [$imagesController, 'newAction']);

        $api->post('/admin/images/create', [$imagesController, 'createAction']);

        $api->get('/admin/images/edit/{id}', [$imagesController, 'editAction']);

        $api->post('/admin/images/save', [$imagesController, 'saveAction']);

        $api->post('/admin/images/search', [$imagesController, 'searchAction']);

        $api->get('/admin/images/list', [$imagesController, 'listAction']);

        $api->get('/admin/images/delete/{id}', [$imagesController, 'saveAction']);

        $api->get('/admin/images', [$imagesController, 'indexAction']);

        #--------------------------------------------------------------------------------------------#

        $api->get('/admin/partners/index', [$partnersController, 'indexAction']);

        $api->get('/admin/partners/new', [$partnersController, 'newAction']);

        $api->post('/admin/partners/create', [$partnersController, 'createAction']);

        $api->get('/admin/partners/edit/{id}', [$partnersController, 'editAction']);

        $api->post('/admin/partners/save', [$partnersController, 'saveAction']);

        $api->post('/admin/partners/search', [$partnersController, 'searchAction']);

        $api->get('/admin/partners/list', [$partnersController, 'listAction']);

        $api->get('/admin/partners/delete/{id}', [$partnersController, 'saveAction']);

        $api->get('/admin/partners', [$partnersController, 'indexAction']);

        #--------------------------------------------------------------------------------------------#

        $api->get('/admin/payments/index', [$paymentsController, 'indexAction']);

        $api->get('/admin/payments/new', [$paymentsController, 'newAction']);

        $api->post('/admin/payments/create', [$paymentsController, 'createAction']);

        $api->get('/admin/payments/edit/{id}', [$paymentsController, 'editAction']);

        $api->post('/admin/payments/save', [$paymentsController, 'saveAction']);

        $api->post('/admin/payments/search', [$paymentsController, 'searchAction']);

        $api->get('/admin/payments/list', [$paymentsController, 'listAction']);

        $api->get('/admin/payments/delete/{id}', [$paymentsController, 'saveAction']);

        $api->get('/admin/payments', [$paymentsController, 'indexAction']);

        #--------------------------------------------------------------------------------------------#

        $api->get('/admin/professional_experience/index', [$professionalExperienceController, 'indexAction']);

        $api->get('/admin/professional_experience/new', [$professionalExperienceController, 'newAction']);

        $api->post('/admin/professional_experience/create', [$professionalExperienceController, 'createAction']);

        $api->get('/admin/professional_experience/edit/{id}', [$professionalExperienceController, 'editAction']);

        $api->post('/admin/professional_experience/save', [$professionalExperienceController, 'saveAction']);

        $api->post('/admin/professional_experience/search', [$professionalExperienceController, 'searchAction']);

        $api->get('/admin/professional_experience/list', [$professionalExperienceController, 'listAction']);

        $api->get('/admin/professional_experience/delete/{id}', [$professionalExperienceController, 'saveAction']);

        $api->get('/admin/professional_experience', [$professionalExperienceController, 'indexAction']);

        #--------------------------------------------------------------------------------------------#

        $api->get('/admin/resumes/index', [$resumesController, 'indexAction']);

        $api->get('/admin/resumes/new', [$resumesController, 'newAction']);

        $api->post('/admin/resumes/create', [$resumesController, 'createAction']);

        $api->get('/admin/resumes/edit/{id}', [$resumesController, 'editAction']);

        $api->post('/admin/resumes/save', [$resumesController, 'saveAction']);

        $api->post('/admin/resumes/search', [$resumesController, 'searchAction']);

        $api->get('/admin/resumes/list', [$resumesController, 'listAction']);

        $api->get('/admin/resumes/delete/{id}', [$resumesController, 'saveAction']);

        $api->get('/admin/resumes', [$resumesController, 'indexAction']);

        #--------------------------------------------------------------------------------------------#

        $api->get('/admin/skills/index', [$skillsController, 'indexAction']);

        $api->get('/admin/skills/new', [$skillsController, 'newAction']);

        $api->post('/admin/skills/create', [$skillsController, 'createAction']);

        $api->get('/admin/skills/edit/{id}', [$skillsController, 'editAction']);

        $api->post('/admin/skills/save', [$skillsController, 'saveAction']);

        $api->post('/admin/skills/search', [$skillsController, 'searchAction']);

        $api->get('/admin/skills/list', [$skillsController, 'listAction']);

        $api->get('/admin/skills/delete/{id}', [$skillsController, 'saveAction']);

        $api->get('/admin/skills', [$skillsController, 'indexAction']);

        #--------------------------------------------------------------------------------------------#


        $api->get('/admin/subcategory/index', [$subcategoryController, 'indexAction']);

        $api->get('/admin/subcategory/new', [$subcategoryController, 'newAction']);

        $api->post('/admin/subcategory/create', [$subcategoryController, 'createAction']);

        $api->get('/admin/subcategory/edit/{id}', [$subcategoryController, 'editAction']);

        $api->post('/admin/subcategory/save', [$subcategoryController, 'saveAction']);

        $api->post('/admin/subcategory/search', [$subcategoryController, 'searchAction']);

        $api->get('/admin/subcategory/delete/{id}', [$subcategoryController, 'saveAction']);

        $api->get('/admin/subcategory/list', [$subcategoryController, 'listAction']);

        $api->get('/admin/subcategory', [$subcategoryController, 'indexAction']);

        #--------------------------------------------------------------------------------------------#

        $api->get('/admin/subscriptions/index', [$subscriptionsController, 'indexAction']);

        $api->get('/admin/subscriptions/new', [$subscriptionsController, 'newAction']);

        $api->post('/admin/subscriptions/create', [$subscriptionsController, 'createAction']);

        $api->get('/admin/subscriptions/edit/{id}', [$subscriptionsController, 'editAction']);

        $api->post('/admin/subscriptions/save', [$subscriptionsController, 'saveAction']);

        $api->post('/admin/subscriptions/search', [$subscriptionsController, 'searchAction']);

        $api->get('/admin/subscriptions/list', [$subscriptionsController, 'listAction']);

        $api->get('/admin/subscriptions/delete/{id}', [$subscriptionsController, 'saveAction']);

        $api->get('/admin/subscriptions', [$subscriptionsController, 'indexAction']);

        #--------------------------------------------------------------------------------------------#

        $api->get('/admin/user_subscription/index', [$userSubscriptionController, 'indexAction']);

        $api->get('/admin/user_subscription/new', [$userSubscriptionController, 'newAction']);

        $api->post('/admin/user_subscription/create', [$userSubscriptionController, 'createAction']);

        $api->get('/admin/user_subscription/edit/{id}', [$userSubscriptionController, 'editAction']);

        $api->post('/admin/user_subscription/save', [$userSubscriptionController, 'saveAction']);

        $api->post('/admin/user_subscription/search', [$userSubscriptionController, 'searchAction']);

        $api->get('/admin/user_subscription/list', [$userSubscriptionController, 'listAction']);

        $api->get('/admin/user_subscription/delete/{id}', [$userSubscriptionController, 'saveAction']);

        $api->get('/admin/user_subscription', [$userSubscriptionController, 'indexAction']);

        #--------------------------------------------------------------------------------------------#

        $api->get('/admin/users/index', [$usersController, 'indexAction']);

        $api->get('/admin/users/new', [$usersController, 'newAction']);

        $api->post('/admin/users/create', [$usersController, 'createAction']);

        $api->get('/admin/users/edit/{id}', [$usersController, 'editAction']);

        $api->post('/admin/users/save', [$usersController, 'saveAction']);

        $api->post('/admin/users/search', [$usersController, 'searchAction']);

        $api->get('/admin/users/list', [$usersController, 'listAction']);

        $api->get('/admin/users/delete/{id}', [$usersController, 'saveAction']);

        $api->get('/admin/users', [$usersController, 'indexAction']);

        #--------------------------------------------------------------------------------------------#

        $api->get('/admin/vacancies/index', [$vacanciesController, 'indexAction']);

        $api->get('/admin/vacancies/new', [$vacanciesController, 'newAction']);

        $api->post('/admin/vacancies/create', [$vacanciesController, 'createAction']);

        $api->get('/admin/vacancies/edit/{id}', [$vacanciesController, 'editAction']);

        $api->post('/admin/vacancies/save', [$vacanciesController, 'saveAction']);

        $api->post('/admin/vacancies/search', [$vacanciesController, 'searchAction']);

        $api->get('/admin/vacancies/list', [$vacanciesController, 'listAction']);

        $api->get('/admin/vacancies/delete/{id}', [$vacanciesController, 'saveAction']);

        $api->get('/admin/vacancies', [$vacanciesController, 'indexAction']);

        $api->get('/admin', [$adminController, 'index']);



        /**  ADMIN */
        $api->get('/admin/user/list', [$usersController, 'listUsers']);
        $api->get('/admin/user/add', [$usersController, 'add']);
        $api->post('/admin/user/add', [$usersController, 'add']);
        $api->get('/admin/user/edit/{id}', [$usersController, 'updates']);
        $api->post('/admin/user/edit/{id}', [$usersController, 'updates']);


        $api->get('/admin/company/list', [$companiesController, 'listCompanies']);
        $api->get('/admin/company/add', [$companiesController, 'add']);
        $api->post('/admin/company/add', [$companiesController, 'add']);
        $api->get('/admin/company/edit/{id}', [$companiesController, 'updates']);
        $api->post('/admin/company/edit/{id}', [$companiesController, 'updates']);

        $api->get('/admin/partner/list', [$partnersController, 'listPartners']);
        $api->get('/admin/partner/add', [$partnersController, 'add']);
        $api->post('/admin/partner/add', [$partnersController, 'add']);
        $api->get('/admin/partner/edit/{id}', [$partnersController, 'updates']);
        $api->post('/admin/partner/edit/{id}', [$partnersController, 'updates']);

        $api->get('/admin/vacancy/list', [$vacanciesController, 'listVacancies']);
        $api->get('/admin/vacancy/add', [$vacanciesController, 'add']);
        $api->post('/admin/vacancy/add', [$vacanciesController, 'add']);
        $api->get('/admin/vacancy/edit/{id}', [$vacanciesController, 'updates']);
        $api->post('/admin/vacancy/edit/{id}', [$vacanciesController, 'updates']);

        $api->get('/admin/resume/list', [$resumesController, 'listResumes']);
        $api->get('/admin/resume/add', [$resumesController, 'add']);
        $api->post('/admin/resume/add', [$resumesController, 'add']);
        $api->get('/admin/resume/edit/{id}', [$resumesController, 'updates']);
        $api->post('/admin/resume/edit/{id}', [$resumesController, 'updates']);


        $api->get('/admin/article/list', [$articlesController, 'listArticles']);
        $api->get('/admin/article/add', [$articlesController, 'add']);
        $api->post('/admin/article/add', [$articlesController, 'add']);
        $api->get('/admin/article/edit/{id}', [$articlesController, 'updates']);
        $api->post('/admin/article/edit/{id}', [$articlesController, 'updates']);
    }
}
