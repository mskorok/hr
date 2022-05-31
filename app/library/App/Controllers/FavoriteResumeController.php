<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Constants\Limits;
use App\Model\FavoriteResume;
use App\Model\Resumes;
use App\Model\Users;
use Phalcon\Mvc\Model\Query\Builder;
use Phalcon\Paginator\Factory;
use PhalconApi\Exception;


/**
 * Class FavoritesController
 * @package App\Controllers
 */
class FavoriteResumeController extends ControllerBase
{
    public static $availableIncludes = [
        'Companies',
        'Resumes',
        'Users'
    ];

    /**
     * @param $resume
     * @return mixed
     * @throws Exception
     */
    public function addFavorite($resume)
    {
        /** @var Users $user */
        $user = $this->userService->getDetails();
        if (!$user) {
            return $this->createErrorResponse('User not found');
        }

        $favorite = new FavoriteResume();
        $favorite->setResumeId((int) $resume);
        $favorite->setUserId((int) $user->getId());
        if ($favorite->save()) {
            return $this->createOkResponse();
        }
        return $this->createErrorResponse($favorite->getMessages());
    }

    /**
     * @param $resume
     * @return mixed
     * @throws Exception
     */
    public function removeFavorite($resume)
    {
        /** @var Users $user */
        $user = $this->userService->getDetails();
        if (!$user) {
            return $this->createErrorResponse('User not found');
        }
        $favorite = FavoriteResume::findFirst([
            'conditions' => ' user_id = :uid: AND resume_id = :rid: ',
            'bind' => [
                'uid' =>  $user->getId(),
                'rid' => $resume,
            ]
        ]);

        if ($favorite instanceof FavoriteResume) {
            if ($favorite->delete()) {
                return $this->createOkResponse();
            }

            return $this->createErrorResponse($favorite->getMessages());
        }

        return $this->createErrorResponse('Model not found user=' . $user->getId() . ' resume=' . $resume);
    }

    /**
     * @param $page
     * @return mixed
     * @throws \Exception
     */
    public function listFavorites($page)
    {
        $id = $this->userService->getIdentity();
        $builder = new Builder();
        $builder->addFrom(Resumes::class);
        $builder->leftJoin(
            FavoriteResume::class,
            '[' . FavoriteResume::class . '].[resume_id] = [' . Resumes::class . '].[id]'
        );
        $builder->where('[' . FavoriteResume::class . '].[user_id] = :user:', ['user' => $id]);

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
