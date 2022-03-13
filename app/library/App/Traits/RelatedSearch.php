<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 19.10.17
 * Time: 22:31
 */

namespace App\Traits;

use App\Constants\Services;
use App\Controllers\ControllerBase;
use App\Data\AppParser;
use App\Data\SanitizedQuery;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Query\Builder;
use Phalcon\Mvc\Model\Resultset\Simple;
use PhalconApi\Data\Query\Condition;
use PhalconRest\Api\ApiResource;
use Phalcon\Http\Request;

/**
 * Trait RelatedSearch
 * @package App\Traits
 */
trait RelatedSearch
{

    /**
     * @param null|ControllerBase $className
     * @return array
     */
    protected function sanitizeFields($className = null)
    {
        $fields = $this->query->getFields();
        $modelFields = $className && property_exists($className, 'searchFields')
            ? $className::$searchFields['fields']
            : static::$searchFields['fields'];
        return array_intersect($fields, $modelFields);
    }

    /**
     * @param null|ControllerBase $className
     * @return array
     */
    protected function sanitizeExcludes($className = null)
    {
        $excludes = $this->query->getExcludes();
        $modelFields = $className && property_exists($className, 'searchFields')
            ? $className::$searchFields['fields']
            : static::$searchFields['fields'];
        return array_intersect($excludes, $modelFields);
    }

    /**
     * @param null|ControllerBase $className
     * @return array
     */
    protected function sanitizeConditions($className = null): array
    {
        /** @var array $conditions */
        $conditions = $this->query->getConditions();
        $modelFields = $className && property_exists($className, 'searchFields')
            ? $className::$searchFields['fields']
            : static::$searchFields['fields'];
        $modelClass = $className && property_exists($className, 'searchFields')
            ? $className::$searchFields['model']
            : static::$searchFields['model'];
        $modelClass = substr($modelClass, strrpos($modelClass, '\\') + 1);
        $modelConditions = [];
        /** @var Condition $condition */
        foreach ($conditions as $condition) {
            $modelField = $condition->getField();
            if (strpos($modelField, '.') !== false) {
                $modelField = str_replace($modelClass.'.', '', $modelField);
            }
            if (\in_array($modelField, $modelFields, true)) {
                $newCondition = clone $condition;
                $newCondition->field = $modelField;
                $modelConditions[] = $newCondition;
            }
        }
        return $modelConditions;
    }

    /**
     * @param null|ControllerBase $className
     * @return array
     */
    protected function relatedFields($className = null): array
    {
        $fields = $this->query->getFields();
        $modelFields = $className && property_exists($className, 'searchFields')
            ? $className::$searchFields['fields']
            : static::$searchFields['fields'];
        return array_diff($fields, $modelFields);
    }

    /**
     * @param array $data
     * @param null $className
     * @return array
     */
    protected function getFieldsForRelatedModel(array $data, $className = null): array
    {
        $alias = $data['alias'].'.';
        $aliasLow = strtolower($alias);
        $fields = $data['fields'];
        $relatedFields = $this->relatedFields($className);
        $modelFields =[];
        foreach ($relatedFields as $field) {
            if (strpos($field, $alias) !== false) {
                $modelFields[] = str_replace($alias, '', $field);
            } elseif (strpos($field, $aliasLow) !== false) {
                $modelFields[] = str_replace($aliasLow, '', $field);
            }
        }

        return array_intersect($fields, $modelFields);
    }


    /**
     * @param Builder $qb
     * @param null|ControllerBase $className
     */
    protected function addModelsToBuilder(Builder $qb, $className = null)
    {
        /** @var array $related */
        $related = $className && property_exists($className, 'searchFields')
            ? $className::$searchFields['related']
            : static::$searchFields['related'];
        foreach ($related as $data) {
            $this->addModelToBuilder($qb, $data);
        }
    }

    /**
     * @param Builder $qb
     * @param $data
     * @return Builder
     */
    protected function addModelToBuilder(Builder $qb, $data): Builder
    {
        $parser = new AppParser();
        $parser->addRelatedModelToBuilder($qb, $this->query, $data);
        return $qb;
    }

    /**
     * @param array $data
     * @param null|ControllerBase $className
     * @return array
     */
    protected function addIncludesToResultArray(array $data, $className = null): array
    {
        $identifier = $className && property_exists($className, 'searchFields')
            ? $className::$searchFields['identifier']
            : static::$searchFields['identifier'];

        $alias = $className && property_exists($className, 'searchFields')
            ? $className::$searchFields['alias']
            : static::$searchFields['alias'];

        $theModel = $className && property_exists($className, 'searchFields')
            ? $className::$searchFields['model']
            : static::$searchFields['model'];

        $availableIncludes = $className && property_exists($className, 'availableIncludes')
            ? $className::$availableIncludes
            : static::$availableIncludes;


        /** @var \League\Fractal\Manager $fractal */
        $fractal = $this->di->get(Services::FRACTAL_MANAGER);
        $includes = $fractal->getRequestedIncludes();
        $key = $alias.'_'.$identifier;
        foreach ($data as &$item) {
            $id = (int) $item[$key];
            $model = $theModel::findFirst($id);
            $modelIncludes =[];
            foreach ($includes as $include) {
                if (!\in_array($include, $availableIncludes, true)) {
                    continue;
                }
                $method = 'get'.$include;
                $mi = $model->$method();
                if ($mi instanceof Simple && $mi->count() > 0) {
                    $modelIncludes[$include][] = $mi->toArray()[0];
                } elseif ($mi instanceof Model) {
                    $modelIncludes[$include][] = $mi->toArray();
                }
            }
            $item['includes'] = $modelIncludes;
        }
        return $data;
    }

    /**
     * @param ApiResource $resource
     * @param bool $modify
     * @param bool $fromController
     * @return mixed
     * @throws \RuntimeException
     */
    protected function modelSearch(ApiResource $resource, $modify = false, $fromController = false)
    {
        $className = null;
        $instance = null;
        $modelName = $resource->getModel();
        if ($modelName && $fromController) {
            $modelShortName = explode('\\', $modelName);
            $className = 'App\\Controllers\\' . $modelShortName[2]. 'Controller';
            $instance = new $className();
        } elseif ($fromController) {
            throw new \RuntimeException('Model not found');
        }

        if (!$fromController) {
            $modelClass = static::$searchFields['model'];
            $alias = static::$searchFields['alias'];
            $modelFields = static::$searchFields['fields'];
        } elseif (!$className) {
            throw new \RuntimeException('Class Name  is not found');
        } else if ($instance instanceof ControllerBase) {
            if (property_exists($className, 'searchFields')) {
                $modelFields = $className::$searchFields['fields'];
                $modelClass = $className::$searchFields['model'];
                $alias = $className::$searchFields['alias'];
            } else {
                throw new \RuntimeException('Property "searchFields" is not exist');
            }
        } else {
            throw new \RuntimeException('Model class not found');
        }

        if (\count($this->query->getFields()) > 0) {
            $modelFields = $this->sanitizeFields($className);
        }

        $columnsString = '';
        foreach ($modelFields as $modelField) {
            $columnsString .= '['.$modelClass.'].['.$modelField.'] AS '.$alias.'_'.$modelField.',';
        }
        $columnsString = rtrim($columnsString, ',');

        $modelConditions = $this->sanitizeConditions($className);
        $modelExcludes = $this->sanitizeExcludes($className);
        $sanitizedQuery = new SanitizedQuery();
        $sanitizedQuery->setFields($modelFields);
        $sanitizedQuery->setConditions($modelConditions);
        $sanitizedQuery->setExcludes($modelExcludes);
        $sanitizedQuery->setLimit($this->query->getLimit());
        $sanitizedQuery->setOffset($this->query->getOffset());
        $sanitizedQuery->setSorters($this->query->getSorters());

        /** @var Builder $phqlBuilder */
        $phqlBuilder = $this->phqlQueryParser->fromQuery($sanitizedQuery, $resource);

        $phqlBuilder->columns($columnsString);
        $this->addModelsToBuilder($phqlBuilder, $className);

        if ($modify) {
            $this->modifySearch($phqlBuilder);
        }
        /** @var Model\Resultset\Simple $result */
        $result = $phqlBuilder->getQuery()->execute();
        $result = $result->toArray();
        /** @var array $result */
        $result = array_unique($result, SORT_REGULAR);
        $result = array_values($result);
        /** @var array $result */
        $result = $this->addIncludesToResultArray($result, $className);
        return $this->createArrayResponse(compact('result'), 'data');
    }

    /**
     * @param Builder $qb
     * @return Builder
     */
    protected function modifySearch(Builder $qb): Builder
    {
        return $qb;
    }

    /**
     * @param Builder $query
     */
    protected function addLimit(Builder $query)
    {
        /** @var Request $request */
        $request = $this->request;
        $limit = (int) $request->getQuery('limit');
        if (!$limit || $limit > $this->limit) {
            $query->limit($this->limit);
        }
    }
}
