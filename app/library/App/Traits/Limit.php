<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 04.12.17
 * Time: 16:44
 */

namespace App\Traits;

use App\Constants\Limits;
use League\Fractal\Pagination\PaginatorInterface;
use Phalcon\Mvc\Model\Query\Builder;
use Phalcon\Http\Request;

/**
 * Trait Limit
 * @package App\Traits
 */
trait Limit
{
    protected $limit = Limits::SEARCH_LIMIT;

    protected $pageRange = Limits::PAGE_RANGE;

    protected $firstPage = Limits::FIRST_PAGE;

    protected $bottomInRange = 1;

    protected $topInRange = 1;

    /**
     * @param Builder $query
     */
    protected function addLimit(Builder $query): void
    {
        /** @var Request $request */
        $request = $this->request;
        $limit = (int)$request->getQuery('limit');
        if (!$limit || $limit > $this->limit) {
            $query->limit($this->limit);
        }
    }

    /**
     * @param \stdClass $page
     * @return array
     */
    protected function getPaginationRange(\stdClass $page): array
    {
        $first = $this->firstPage;
        $current = $page->current;
        $last = $page->last;


        $bottom = $current - $this->pageRange;
        $top = $current + $this->pageRange + 1;

        if ($bottom <= $first) {
            $bottom = $first + 1;
            if (($top + 1) < $last && ($top - $bottom) < $this->pageRange) {
                $top++;
            }
        }

        if ($top >= $last) {
            $top = $last - 1;
        }

        if ($bottom > $top) {
            $this->bottomInRange = 0;
            $this->topInRange = 0;
            return [];
        }

        $this->bottomInRange = $bottom;
        $this->topInRange = $top;

        $res = [];

        for ($i = $bottom; $i <= $top; $i++) {
            $res[] = $i;
        }


        return $res;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     */
    public function setLimit($limit): void
    {
        $this->limit = $limit;
    }

    /**
     * @return int
     */
    public function getPageRange(): int
    {
        return $this->pageRange;
    }

    /**
     * @param int $pageRange
     */
    public function setPageRange(int $pageRange): void
    {
        $this->pageRange = $pageRange;
    }

    /**
     * @return int
     */
    public function getFirstPage(): int
    {
        return $this->firstPage;
    }

    /**
     * @param int $firstPage
     */
    public function setFirstPage(int $firstPage): void
    {
        $this->firstPage = $firstPage;
    }

    /**
     * @return int
     */
    public function getBottomInRange(): int
    {
        return $this->bottomInRange;
    }

    /**
     * @param int $bottomInRange
     */
    public function setBottomInRange(int $bottomInRange): void
    {
        $this->bottomInRange = $bottomInRange;
    }

    /**
     * @return int
     */
    public function getTopInRange(): int
    {
        return $this->topInRange;
    }

    /**
     * @param int $topInRange
     */
    public function setTopInRange(int $topInRange): void
    {
        $this->topInRange = $topInRange;
    }
}
