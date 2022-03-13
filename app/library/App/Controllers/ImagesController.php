<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Constants\Limits;
use App\Traits\RenderView;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Paginator\Adapter\Model as Paginator;
use App\Model\Images;
use Phalcon\Mvc\Model\Query\Builder as QueryBuilder;
use Phalcon\Validation\Message\Group;
use ReflectionException;

/**
 * Class ImagesController
 */
class ImagesController extends ControllerBase
{
    use RenderView;

    public static $availableIncludes = [
        'ArticleImages',
        'Users',
        'Articles',
        'Companies'
    ];

    /**
     * Index action
     * @throws ReflectionException
     */
    public function indexAction()
    {
        $this->returnView('index');
    }

    /**
     * Searches for images
     * @throws ReflectionException
     */
    public function searchAction()
    {
        $numberPage = 1;
        $parameters = [];
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, Images::class, $_POST);
            $parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery('page', 'int');
        }

        $parameters['order'] = 'id';

        $images = Images::find($parameters);
        if (\count($images) === 0) {
            return $this->response->redirect('/admin/images/index?notice=' . urlencode('The search did not find any images'));
        }

        $paginator = new Paginator([
            'data' => $images,
            'limit'=> Limits::SEARCH_LIMIT,
            'page' => $numberPage
        ]);

        $page = $paginator->getPaginate();

        $this->returnView('search', ['page' => $page, 'limit' => $paginator->getLimit()]);
    }

    /**
     * Searches for images
     * @throws ReflectionException
     */
    public function listAction()
    {
        $numberPage = $this->request->getQuery('page', 'int');

        /** @var Resultset $images */
        $images = Images::find();
        if ($images->count() === 0) {
            return $this->response->redirect('/admin/images/index?notice=' . urlencode('The search did not find any images'));
        }

        $paginator = new Paginator([
            'data' => $images,
            'limit'=> Limits::SEARCH_LIMIT,
            'page' => $numberPage
        ]);

        $page = $paginator->getPaginate();

        $this->returnView('lists', ['page' => $page, 'limit' => $paginator->getLimit()]);

    }

    /**
     * Displays the creation form
     * @throws ReflectionException
     */
    public function newAction()
    {
        $this->returnView('new');
    }

    /**
     * @param $id
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function editAction($id)
    {
        if ($this->request->isPost()) {
            return $this->response->redirect('/admin/images/index');
        }


        $image = Images::findFirst((int)$id);
        if (!$image) {
            return $this->response->redirect('/admin/images/index?notice=' . urlencode('image was not found'));
        }

        $this->view->id = $image->getId();

        $this->tag::setDefault('id', $image->getId());
        $this->tag::setDefault('path', $image->getPath());
        $this->tag::setDefault('fileName', $image->getFileName());

        $this->returnView('edit');
        return null;
    }

    /**
     * Creates a new image
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            return $this->response->redirect('/admin/images/index');
        }

        $image = new Images();
        $image->setPath($this->request->getPost('path'));
        $image->setFileName($this->request->getPost('fileName'));
        $image->beforeCreate();
        

        if (!$image->save()) {
            $mes = '';
            foreach ($image->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/images/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/images/index?success=' . urlencode('image was created successfully'));
    }

    /**
     * Saves a image edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            return $this->response->redirect('/admin/images/index');
        }

        $id = $this->request->getPost('id');
        $image = Images::findFirst((int)$id);

        if (!$image) {
            return $this->response->redirect('/admin/images/index?notice=' . urlencode('image does not exist ' . $id));
        }

        $image->setPath($this->request->getPost('path'));
        $image->setFileName($this->request->getPost('fileName'));
        $image->beforeUpdate();
        

        if (!$image->save()) {
            $mes = '';
            foreach ($image->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/images/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/images/index?success=' . urlencode('image was updated successfully'));
    }

    /**
     * @param $id
     * @return ResponseInterface
     */
    public function deleteAction($id): ResponseInterface
    {
        $image = Images::findFirst((int)$id);
        if (!$image) {
            return $this->response->redirect('/admin/images/index?notice=' . urlencode('image was not found'));
        }

        if (!$image->delete()) {
            $mes = '';
            foreach ($image->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/images/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/images/index?success=' . urlencode('image was deleted successfully'));
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
        $mes['Post-data is invalid'] = 'Post-data is invalid';
        foreach ($this->messages as $message) {
            $mes[] = $message->getMessage();
        }

        return $this->createErrorResponse($mes);
    }
}
