<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 18.10.17
 * Time: 19:38
 */

namespace App\Data;

use PhalconApi\Data\Query;

/**
 * Class SanitizedQuery
 * @package App\Data
 */
class SanitizedQuery extends Query
{
    /**
     * @param null $offset
     * @return $this|void
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    /**
     * @param null $limit
     * @return $this|void
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * @param array $fields
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
    }

    /**
     * @param array $conditions
     */
    public function setConditions($conditions)
    {
        $this->conditions = $conditions;
    }

    /**
     * @param array $sorters
     */
    public function setSorters($sorters)
    {
        $this->sorters = $sorters;
    }

    /**
     * @param array $excludes
     */
    public function setExcludes($excludes)
    {
        $this->excludes = $excludes;
    }
}