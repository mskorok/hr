<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 18.10.17
 * Time: 20:53
 */

namespace App\Data;

use App\Constants\Services;
use Phalcon\Di;
use Phalcon\Mvc\Model\Query\Builder;
use PhalconApi\Data\Query;
use PhalconApi\Data\Query\Condition;
use PhalconRest\QueryParsers\PhqlQueryParser;

/**
 * Class AppParser
 * @package App\Data
 */
class AppParser extends PhqlQueryParser
{
    /**
     * @param Builder $builder
     * @param Query $query
     * @param array $data
     *
     */
    public function addRelatedModelToBuilder(Builder $builder, Query $query, array $data)
    {
        $baseClass = $builder->getFrom();
        $baseClass = \is_array($baseClass) ? array_keys($baseClass)[0] : $baseClass;
        $class = $data['model'];
        $conditions = $query->getConditions();
        $modelConditions =[];
        foreach ($conditions as $condition) {
            if ($condition instanceof Condition) {
                if ($this->hasField($condition->getField(), $data)) {
                    $modelConditions[] = $condition;
                }
            }
        }
        $excludes = $query->getExcludes();
        $modelExcludes = [];
        foreach ($excludes as $exclude) {
            if ($this->hasField($exclude, $data)) {
                $modelExcludes[] = $exclude;
            }
        }

        if (\count($modelConditions) > 0 || \count($modelExcludes) > 0) {
            if ($data['through'] !== null) {
                [$baseField, $throughField1] = $data['relation'][0];
                [$throughField2, $modelField] = $data['relation'][1];
                $throughClass = $data['through'];
                $conditionString1 = '['.$baseClass.'].['.$baseField.'] = ['
                    .$data['throughAlias'].'].['.$throughField1.']';
                $conditionString2 = '['.$data['throughAlias'].'].['
                    .$throughField2.'] = ['.$data['alias'].'].['.$modelField.']';
                $builder->leftJoin($throughClass, $conditionString1, $data['throughAlias']);
                $builder->leftJoin($class, $conditionString2, $data['alias']);
            } else {
                [$baseField, $modelField] = $data['relation'];
                $conditionString = '['.$baseClass.'].['.$baseField.'] = ['.$data['alias'].'].['.$modelField.']';
                $builder->leftJoin($class, $conditionString, $data['alias']);
            }
            /** @var array $modelFields */
            $modelFields = $data['fields'];
            $columnsString = '';
            foreach ($modelFields as $modelField) {
                $columnsString .= '['.$data['alias'].'].'.$modelField.' AS '.$data['alias'].'_'.$modelField.',';
            }
            $columnsString = rtrim($columnsString, ',');

            $oldColumns = $builder->getColumns();
            $columnsString = $oldColumns.', '.$columnsString;
            $builder->columns($columnsString);


            $andConditions = [];
            $orConditions = [];

            /** @var Condition $condition */
            foreach ($modelConditions as $conditionIndex => $condition) {
                if ((int) $condition->getType() === Condition::TYPE_AND) {
                    $andConditions[] = $condition;
                } elseif ((int) $condition->getType() === Condition::TYPE_OR) {
                    $orConditions[] = $condition;
                }
            }

            $allConditions = $orConditions + $andConditions;

            /** @var Condition $condition */
            foreach ($allConditions as $conditionIndex => $condition) {
                $operator = $this->getOperator($condition->getOperator());

                if (!$operator) {
                    continue;
                }

                $parsedValues = $this->parseValues($operator, $condition->getValue());

                $format = $this->getConditionFormat($operator);
                $valuesReplacementString = $this
                    ->getValuesReplacementString($parsedValues, $data['alias'].$conditionIndex);
                $conditionField = $this->sanitizedField($condition->getField(), $data);
                $fieldString = sprintf('[%s].[%s]', $data['alias'], $conditionField);

                $conditionString = sprintf($format, $fieldString, $operator, $valuesReplacementString);

                $bindValues = $this->getBindValues($parsedValues, $data['alias'].$conditionIndex);

                switch ($condition->getType()) {
                    case Condition::TYPE_OR:
                        $builder->orWhere($conditionString, $bindValues);
                        break;
                    case Condition::TYPE_AND:
                    default:
                        $builder->andWhere($conditionString, $bindValues);
                        break;
                }
            }
        }
    }

    /**
     * @param $class
     * @return mixed
     */
    protected function getPrimaryKey($class)
    {
        $modelsMetaData = Di::getDefault()->get(Services::MODELS_METADATA);

        return $modelsMetaData->getIdentityField(new $class);
    }

    /**
     * @param $operator
     * @return mixed|null
     */
    protected function getOperator($operator)
    {
        $operatorMap = $this->operatorMap();

        if (array_key_exists($operator, $operatorMap)) {
            return $operatorMap[$operator];
        }

        return null;
    }

    /**
     * @return array
     */
    protected function operatorMap(): array
    {
        return [
            Query::OPERATOR_IS_EQUAL => static::OPERATOR_IS_EQUAL,
            Query::OPERATOR_IS_GREATER_THAN => static::OPERATOR_IS_GREATER_THAN,
            Query::OPERATOR_IS_GREATER_THAN_OR_EQUAL => static::OPERATOR_IS_GREATER_THAN_OR_EQUAL,
            Query::OPERATOR_IS_IN => static::OPERATOR_IS_IN,
            Query::OPERATOR_IS_LESS_THAN => static::OPERATOR_IS_LESS_THAN,
            Query::OPERATOR_IS_LESS_THAN_OR_EQUAL => static::OPERATOR_IS_LESS_THAN_OR_EQUAL,
            Query::OPERATOR_IS_LIKE => static::OPERATOR_IS_LIKE,
            Query::OPERATOR_IS_JSON_CONTAINS => static::OPERATOR_IS_JSON_CONTAINS,
            Query::OPERATOR_IS_NOT_EQUAL => static::OPERATOR_IS_NOT_EQUAL,
        ];
    }

    /**
     * @param $operator
     * @param $values
     * @return array
     */
    protected function parseValues($operator, $values): array
    {
        $self = $this;

        if (\is_array($values)) {
            return array_map(function ($value) use ($self, $operator) {
                return $self->parseValue($operator, $value);
            }, $values);
        }

        return $this->parseValue($operator, $values);
    }

    /**
     * @param $operator
     * @param $value
     * @return mixed
     */
    protected function parseValue($operator, $value)
    {
        return $value;
    }

    /**
     * @param $operator
     * @return null|string
     */
    protected function getConditionFormat($operator): ?string
    {
        $format = null;

        switch ($operator) {
            case self::OPERATOR_IS_IN:
                $format = '%s %s (%s)';
                break;
            case self::OPERATOR_IS_JSON_CONTAINS:
                $format = '%1$s %2$s (%1$s, %3$s)';
                break;
            default:
                $format = '%s %s %s';
                break;
        }

        return $format;
    }

    /**
     * @param $values
     * @param string $suffix
     * @return string
     */
    protected function getValuesReplacementString($values, $suffix = ''): string
    {
        $key = self::DEFAULT_KEY . $suffix;

        if (\is_array($values)) {
            $formatted = [];
            $valuesCount = \count($values);
            for ($valueIndex = 0; $valueIndex < $valuesCount; $valueIndex++) {
                $formatted[] = ':' . $key . '_' . $valueIndex . ':';
            }

            return implode(', ', $formatted);
        }

        return ':' . $key . ':';
    }

    /**
     * @param $values
     * @param string $suffix
     * @return array
     */
    protected function getBindValues($values, $suffix = ''): array
    {
        $key = self::DEFAULT_KEY . $suffix;

        if (\is_array($values)) {
            $valueIndex = 0;
            $parsed = [];

            foreach ($values as $value) {
                $parsed[$key . '_' . $valueIndex] = $value;
                $valueIndex++;
            }

            return $parsed;
        }

        return [$key => $values];
    }

    /**
     * @param $sanitizedField
     * @param $data
     * @return bool
     */
    protected function hasField($sanitizedField, $data): bool
    {
        $alias = $data['alias'].'.';
        $aliasLow = strtolower($alias);
        /** @var array $fields */
        $fields = $data['fields'];
        if (strpos($sanitizedField, $alias) !== false) {
            $sanitizedField = str_replace($alias, '', $sanitizedField);
        } elseif (strpos($sanitizedField, $aliasLow) !== false) {
            $sanitizedField = str_replace($aliasLow, '', $sanitizedField);
        }

        return \in_array($sanitizedField, $fields, true);
    }

    /**
     * @param $sanitizedField
     * @param $data
     * @return mixed|null
     */
    protected function sanitizedField($sanitizedField, $data)
    {
        $alias = $data['alias'].'.';
        $aliasLow = strtolower($alias);
        /** @var array $fields */
        $fields = $data['fields'];
        if (strpos($sanitizedField, $alias) !== false) {
            $sanitizedField = str_replace($alias, '', $sanitizedField);
        } elseif (strpos($sanitizedField, $aliasLow) !== false) {
            $sanitizedField = str_replace($aliasLow, '', $sanitizedField);
        }

        return \in_array($sanitizedField, $fields, true) ? $sanitizedField : null;
    }
}
