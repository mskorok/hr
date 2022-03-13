<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Constants\Limits;
use App\Model\Favorites;
use App\Model\Vacancies;
use App\User\Service;
use Phalcon\Mvc\Model\Query\Builder;
use Phalcon\Paginator\Factory;


/**
 * Class FavoritesController
 * @package App\Controllers
 */
class FavoritesController extends ControllerBase
{
    public static $availableIncludes = [
        'Users',
        'Vacancies'
    ];

    /**
     * @param $user
     * @param $vacancy
     * @return mixed
     */
    public function addFavorite($user, $vacancy)
    {
        $favorite = new Favorites();
        $favorite->setUserId((int) $user);
        $favorite->setVacancyId((int) $vacancy);

        if ($favorite->save()) {
            return $this->createOkResponse();
        }

        return $this->createErrorResponse('Model not saved');
    }

    /**
     * @param $user
     * @param $vacancy
     * @return mixed
     */
    public function removeFavorite($user, $vacancy)
    {
        $favorite = Favorites::findFirst(['user_id' => $user, 'vacancy_id' => $vacancy]);

        if ($favorite instanceof Favorites) {
            if ($favorite->delete()) {
                return $this->createOkResponse();
            }

            return $this->createErrorResponse('Model not deleted');
        }

        return $this->createErrorResponse('Model not found');
    }

    /**
     * @param $page
     * @return mixed
     */
    public function listFavorites($page)
    {
        /** @var Service $userService */
        $userService = $this->userService;
        $id = $userService->getIdentity();
        $builder = new Builder();
        $builder->addFrom(Vacancies::class);
        $builder->leftJoin(
            Favorites::class,
            '[' . Favorites::class . '].[vacancy_id] = [' . Vacancies::class . '].[id]'
        );
        $builder->where('[' . Favorites::class . '].[user_id] = :user:', ['user' => $id]);




        $options = [
            'builder' => $builder,
            'limit'   => Limits::SEARCH_LIMIT,
            'page'    => (int) $page,
            'adapter' => 'queryBuilder',
        ];

        $paginator = Factory::load($options);

        $page =  $paginator->getPaginate();

        $items = $page->items;

        $_items = [];

        foreach ($items as $item) {
            $_item = $item;
            $_item->name = html_entity_decode($item->name);
            $_item->description = html_entity_decode($item->description);
            $_item->city = html_entity_decode($item->city);
            $_item->professional_experience = html_entity_decode($item->professional_experience);
            $_item->key_skills = html_entity_decode($item->key_skills);
            $_item->location = html_entity_decode($item->location);
            $_item->responsibilities = html_entity_decode($item->responsibilities);
            $_item->main_requirements = html_entity_decode($item->main_requirements);
            $_item->additional_requirements = html_entity_decode($item->additional_requirements);
            $_item->work_conditions = html_entity_decode($item->work_conditions);

            $_items[] = $_item;

        }

        $pagesInRange = $this->getPaginationRange($page);

        $data = [
            'vacancies'     => $_items,
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
