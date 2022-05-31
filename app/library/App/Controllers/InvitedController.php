<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Constants\Limits;
use App\Model\Companies;
use App\Model\FavoriteResume;
use App\Model\Invited;
use App\Model\Resumes;
use App\Model\Users;
use App\User\Service;
use Phalcon\Mvc\Model\Query\Builder;
use Phalcon\Mvc\Model\Resultset\Simple;
use Phalcon\Paginator\Factory;


/**
 * Class FavoritesController
 * @package App\Controllers
 */
class InvitedController extends ControllerBase
{
    public static $availableIncludes = [
        'Companies',
        'Resumes',
        'Users'
    ];

    /**
     * @param $user
     * @param $resume
     * @return mixed
     */
    public function addInvited($user, $resume)
    {
        $id = $user;
        /** @var Users $user */
        $user = Users::find((int) $user)[0];
        if (!$user) {
            return $this->createErrorResponse('User not found');
        }
        /** @var Simple $companies */
        $companies = $user->getCompanies();
        $company = $companies[0];
        if ($company instanceof Companies) {
            $invited = new Invited();
            $invited->setCompanyId((int) $company->getId());
            $invited->setResumeId((int) $resume);
            $invited->setUserId((int) $id);
            if ($invited->save()) {
                return $this->createOkResponse();
            }

            return $this->createErrorResponse('Model not saved');
        }
        return $this->createErrorResponse('Company not found');
    }

    /**
     * @param $user
     * @param $resume
     * @return mixed
     */
    public function removeInvited($user, $resume)
    {
        $id = $user;
        $user = Users::findFirst((int) $user);
        if (!$user) {
            return $this->createErrorResponse('User not found');
        }
        /** @var Simple $companies */
        $companies = $user->getCompanies();
        /** @var Companies $company */
        $company = $companies[0];
        if ($company instanceof Companies) {
            $invited = Invited::findFirst(['user_id' => $id, 'resume_id' => $resume, 'company_id' => $company->getId()]);
            if ($invited instanceof Invited) {
                if ($invited->delete()) {
                    return $this->createOkResponse();
                }

                return $this->createErrorResponse('Model not deleted');
            }
        }

        return $this->createErrorResponse('Model not found user=' . $id . ' resume=' . $resume);
    }

    /**
     * @param $page
     * @return mixed
     */
    public function listInvited($page)
    {
        /** @var Service $userService */
        $userService = $this->userService;
        $id = $userService->getIdentity();
        $builder = new Builder();
        $builder->addFrom(Resumes::class);
        $builder->leftJoin(
            Invited::class,
            '[' . Invited::class . '].[resume_id] = [' . Resumes::class . '].[id]'
        );
        $builder->where('[' . Invited::class . '].[user_id] = :user:', ['user' => $id]);

        $options = [
            'builder' => $builder,
            'limit'   => Limits::SEARCH_LIMIT,
            'page'    => (int) $page,
            'adapter' => 'queryBuilder',
        ];

        $paginator = Factory::load($options);

        $page = $paginator->getPaginate();

        $items = $page->items;

        $_items = [];

        /** @var Resumes $item */
        foreach ($items as $item) {
            $_item = new \stdClass();

            $_item->id = $item->getId();
            $_item->user = $item->getUsers();
            $_item->cv = $item->getUploaded();
            $_item->currency = $item->getCurrency();
            $_item->salary = $item->getSalary();
            $_item->work_place = $item->getWorkPlace();
            $_item->certification = $item->getCertification();
            $_item->position = html_entity_decode($item->getPosition());
            $_item->professional_area = html_entity_decode($item->getProfessionalArea());
            $_item->key_skills = html_entity_decode($item->getKeySkills());
            $_item->language = html_entity_decode($item->getLanguage());
            $_item->about_me = html_entity_decode($item->getAboutMe());
            $_item->location = html_entity_decode($item->getLocation());
            $_item->date = $item->getCreationDate();
            $_item->modifiedDate = $item->getModifiedDate();

            $birthday = new \DateTime($_item->user->getBirthday());
            $now = new \DateTime();

            $diff = date_diff($now, $birthday);
            $_item->age = $diff->y;

            $_items[] = $_item;
        }

        $pagesInRange = $this->getPaginationRange($page);

        $data = [
            'resumes'       => $_items,
            'totalItems'    => $page->total_items,
            'totalPages'    => $page->total_pages,
            'limit'         => $page->limit,
            'current'       => $page->current,
            'before'        => $page->before,
            'next'          => $page->next,
            'last'          => $page->last,
            'first'         => $this->firstPage,
            'pagesRange'    => $pagesInRange,
            'bottomInRange' => $this->bottomInRange,
            'topInRange'    => $this->topInRange
        ];

        return $this->createArrayResponse($data, 'data');
    }

}
