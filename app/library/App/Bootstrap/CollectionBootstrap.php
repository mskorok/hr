<?php
declare(strict_types=1);

namespace App\Bootstrap;

use App\BootstrapInterface;
use App\Collections\ExportCollection;
use App\Resources\ArticleImagesResource;
use App\Resources\ArticlesResource;
use App\Resources\ArticlesTranslatedResource;
use App\Resources\CandidatesResource;
use App\Resources\CategoriesResource;
use App\Resources\CommentsResource;
use App\Resources\CompaniesResource;
use App\Resources\CompanySubscriptionResource;
use App\Resources\CompanyManagerResource;
use App\Resources\CountriesResource;
use App\Resources\DealsResource;
use App\Resources\EducationResource;
use App\Resources\ExpertInfoResource;
use App\Resources\FavoriteResumeResource;
use App\Resources\FavoritesResource;
use App\Resources\ImagesResource;
use App\Resources\LanguagesResource;
use App\Resources\MailResource;
use App\Resources\MessagesResource;
use App\Resources\MessengerCategoryResource;
use App\Resources\PartnerInfoResource;
use App\Resources\PaymentsResource;
use App\Resources\ProfessionalExperienceResource;
use App\Resources\ResumesResource;
use App\Resources\ScheduleResource;
use App\Resources\SettingsResource;
use App\Resources\SkillsResource;
use App\Resources\SubcategoryResource;
use App\Resources\SubscriptionsResource;
use App\Resources\TagResource;
use App\Resources\TeachersResource;
use App\Resources\UsersResource;
use App\Resources\UserSubscriptionResource;
use App\Resources\VacanciesResource;
use Phalcon\Config;
use Phalcon\DiInterface;
use PhalconApi\Exception;
use PhalconRest\Api;
use Psy\Exception\RuntimeException;

/**
 * Class CollectionBootstrap
 * @package App\Bootstrap
 */
class CollectionBootstrap implements BootstrapInterface
{
    /**
     * @param Api $api
     * @param DiInterface $di
     * @param Config $config
     * @throws \Psy\Exception\RuntimeException
     */
    public function run(Api $api, DiInterface $di, Config $config): void
    {
        try {
            $api
                ->collection(new ExportCollection('/export'))
                ->resource(new ArticlesResource($config->application->adapterPath . '/articles'))
                ->resource(new ArticlesTranslatedResource($config->application->adapterPath . '/articles-translated'))
                ->resource(new ArticleImagesResource($config->application->adapterPath . '/article-images'))
                ->resource(new CandidatesResource($config->application->adapterPath . '/candidates'))
                ->resource(new CategoriesResource($config->application->adapterPath . '/categories'))
                ->resource(new CommentsResource($config->application->adapterPath . '/comments'))
                ->resource(new CompaniesResource($config->application->adapterPath . '/companies'))
                ->resource(new CompanySubscriptionResource($config->application->adapterPath . '/company_subscription'))
                ->resource(new CompanyManagerResource($config->application->adapterPath . '/company_manager'))
                ->resource(new CountriesResource($config->application->adapterPath . '/countries'))
                ->resource(new DealsResource($config->application->adapterPath . '/deals'))
                ->resource(new EducationResource($config->application->adapterPath . '/education'))
                ->resource(new ExpertInfoResource($config->application->adapterPath . '/expert_info'))
                ->resource(new FavoritesResource($config->application->adapterPath . '/favorites'))
                ->resource(new FavoriteResumeResource($config->application->adapterPath . '/favorite-resume'))
                ->resource(new ImagesResource($config->application->adapterPath . '/images'))
                ->resource(new LanguagesResource($config->application->adapterPath . '/languages'))
                ->resource(new MailResource($config->application->adapterPath . '/mail'))
                ->resource(new MessagesResource($config->application->adapterPath . '/messages'))
                ->resource(new MessengerCategoryResource($config->application->adapterPath . '/messenger_category'))
                ->resource(new PartnerInfoResource($config->application->adapterPath . '/partners'))
                ->resource(new PaymentsResource($config->application->adapterPath . '/payments'))
                ->resource(new ProfessionalExperienceResource($config->application->adapterPath . '/experience'))
                ->resource(new ResumesResource($config->application->adapterPath . '/resumes'))
                ->resource(new SettingsResource($config->application->adapterPath . '/settings'))
                ->resource(new ScheduleResource($config->application->adapterPath . '/schedule'))
                ->resource(new SkillsResource($config->application->adapterPath . '/skills'))
                ->resource(new SubscriptionsResource($config->application->adapterPath . '/subscriptions'))
                ->resource(new SubcategoryResource($config->application->adapterPath . '/subcategory'))
                ->resource(new TagResource($config->application->adapterPath . '/tag'))
                ->resource(new TeachersResource($config->application->adapterPath . '/teachers'))
                ->resource(new UsersResource($config->application->adapterPath . '/users'))
                ->resource(new UserSubscriptionResource($config->application->adapterPath . '/user_subscription'))
                ->resource(new VacanciesResource($config->application->adapterPath . '/vacancies'));
        } catch (Exception $exception) {
            throw new RuntimeException($exception->getMessage());
        }
    }
}
