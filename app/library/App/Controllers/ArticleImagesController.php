<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Constants\Limits;
use App\Model\ArticleImages;
use App\Traits\RenderView;
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Paginator\Adapter\Model as Paginator;
use Phalcon\Mvc\Model\Query\Builder as QueryBuilder;
use Phalcon\Validation\Message\Group;

/**
 * Class ArticleImagesController
 * @package App\Controllers
 */
class ArticleImagesController extends ControllerBase
{
    use RenderView;

    public static $availableIncludes = [
        'Articles',
        'Images'
    ];

    /**
     * Index action
     * @throws \ReflectionException
     */
    public function indexAction()
    {
        $this->returnView('index');
    }

    /**
     * Searches for article_images
     * @throws \ReflectionException
     */
    public function searchAction()
    {
        $numberPage = 1;
        $parameters = [];
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, ArticleImages::class, $_POST);
            $parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery('page', 'int');
        }


        $parameters['order'] = 'id';

        $article_images = ArticleImages::find($parameters);
        if (\count($article_images) === 0) {
            return $this->response->redirect(
                '/admin/article_images/index?notice=' . urlencode('The search did not find any article_images')
            );
        }

        $paginator = new Paginator([
            'data' => $article_images,
            'limit'=> Limits::SEARCH_LIMIT,
            'page' => $numberPage
        ]);

        $page = $paginator->getPaginate();

        $this->returnView('search', ['page' => $page, 'limit' => $paginator->getLimit()]);
    }

    /**
     * Searches for articles
     * @throws \ReflectionException
     */
    public function listAction()
    {
        $numberPage = $this->request->getQuery('page', 'int');

        /** @var Resultset $articles */
        $articles = ArticleImages::find();
        if ($articles->count() === 0) {
            return $this->response->redirect('/admin/article_images/index?notice=' . urlencode('The search did not find any article_images'));
        }

        $paginator = new Paginator([
            'data' => $articles,
            'limit'=> Limits::SEARCH_LIMIT,
            'page' => $numberPage
        ]);

        $page = $paginator->getPaginate();

        $this->returnView('lists', ['page' => $page, 'limit' => $paginator->getLimit()]);

    }

    /**
     * Displays the creation form
     * @throws \ReflectionException
     */
    public function newAction()
    {
        $this->returnView('new');
    }

    /**
     * @param $id
     * @return null|\Phalcon\Http\Response
     * @throws \ReflectionException
     */
    public function editAction($id)
    {

        if ($this->request->isPost()) {
            return $this->response->redirect('/admin/article_images/index');
        }
        $article_image = ArticleImages::findFirst((int)$id);
        if (!$article_image) {
            return $this->response->redirect('/admin/article_images/index?notice=' . urlencode('article_images was not found'));
        }
        $this->view->id = $article_image->getId();

        $this->tag::setDefault('id', $article_image->getId());
        $this->tag::setDefault('article_id', $article_image->getArticleId());
        $this->tag::setDefault('image_id', $article_image->getImageId());
        $this->returnView('edit');
        return null;
    }

    /**
     * Creates a new article_image
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            return $this->response->redirect('/admin/article_images/index');
        }

        $article_image = new ArticleImages();
        $article_image->setArticleId($this->request->getPost('article_id'));
        $article_image->setImageId($this->request->getPost('image_id'));
        

        if (!$article_image->save()) {
            $mes = '';
            foreach ($article_image->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/article_images/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/article_images/index?success=' . urlencode('article_images was created successfully'));
    }

    /**
     * Saves a article_image edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            return $this->response->redirect('/admin/article_images/index');
        }

        $id = $this->request->getPost('id');
        $article_image = ArticleImages::findFirst((int)$id);

        if (!$article_image) {
            return $this->response->redirect('/admin/article_images/index?notice=' . urlencode('article_images does not exist ' . $id));
        }

        $article_image->setArticleId($this->request->getPost('article_id'));
        $article_image->setImageId($this->request->getPost('image_id'));
        

        if (!$article_image->save()) {
            $mes = '';
            foreach ($article_image->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/article_images/index?notice=' . urlencode($mes));
        }
        return $this->response->redirect('/admin/article_images/index?success=' . urlencode('article_images was updated successfully'));
    }

    /**
     * @param $id
     * @return \Phalcon\Http\Response
     */
    public function deleteAction($id)
    {
        $article_image = ArticleImages::findFirst((int)$id);
        if (!$article_image) {
            return $this->response->redirect('/admin/article_images/index?notice=' . urlencode('article_images was not found'));
        }

        if (!$article_image->delete()) {
            $mes = '';
            foreach ($article_image->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/article_images/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/article_images/index?success=' . urlencode('article_images was deleted successfully'));
    }


    /*************** PROTECTED   *********************/

    /**
     * @param QueryBuilder $query
     */
    protected function modifyAllQuery(QueryBuilder $query): void
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
