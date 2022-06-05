<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Constants\AclRoles;
use App\Constants\Limits;
use App\Constants\Message;
use App\Model\Companies;
use App\Model\Invited;
use App\Model\Messages;
use App\Model\Resumes;
use App\Model\Users;
use Phalcon\Mvc\Model\Query\Builder;
use Phalcon\Paginator\Factory;
use PhalconApi\Exception;


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
     * @param $resumeId
     * @return mixed
     * @throws Exception
     */
    public function addInvited($resumeId)
    {
        /** @var Users $user */
        $user = $this->userService->getDetails();
        if (!$user) {
            return $this->createErrorResponse('User not found');
        }

        if (!in_array($user->getRole(), AclRoles::COMPANY_ROLES, true)) {
            return $this->createErrorResponse('User has no permission');
        }

        $companies = $this->request->getQuery('companies', 'string');
        if (!$companies) {
            return $this->createErrorResponse('Companies ids empty');
        }
        $companies = explode(',', $companies);
        $userCompanies = $user->getCompanies();
        $messages = [];

        $resume = Resumes::findFirst((int) $resumeId);



        /** @var Companies $company */
        foreach ($userCompanies as $company) {
            if (in_array($company->getId(), $companies, false)) {
                $invited = new Invited();
                $invited->setCompanyId((int) $company->getId());
                $invited->setResumeId((int) $resumeId);
                $invited->setUserId((int) $user->getId());
                if (!$invited->save()) {
                    $messages[] = $invited->getMessages();
                } else {

                    $text =  'Dear user! You invited to be candidate to work at ' . $company->getName();
                    $message = new Messages();
                    $message->setSender($user->getId());
                    $message->setRecipient($resume->getUserId());
                    $message->setCategories(Message::INVITATIONS);
                    $message->setTitle('Invitation');
                    $message->setContent($text);
                    $message->save();
                }
            }
        }

        if (count($messages) === 0) {
            return $this->createOkResponse();
        }

        $mes = implode(',', $messages);

        return $this->createErrorResponse($mes);
    }

    /**
     * @param $resume
     * @return mixed
     * @throws Exception
     */
    public function removeInvited($resume)
    {

        /** @var Users $user */
        $user = $this->userService->getDetails();
        if (!$user) {
            return $this->createErrorResponse('User not found');
        }

        $companies = $this->request->getQuery('companies', 'string');
        if (!$companies) {
            return $this->createErrorResponse('Companies ids empty');
        }
        $companies = explode(',', $companies);
        $userCompanies = $user->getCompanies();
        $messages = [];

        /** @var Companies $company */
        foreach ($userCompanies as $company) {
            if (in_array($company->getId(), $companies, false)) {
                $invited = Invited::findFirst([
                    'conditions' => ' user_id = :uid: AND resume_id = :rid: AND company_id = :cid: ',
                    'bind' => [
                        'uid' =>  $user->getId(),
                        'rid' => $resume,
                        'cid' => $company->getId(),
                    ]
                ]);

                if ($invited instanceof Invited) {
                    if (!$invited->delete()) {
                        $messages[] = $invited->getMessages();
                    }
                } else {
                    $messages[] = 'Invited id = ' . $resume . ' not found';
                }
            }
        }

        if (count($messages) === 0) {
            return $this->createOkResponse();
        }

        $mes = implode(',', $messages);

        return $this->createErrorResponse($mes);
    }

    /**
     * @param $page
     * @return mixed
     * @throws \Exception
     */
    public function listInvited($page)
    {
        $id = $this->userService->getIdentity();

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
