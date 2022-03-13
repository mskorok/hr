<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Constants\Limits;
use App\Forms\CommentsForm;
use App\Model\Comments;
use App\Traits\RenderView;
use App\Validators\CommentsValidator;
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Http\Response;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Paginator\Adapter\Model as Paginator;
use Phalcon\Mvc\Model\Query\Builder as QueryBuilder;
use Phalcon\Validation\Message\Group;
use PhalconApi\Constants\PostedDataMethods;

/**
 * Class CommentsController
 * @package App\Controllers
 */
class CommentsController extends ControllerBase
{

    use RenderView;

    public static $availableIncludes = [
        'Articles',
        'Comment',
        'Comments',
        'User'
    ];

    public static $encodedFields = [
        'text',
        'title'
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
     * Searches for comments
     * @throws \ReflectionException
     */
    public function searchAction()
    {
        $numberPage = 1;
        $parameters = [];
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, Comments::class, $_POST);
            $parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery('page', 'int');
        }

        $parameters['order'] = 'id';

        $comments = Comments::find($parameters);
        if (\count($comments) === 0) {
            return $this->response->redirect('/admin/comments/index?notice=' . urlencode('The search did not find any comments'));
        }

        /** @var Comments $comment */
        foreach ($comments as $comment) {
            $this->afterFind($comment);

        }

        $paginator = new Paginator([
            'data' => $comments,
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

        /** @var Resultset $comments */
        $comments = Comments::find();
        if ($comments->count() === 0) {
            return $this->response->redirect('/admin/comments/index?notice=' . urlencode('The search did not find any comment'));
        }

        /** @var Comments $comment */
        foreach ($comments as $comment) {
            $this->afterFind($comment);

        }

        $paginator = new Paginator([
            'data' => $comments,
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
    public function editAction($id)
    {
        if ($this->request->isPost()) {
            return $this->response->redirect('/admin/comments/index');
        }
        $comments = Comments::findFirst((int)$id);
        if (!$comments) {
            return $this->response->redirect('/admin/comments/index?notice=' . urlencode('company was not found'));
        }

        $this->view->id = $comments->getId();

        $this->tag::setDefault('id', $comments->getId());
        $this->tag::setDefault('title', $comments->getTitle());
        $this->tag::setDefault('text', $comments->getText());
        $this->tag::setDefault('article_id', $comments->getArticleId());
        $this->tag::setDefault('user_id', $comments->getUserId());
        $this->tag::setDefault('parent_id', $comments->getParentId());
        $this->returnView('edit');
        return null;
    }

    /**
     * Creates a new comments
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            return $this->response->redirect('/admin/comments/index');
        }

        $comments = new Comments();
        $comments->setTitle($this->request->getPost('title'));
        $comments->setText($this->request->getPost('text'));
        $comments->setUserId($this->request->getPost('user_id'));
        $comments->setArticleId($this->request->getPost('article_id'));
        $comments->setParentId($this->request->getPost('parent_id'));

        $this->transformModelBeforeSave($comments);

        if (!$comments->save()) {
            $mes = '';
            foreach ($comments->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/comments/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/comments/index?success=' . urlencode('comments was created successfully'));
    }

    /**
     * Saves a comments edited
     *
     */
    public function saveAction()
    {
        if (!$this->request->isPost()) {
            return $this->response->redirect('/admin/comments/index');
        }

        $id = $this->request->getPost('id');
        $comments = Comments::findFirst((int)$id);

        if (!$comments) {
            return $this->response->redirect('/admin/comments/index?notice=' . urlencode('comments does not exist ' . $id));
        }

        $comments->setTitle($this->request->getPost('title'));
        $comments->setText($this->request->getPost('text'));
        $comments->setUserId($this->request->getPost('user_id'));
        $comments->setArticleId($this->request->getPost('article_id'));
        $comments->setParentId($this->request->getPost('parent_id'));

        $this->transformModelBeforeSave($comments);

        if (!$comments->save()) {
            $mes = '';
            foreach ($comments->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/comments/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/comments/index?success=' . urlencode('comments was updated successfully'));
    }

    /**
     * @param $id
     * @return Response
     */
    public function deleteAction($id)
    {
        $comments = Comments::findFirst((int)$id);
        if (!$comments) {
            return $this->response->redirect('/admin/comments/index?notice=' . urlencode('comments was not found'));
        }

        if (!$comments->delete()) {
            $mes = '';
            foreach ($comments->getMessages() as $message) {
                $mes .= $message;
            }

            return $this->response->redirect('/admin/comments/index?notice=' . urlencode($mes));
        }

        return $this->response->redirect('/admin/comments/index?success=' . urlencode('comments was deleted successfully'));
    }


    /**
     *
     * @throws \ReflectionException
     */
    public function listComments()
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
        $comments = new Comments();
        $messages = [];
        if ($this->request->isPost()) {
            $comments = new Comments();
            $params = $this->request->getPost();

            $validator = new CommentsValidator();
            $res = $validator->validate($params);
            if ($res->count() === 0) {
                $this->sanitizePostData($params);
                if ($comments->save($params)) {
                    return $this->response->redirect('/admin/comments/edit/' . $comments->getId());
                }
                $messages = $comments->getMessages();
            } else {
                $messages = $validator->getMessages();
            }
        }
        $form = new CommentsForm($comments);
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
            return $this->response->redirect('/admin/comments/list');
        }
        $comments = Comments::find($id)[0];
        if (!$comments) {
            return $this->response->redirect('/admin/comments/list');
        }

        $this->afterFind($comments);


        $messages = [];

        if ($this->request->isPost()) {
            $params = $this->request->getPost();
            $image = null;

            $validator = new CommentsValidator();
            $res = $validator->validate($params);
            if ($res->count() === 0) {
                $this->sanitizePostData($params);
                /** @var Comments $comments */
                $comments->save($params);
            } else {
                $messages = $validator->getMessages();
            }

            $comments->refresh();
        }

        $form = new CommentsForm($comments);


        $form->renderForm();

        $this->returnView(
            'updates',
            [
                'form' => $form,
                'messages' => $messages,
                'id' => $comments->getId()
            ]
        );
        return null;
    }


    /********    PROTECTED    *******/

    /**
     * @param $data
     * @return array
     * @throws \RuntimeException
     */
    protected function transformPostData($data)
    {
        if (!isset($data['id'])) {
            $data['id'] = '';
        }

        return parent::transformPostData($data);
    }


    /**
     *
     */
    protected function beforeHandleCreate()
    {
        $resource = $this->getResource();
        $resource->postedDataMethod(PostedDataMethods::POST);
    }

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

    /**
     * @param $data
     * @param $isUpdate
     * @return bool
     */
    protected function postDataValid($data, $isUpdate): bool
    {
        $params = $data;
        if (isset($params['fileName'])) {
            unset($params['fileName']);
        }
        $validator = new CommentsValidator();
        $res = $validator->validate($params);
        $this->messages = $validator->getMessages();
        if ($res->count() !== 0) {
            return false;
        }
        return $res->count() === 0;
    }

    /**
     * @param $id
     * @throws \RuntimeException
     * @throws \PhalconApi\Exception
     */
    protected function beforeHandleRemove($id)
    {
        $admin = $this->isAdminUser();
        if (!$admin) {
            throw new \RuntimeException('Only admin has permission to remove User');
        }
    }
}
