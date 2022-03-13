<?php
declare(strict_types=1);

namespace App\Controllers;


use App\Constants\Limits;
use App\Model\EducationalInstitutions;
use App\Model\EducationInstitutionLevel;
use App\Model\EducationLevel;
use Phalcon\Mvc\Model\Query\Builder;
use Phalcon\Paginator\Adapter\QueryBuilder;

/**
 * Class EducationalInstitutionsController
 * @package App\Controllers
 */
class EducationalInstitutionsController extends ControllerBase
{
    /**
     * @var array
     */
    public static $availableIncludes = [
        'Countries',
        'EducationInstitutionLevel',
        'EducationLevel'
    ];

    /**
     * @return mixed
     */
    public function list()
    {
        $numberPage = $this->request->getQuery('page', 'int', 1);

        if (empty($numberPage)) {
            $numberPage = 0;
        }

        $builder = new Builder();
        $builder->addFrom(EducationalInstitutions::class);
        $builder->leftJoin(
            EducationInstitutionLevel::class,
            '[' . EducationInstitutionLevel::class . '].[institution_id] = [' . EducationalInstitutions::class . '].[id]'
        );
        $builder->leftJoin(
            EducationLevel::class,
            '[' . EducationInstitutionLevel::class . '].[level_id] = [' . EducationLevel::class . '].[id]'
        );


        $paginator = new QueryBuilder([
            'builder' => $builder,
            'limit'=> Limits::SEARCH_LIMIT,
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
            'institutions'   => $items,
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
            'topInRange'    => $this->topInRange,
            'test'          => $test
        ];

        return $this->createArrayResponse($data, 'data');
    }
}