<?php
declare(strict_types=1);

namespace App\Controllers;
 
use App\Constants\Limits;
use App\Constants\Services;
use App\Forms\ArticlesForm;
use App\Model\Articles;
use App\Model\ArticleTag;
use App\Model\Images;
use App\Model\Tag;
use App\Traits\RenderView;
use App\Validators\ArticlesValidator;
use App\Validators\ImagesValidator;
use Phalcon\Exception\RuntimeException;
use Phalcon\Http\Response;
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Paginator\Adapter\Model as Paginator;
use Phalcon\Mvc\Model\Query\Builder;
use Phalcon\Validation\Message\Group;
use Phalcon\Paginator\Adapter\QueryBuilder;

/**
 * Class ArticlesController
 * @package App\Controllers
 */
class ArticlesController extends ControllerBase
{
    use RenderView;

    public static $availableIncludes = [
//        'ArticleImages',
        'Images'
    ];

    public static $encodedFields = [
        'text',
        'title',
        'description'
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
     * Searches for articles
     * @throws \ReflectionException
     */
    public function searchAction()
    {
        $numberPage = 1;
        $parameters = [];
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, Articles::class, $_POST);
            $parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery('page', 'int');
        }


        $parameters['order'] = 'id';

        /** @var Resultset $articles */
        $articles = Articles::find($parameters);
        if ($articles->count() === 0) {
            return $this->response->redirect('/admin/articles/index?notice=' . urlencode('The search did not find any articles'));
        }

        $paginator = new Paginator([
            'data' => $articles,
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
        $articles = Articles::find();
        if ($articles->count() === 0) {
            return $this->response->redirect('/admin/articles/index?notice=' . urlencode('The search did not find any articles'));
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
     * @return null|Response
     * @throws \ReflectionException
     */
    public function editAction($id): ?Response
    {
        if ($this->request->isPost()) {
            return $this->response->redirect('/admin/articles/index');
        }
        $article = Articles::findFirst((int)$id);
        if (!$article) {
            return $this->response->redirect('/admin/articles/index?notice=' . urlencode('article was not found'));
        }

        $this->view->id = $article->getId();

        $this->tag::setDefault('id', $article->getId());
        $this->tag::setDefault('title', $article->getTitle());
        $this->tag::setDefault('description', $article->getDescription());
        $this->tag::setDefault('text', $article->getText());

        $this->returnView('edit');
        return null;
    }

    /**
     * Creates a new article
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            return $this->response->redirect('/admin/articles/index');
        }

        $article = new Articles();
        $article->setTitle($this->request->getPost('title'));
        $article->setDescription($this->request->getPost('description'));
        $article->setText($this->request->getPost('text'));
        $article->beforeCreate();

        $this->transformModelBeforeSave($article);

        if (!$article->save()) {
            $mes = '';
            foreach ($article->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/articles/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/articles/index?success=' . urlencode('article was created successfully'));

    }

    /**
     * Saves a article edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            return $this->response->redirect('/admin/articles/index');
        }

        $id = $this->request->getPost('id');
        $article = Articles::findFirst((int)$id);

        if (!$article) {
            return $this->response->redirect('/admin/articles/index?notice=' . urlencode('article does not exist ' . $id));
        }

        $article->setTitle($this->request->getPost('title'));
        $article->setDescription($this->request->getPost('description'));
        $article->setText($this->request->getPost('text'));
        $article->beforeUpdate();
        $this->transformModelBeforeSave($article);

        if (!$article->save()) {
            $mes = '';
            foreach ($article->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/articles/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/articles/index?success=' . urlencode('article was updated successfully'));
    }

    /**
     * @param $id
     * @return Response
     */
    public function deleteAction($id)
    {
        $article = Articles::findFirst((int)$id);
        if (!$article) {
            return $this->response->redirect('/admin/articles/index?notice=' . urlencode('article was not found'));
        }

        if (!$article->delete()) {
            $mes = '';
            foreach ($article->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/articles/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/articles/index?success=' . urlencode('article was deleted successfully'));
    }


    /************************************ SITE *************/



    /**
     * @return mixed
     */
    public function listAllArticles()
    {
        $numberPage = $this->request->getQuery('page', 'int');
        $category = $this->request->getQuery('category', 'int');
        $tag = $this->request->getQuery('tag', 'int');


        $builder = new Builder();
        $builder->addFrom(Articles::class);
        if ($category) {
            $builder->where('category_id = ' . $category);
        }

        if ($tag) {
            $builder->leftJoin(
                ArticleTag::class,
                '[' . ArticleTag::class . '].[article_id] = [' . Articles::class . '].[id]'
            );
            $builder->leftJoin(
                Tag::class,
                '[' . ArticleTag::class . '].[tag_id] = [' . Tag::class . '].[id]'
            );
            if ($category) {
                $builder->andWhere('[' . Tag::class . '].[id] = :tag_id:', ['tag_id' => $tag]);
            } else {
                $builder->where('[' . Tag::class . '].[id] = :tag_id:', ['tag_id' => $tag]);
            }
        }

        $paginator = new QueryBuilder([
            'builder' => $builder,
            'limit'=> Limits::SEARCH_LIMIT,
            'page' => $numberPage
        ]);
        $page = $paginator->getPaginate();

//        $items = $page->items->toArray();

        $items = $page->items;

        $collection = [];

        /** @var Articles $item */
        foreach ($items as $item) {
            $item->setTitle(html_entity_decode($item->getTitle()));
            $item->setDescription(html_entity_decode($item->getDescription()));
            $item->setText(html_entity_decode($item->getText()));
            $collection[] = $item;
        }


        $pagesInRange = $this->getPaginationRange($page);

        $items = $this->getComplexArray($collection);

        $data = [
            'articles'      => $items,
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
    public function searchArticle()
    {
        $numberPage = $this->request->getQuery('page', 'int', 1);
        $category = $this->request->getQuery('category', 'int');
        $tag = $this->request->getQuery('tag', 'int');
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
                FROM `articles`
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
        $builder->addFrom(Articles::class);
        $builder->inWhere('id', $ids);

        if (!empty($category)) {
            $builder->andWhere('category_id = ' . $category);
        }

        if (!empty($tag)) {
            $builder->leftJoin(
                ArticleTag::class,
                '[' . ArticleTag::class . '].[article_id] = [' . Articles::class . '].[id]'
            );
            $builder->leftJoin(
                Tag::class,
                '[' . ArticleTag::class . '].[tag_id] = [' . Tag::class . '].[id]'
            );
            $builder->andWhere('[' . Tag::class . '].[id] = :tag_id:', ['tag_id' => $tag]);
        }

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
            'articles'      => $items,
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


    /**************************  TEST *********************/

    /**
     *
     * @throws \ReflectionException
     */
    public function listArticles()
    {
        $this->returnView('list');
    }

    /**
     *
     * @throws \RuntimeException
     * @throws \ReflectionException
     * @throws \Exception
     * @return Response | null
     */
    public function add(): ?Response
    {
        $article = new Articles();
        $messages = [];
        if ($this->request->isPost()) {
            $params = $this->request->getPost();
            $config = $this->getDI()->get(Services::CONFIG);
            $image = null;

            if ($this->request->hasFiles(true)) {
                $uploadDir = $config->application->uploadDir;

                if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755) && !is_dir($uploadDir)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $uploadDir));
                }
                /** @var \Phalcon\Http\Request\File $file */
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
                        } catch (\RuntimeException $exception) {
                            $messages['image_not_created'] = $exception->getMessage();
                        }
                    }
                }
            }
            unset($params['fileName']);
            if ($image instanceof Images) {
                $params['avatar'] = $image->getId();
            }


            $validator = new ArticlesValidator();
            $res = $validator->validate($params);
            if ($res->count() === 0) {
                $this->sanitizePostData($params);
                if ($article->save($params)) {
                    return $this->response->redirect('/admin/articles/edit/' . $article->getId());
                }
                $messages = $article->getMessages();
            } else {
                $messages = $validator->getMessages();
            }
        }

        $form = new ArticlesForm($article);
        $form->renderForm();
        $this->returnView('add', compact('form', 'messages'));
        return null;
    }


    /**
     * @param $id
     * @return \Phalcon\Http\Response | null
     * @throws \RuntimeException
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function updates($id): ?Response
    {
        $id = (int)$id;
        if (empty($id)) {
            return $this->response->redirect('/admin/articles/list');
        }
        $article = Articles::find($id)[0];
        if (!$article) {
            return $this->response->redirect('/admin/articles/list');
        }
        $messages = [];

        if ($this->request->isPost()) {
            $params = $this->request->getPost();

            $config = $this->getDI()->get(Services::CONFIG);
            $image = null;

            if ($this->request->hasFiles(true)) {
                $uploadDir = $config->application->uploadDir;

                if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755) && !is_dir($uploadDir)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $uploadDir));
                }
                /** @var \Phalcon\Http\Request\File $file */
                foreach ($this->request->getUploadedFiles(true) as $file) {
                    if ($file->getKey() === 'fileName') {
                        $fileName = uniqid('Articles_image_' . date('Y-m-d') . '_', false);
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
                        } catch (\RuntimeException $exception) {
                            $messages['image_not_created'] = $exception->getMessage();
                        }
                    }
                }
            }

            unset($params['fileName']);
            if ($image instanceof Images) {
                $params['image_id'] = $image->getId();
            }


            $validator = new ArticlesValidator();
            $res = $validator->validate($params);
            if ($res->count() === 0) {
                $this->sanitizePostData($params);
                /** @var Articles $article */
                $article->save($params);
            } else {
                $messages = $validator->getMessages();
            }

            $article->refresh();
        }

        $form = new ArticlesForm($article);


        $form->renderForm();


        $this->returnView(
            'updates',
            [
                'form' => $form,
                'messages' => $messages,
                'id' => $article->getId()
            ]
        );
        return null;
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
