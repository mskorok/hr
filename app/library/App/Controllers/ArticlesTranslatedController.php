<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Model\ArticlesTranslated;
use App\Transformers\ArticlesTranslatedTransformer;
use Phalcon\Mvc\Model\Query\Builder;
use Phalcon\Validation\Message\Group;

/**
 * Class ArticlesTranslatedController
 * @package App\Controllers
 */
class ArticlesTranslatedController extends ControllerBase
{

    public static $availableIncludes = [
        'Article',
        'Language'
    ];

    public static $encodedFields = [
        'text',
        'title',
        'description'
    ];

    /**
     * @param $article
     * @param $lang
     * @return mixed
     */
    public function getTranslated($article, $lang)
    {
        $item = ArticlesTranslated::findFirst([
            'article_id =' .  (int)$article . ' AND language_id = ' . (int) $lang
        ]);
        if (property_exists($item, 'text')) {
            $item->setText(html_entity_decode($item->getText()));
        }

        if (property_exists($item, 'title')) {
            $item->setTitle(html_entity_decode($item->getTitle()));
        }

        if (property_exists($item, 'description')) {
            $item->setDescription(html_entity_decode($item->getDescription()));
        }

        return $this->createItemResponse($item, new ArticlesTranslatedTransformer());
    }


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

    /**
     * @param $item
     * @return mixed
     */
    protected function getFindResponse($item)
    {
        if (property_exists($item, 'text')) {
            $item->text = html_entity_decode($item->text);
        }

        if (property_exists($item, 'title')) {
            $item->title = html_entity_decode($item->title);
        }

        if (property_exists($item, 'description')) {
            $item->description = html_entity_decode($item->description);
        }
        return parent::getFindResponse($item);
    }
}
