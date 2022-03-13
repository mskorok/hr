<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Model\Comments;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class CommentsTransformer
 * @package App\Transformers
 */
class CommentsTransformer extends ModelTransformer
{
    /**
     * Transforms are automatically handled
     * based on your model when you extend ModelTransformer
     * and assign the modelClass property
     */


    public function __construct()
    {
        $this->modelClass = Comments::class;

        $this->availableIncludes = [
            'Articles',
            'Comment',
            'Comments',
            'User'
        ];
    }


    /**
     * @param Comments $model
     * @return Collection
     */
    public function includeArticles(Comments $model): Collection
    {
        return $this->collection($model->getArticles(), new ArticlesTransformer());
    }

    /**
     * @param Comments $model
     * @return Item
     */
    public function includeComment(Comments $model): Item
    {
        return $this->item($model->getComment(), new CommentsTransformer());
    }

    /**
     * @param Comments $model
     * @return Collection
     */
    public function includeComments(Comments $model): Collection
    {
        return $this->collection($model->getComments(), new CommentsTransformer());
    }


    /**
     * @param Comments $model
     * @return Collection
     */
    public function includeUser(Comments $model): Collection
    {
        return $this->collection($model->getUser(), new UsersTransformer());
    }
}
