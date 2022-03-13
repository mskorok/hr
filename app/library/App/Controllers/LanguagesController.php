<?php
declare(strict_types=1);

namespace App\Controllers;

use Phalcon\Mvc\Model\Query\Builder;
use Phalcon\Validation\Message\Group;

/**
 * Class LanguagesController
 * @package App\Controllers
 */
class LanguagesController extends ControllerBase
{

    public static $availableIncludes = [
        'Articles',
        'ArticlesLinked',
        'ArticlesTranslated',
        'ArticlesLinkedTranslated'
    ];




    /*************** PROTECTED   *********************/

    /**
     * @param Builder $query
     */
    protected function modifyAllQuery(Builder $query): void
    {
        $limit = $this->request->getQuery('limit');
        if (!$limit || $limit > $this->limit) {
            $query->limit($this->limit);
        }
    }

    /**
     *
     */
    protected function beforeHandle()
    {
        $this->messages = new Group();
    }

    /**
     * @param $data
     * @return mixed
     * @throws \RuntimeException
     */
    protected function onDataInvalid($data)
    {
        $mes = [];
        $mes['Post-data is invalid'];
        foreach ($this->messages as $message) {
            $mes[] = $message->getMessage();
        }

        return $this->createErrorResponse($mes);
    }
}
