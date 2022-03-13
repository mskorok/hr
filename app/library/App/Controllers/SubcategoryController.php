<?php
declare(strict_types=1);

namespace App\Controllers;
 
use App\Constants\Limits;
use App\Constants\Services;
use App\Forms\SubcategoryForm;
use App\Model\Articles;
use App\Model\Subcategory;
use App\Model\Images;
use App\Traits\RenderView;
use App\Validators\SubcategoryValidator;
use App\Validators\ImagesValidator;
use Exception;
use Phalcon\Http\Request\File;
use Phalcon\Http\Response;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Paginator\Adapter\Model as Paginator;
use Phalcon\Mvc\Model\Query\Builder;
use Phalcon\Paginator\Adapter\QueryBuilder;
use ReflectionException;
use RuntimeException;

/**
 * Class SubcategoryController
 * @package App\Controllers
 */
class SubcategoryController extends ControllerBase
{
    use RenderView;

    public static $availableIncludes = [
        'Images'
    ];

    public static $encodedFields = [
        'text',
        'title',
        'description',
        'link'
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
     * Searches for subcategory
     * @throws ReflectionException
     */
    public function searchAction()
    {
        $numberPage = 1;
        $parameters = [];
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, Subcategory::class, $_POST);
            $parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery('page', 'int');
        }


        $parameters['order'] = 'id';

        /** @var Resultset $subcategory */
        $subcategory = Subcategory::find($parameters);
        if ($subcategory->count() === 0) {
            return $this->response->redirect('/admin/subcategory/index?notice=' . urlencode('The search did not find any subcategory'));
        }

        $paginator = new Paginator([
            'data' => $subcategory,
            'limit'=> Limits::SEARCH_LIMIT,
            'page' => $numberPage
        ]);

        $page = $paginator->getPaginate();

        $this->returnView('search', ['page' => $page, 'limit' => $paginator->getLimit()]);
    }

    /**
     * Searches for subcategory
     * @throws ReflectionException
     */
    public function listAction()
    {
        $numberPage = $this->request->getQuery('page', 'int');

        /** @var Resultset $subcategory */
        $subcategory = Subcategory::find();
        if ($subcategory->count() === 0) {
            return $this->response->redirect('/admin/subcategory/index?notice=' . urlencode('The search did not find any subcategory'));
        }

        $paginator = new Paginator([
            'data' => $subcategory,
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
     * @return ResponseInterface|null
     * @throws ReflectionException
     */
    public function editAction($id): ?ResponseInterface
    {
        if ($this->request->isPost()) {
            return $this->response->redirect('/admin/subcategory/index');
        }
        $subcategory = Subcategory::findFirst((int)$id);
        if (!$subcategory) {
            return $this->response->redirect('/admin/subcategory/index?notice=' . urlencode('subcategory was not found'));
        }

        $this->view->id = $subcategory->getId();

        $this->tag::setDefault('id', $subcategory->getId());
        $this->tag::setDefault('title', $subcategory->getTitle());
        $this->tag::setDefault('description', $subcategory->getDescription());
        $this->tag::setDefault('text', $subcategory->getText());

        $this->returnView('edit');
        return null;
    }

    /**
     * Creates a new subcategory
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            return $this->response->redirect('/admin/subcategory/index');
        }

        $subcategory = new Subcategory();
        $subcategory->setTitle($this->request->getPost('title'));
        $subcategory->setDescription($this->request->getPost('description'));
        $subcategory->setText($this->request->getPost('text'));
        $subcategory->beforeCreate();

        $this->transformModelBeforeSave($subcategory);

        if (!$subcategory->save()) {
            $mes = '';
            foreach ($subcategory->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/subcategory/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/subcategory/index?success=' . urlencode('subcategory was created successfully'));

    }

    /**
     * Saves a subcategory edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            return $this->response->redirect('/admin/subcategory/index');
        }

        $id = $this->request->getPost('id');
        $subcategory = Subcategory::findFirst((int)$id);

        if (!$subcategory) {
            return $this->response->redirect('/admin/subcategory/index?notice=' . urlencode('subcategory does not exist ' . $id));
        }

        $subcategory->setTitle($this->request->getPost('title'));
        $subcategory->setDescription($this->request->getPost('description'));
        $subcategory->setText($this->request->getPost('text'));
        $subcategory->beforeUpdate();
        $this->transformModelBeforeSave($subcategory);

        if (!$subcategory->save()) {
            $mes = '';
            foreach ($subcategory->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/subcategory/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/subcategory/index?success=' . urlencode('subcategory was updated successfully'));
    }

    /**
     * @param $id
     * @return Response
     */
    public function deleteAction($id)
    {
        $subcategory = Subcategory::findFirst((int)$id);
        if (!$subcategory) {
            return $this->response->redirect('/admin/subcategory/index?notice=' . urlencode('subcategory was not found'));
        }

        if (!$subcategory->delete()) {
            $mes = '';
            foreach ($subcategory->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/subcategory/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/subcategory/index?success=' . urlencode('subcategory was deleted successfully'));
    }


    /************************************ SITE *************/



    /**
     * @return mixed
     */
    public function listAllSubcategory()
    {
        $numberPage = $this->request->getQuery('page', 'int');


        $builder = new Builder();
        $builder->addFrom(Subcategory::class);

        $paginator = new QueryBuilder([
            'builder' => $builder,
            'limit'=> Limits::SEARCH_LIMIT,
            'page' => $numberPage
        ]);
        $page = $paginator->getPaginate();

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
            'subcategory'      => $items,
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
            'topInRange'    => $this->topInRange
        ];

        return $this->createArrayResponse($data, 'data');
    }



    /**
     * @return mixed
     */
    public function searchSubcategory()
    {
        $numberPage = $this->request->getQuery('page', 'int', 1);
        $q = $this->request->getQuery('q', null, '');

        if (empty($numberPage)) {
            $numberPage = 0;
        }

        if (empty($q) || strlen($q) < 3) {
            return $this->createErrorResponse('Empty query!');
        }

        $question = $this->sanitize($q);
        $sql = "SELECT id,
                MATCH (title, description, `text`) AGAINST ('{$question}' IN BOOLEAN MODE) as REL
                FROM `subcategory`
                WHERE MATCH (title, description, `text`) AGAINST ('{$question}' IN BOOLEAN MODE)
                ORDER BY REL;";

        $connection = $this->db;
        $res = $connection->query($sql);

        $ids = [];

        do {
            $row = $res->fetchArray();
            if ($row) {
                $ids[] = (int)$row['id'];
            }
        } while($row);



        $builder = new Builder();
        $builder->addFrom(Subcategory::class);
        $builder->inWhere('id', $ids);

        $paginator = new QueryBuilder([
            'builder' => $builder,
            'limit'=> Limits::SEARCH_LIMIT,
            'page' => $numberPage
        ]);
        $page = $paginator->getPaginate();

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
            'subcategory'      => $items,
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
            'topInRange'    => $this->topInRange
        ];

        return $this->createArrayResponse($data, 'data');
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getSubcategoryArticles($id)
    {
        $id = (int) $id;

        $numberPage = $this->request->getQuery('page', 'int', 1);

        if (empty($numberPage)) {
            $numberPage = 0;
        }



        if (empty($id)) {
            $this->flashSession->warning('Short category name');
            return $this->response->redirect('/');
        }

        /** @var Subcategory $subcategory */
        $subcategory = Subcategory::findFirst($id);


        if (!$subcategory) {
            $this->flashSession->warning('Category not found');
            return $this->response->redirect('/');
        }
        $articles = $subcategory->getArticles();

        $subName = $subcategory->getTitle();




        $ids = [];

        /** @var Articles $sub */
        foreach ($articles as $sub) {
            $ids[] = $sub->getId();
        }


        $builder = new Builder();
        $builder->addFrom(Articles::class);
        $builder->inWhere('id', $ids);

        $paginator = new \Phalcon\Paginator\Adapter\QueryBuilder([
            'builder' => $builder,
            'limit'=> Limits::SEARCH_LIMIT,
            'page' => $numberPage
        ]);
        $page = $paginator->getPaginate();

        $test = $paginator->paginate();

//        $items = $page->items->toArray();

        $items = $page->items;

//        throw new Exception('ARTICLES ' . serialize($items));

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
            'articles'   => $items,
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
            'subName'           => $subName,
        ];

        return $this->createArrayResponse($data, 'data');
    }


    /**************************  TEST *********************/

    /**
     *
     * @throws ReflectionException
     */
    public function listSubcategory()
    {
        $this->returnView('list');
    }

    /**
     *
     * @return ResponseInterface|null
     *@throws ReflectionException
     * @throws Exception
     * @throws RuntimeException
     */
    public function add(): ?ResponseInterface
    {
        $subcategory = new Subcategory();
        $messages = [];
        if ($this->request->isPost()) {
            $params = $this->request->getPost();
            $config = $this->getDI()->get(Services::CONFIG);
            $image = null;

            if ($this->request->hasFiles(true)) {
                $uploadDir = $config->application->uploadDir;

                if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755) && !is_dir($uploadDir)) {
                    throw new RuntimeException(sprintf('Directory "%s" was not created', $uploadDir));
                }
                /** @var File $file */
                foreach ($this->request->getUploadedFiles(true) as $file) {
                    if ($file->getKey() === 'fileName') {
                        $fileName = uniqid('Article_image_' . date('Y-m-d') . '_', false);
                        $fileName .= '.' . $file->getExtension();
                        try {
                            $file->moveTo($uploadDir . $fileName);
                            $image = new Images();
                            $image->setFileName($fileName);
                            $image->setPath('/uploads/');
                            $data = $image->toArray();
                            $imageValidator = new ImagesValidator();
                            $res = $imageValidator->validate($data);
                            if ($res->count() === 0) {
                                $image->save();
                            } else {
                                $messages = $imageValidator->getMessages();
                            }
                        } catch (RuntimeException $exception) {
                            $messages['image_not_created'] = $exception->getMessage();
                        }
                    }
                }
            }
            unset($params['fileName']);
            if ($image instanceof Images) {
                $params['avatar'] = $image->getId();
            }


            $validator = new SubcategoryValidator();
            $res = $validator->validate($params);
            if ($res->count() === 0) {
                $this->sanitizePostData($params);
                if ($subcategory->save($params)) {
                    return $this->response->redirect('/admin/subcategory/edit/' . $subcategory->getId());
                }
                $messages = $subcategory->getMessages();
            } else {
                $messages = $validator->getMessages();
            }
        }

        $form = new SubcategoryForm($subcategory);
        $form->renderForm();
        $this->returnView('add', compact('form', 'messages'));
        return null;
    }


    /**
     * @param $id
     * @return mixed
     * @throws RuntimeException
     * @throws ReflectionException
     * @throws Exception
     */
    public function updates($id)
    {
        $id = (int)$id;
        if (empty($id)) {
            return $this->response->redirect('/admin/subcategory/list');
        }
        $subcategory = Subcategory::find($id)[0];
        if (!$subcategory) {
            return $this->response->redirect('/admin/subcategory/list');
        }
        $messages = [];

        if ($this->request->isPost()) {
            $params = $this->request->getPost();

            $config = $this->getDI()->get(Services::CONFIG);
            $image = null;

            if ($this->request->hasFiles(true)) {
                $uploadDir = $config->application->uploadDir;

                if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755) && !is_dir($uploadDir)) {
                    throw new RuntimeException(sprintf('Directory "%s" was not created', $uploadDir));
                }
                /** @var File $file */
                foreach ($this->request->getUploadedFiles(true) as $file) {
                    if ($file->getKey() === 'fileName') {
                        $fileName = uniqid('Subcategory_image_' . date('Y-m-d') . '_', false);
                        $fileName .= '.' . $file->getExtension();
                        try {
                            $file->moveTo($uploadDir . $fileName);
                            $image = new Images();
                            $image->setFileName($fileName);
                            $image->setPath('/uploads/');
                            $data = $image->toArray();
                            $imageValidator = new ImagesValidator();
                            $res = $imageValidator->validate($data);
                            if ($res->count() === 0) {
                                $image->save();
                            } else {
                                $messages = $imageValidator->getMessages();
                            }
                        } catch (RuntimeException $exception) {
                            $messages['image_not_created'] = $exception->getMessage();
                        }
                    }
                }
            }

            unset($params['fileName']);
            if ($image instanceof Images) {
                $params['image_id'] = $image->getId();
            }


            $validator = new SubcategoryValidator();
            $res = $validator->validate($params);
            if ($res->count() === 0) {
                $this->sanitizePostData($params);
                /** @var Subcategory $subcategory */
                $subcategory->save($params);
            } else {
                $messages = $validator->getMessages();
            }

            $subcategory->refresh();
        }

        $form = new SubcategoryForm($subcategory);


        $form->renderForm();


        $this->returnView(
            'updates',
            [
                'form' => $form,
                'messages' => $messages,
                'id' => $subcategory->getId()
            ]
        );
    }

    /*************** PROTECTED   *********************/

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
