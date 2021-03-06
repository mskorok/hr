<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Constants\Limits;
use App\Model\Categories;
use App\Model\Subcategory;
use Phalcon\Mvc\Model\Query\Builder;
use Phalcon\Validation\Message\Group;
use RuntimeException;

/**
 * Class CategoriesController
 * @package App\Controllers
 */
class CategoriesController extends ControllerBase
{

    public static $availableIncludes = [
        'Articles',
        'Images'
    ];

    public static $encodedFields = [
        'name'
    ];

    /**
     * @param $name
     * @return mixed
     */
    public function getSubcategories($name)
    {
        $numberPage = $this->request->getQuery('page', 'int', 1);
        $country = $this->request->getQuery('country', 'int', 0);
        $query = $this->request->getQuery('q', 'string', '');
        $query =  htmlspecialchars($query);

        if (empty($numberPage)) {
            $numberPage = 0;
        }


        if (empty($name) || strlen($name) < 3) {
            $this->flashSession->warning('Short category name');
            return $this->response->redirect('/');
        }

        $category = Categories::findFirst('name = "' . $name . '"');

        if (!$category) {
            $this->flashSession->warning('Category not found');
            return $this->response->redirect('/');
        }
        $subcategories = $category->getSubcategory();


        $ids = [];

        /** @var Subcategory $sub */
        foreach ($subcategories as $sub) {
            $ids[] = $sub->getId();
        }


        $builder = new Builder();
        $builder->from(Subcategory::class);

        if ($country) {
            $builder->where("country_id = :country:",
                [
                    'country' => $country
                ]);
        }

        if ($query) {
            $builder->andWhere('title LIKE "%' . $query . '%" OR description LIKE "%' . $query . '%" OR text LIKE "%' . $query . '%"');
        }

        $builder->inWhere('id', $ids);

        $paginator = new \Phalcon\Paginator\Adapter\QueryBuilder([
            'builder' => $builder,
            'limit' => Limits::SEARCH_LIMIT,
            'page' => $numberPage
        ]);
        $page = $paginator->getPaginate();

        $test = $paginator->paginate();

//        $items = $page->items->toArray();

        $items = $page->items;

        $collection = [];

        foreach ($items as $item) {
            $item->setTitle(html_entity_decode($item->getTitle()));
            $item->setDescription(html_entity_decode($item->getDescription()));
            $item->setText(html_entity_decode($item->getText()));
            $collection[] = $item;
        }


        $pagesInRange = $this->getPaginationRange($page);

        $items = $this->getComplexArray($collection);

        $data = [
            'subcategory' => $items,
            'totalItems' => $page->total_items,
            'totalPages' => $page->total_pages,
            'limit' => $page->limit,
            'current' => $page->current,
            'before' => $page->before,
            'next' => $page->next,
            'last' => $page->last,
            'first' => $this->firstPage,
            'pagesRange' => $pagesInRange,
            'bottomInRange' => $this->bottomInRange,
            'topInRange' => $this->topInRange,
            'test' => $test,
        ];

        return $this->createArrayResponse($data, 'data');
    }

    /**
     * @return mixed
     */
    public function getAll() {
        $categories = Categories::find();
        return $this->createArrayResponse($categories, 'categories');
    }


    /*************** PROTECTED   *********************/

    /**
     *
     */
    protected function beforeHandle(): void
    {
        $this->messages = new Group();
    }

    /**
     * @param $data
     * @return mixed
     * @throws RuntimeException
     */
    protected function onDataInvalid($data)
    {
        $mes = [];
        foreach ($this->messages as $message) {
            $mes[] = $message->getMessage();
        }

        return $this->createErrorResponse($mes);
    }
}
