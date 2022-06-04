<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Model\Messages;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Phalcon\Mvc\Model\Resultset\Simple;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class MessagesTransformer
 * @package App\Transformers
 */
class MessagesTransformer extends ModelTransformer
{

    public function __construct()
    {
        $this->modelClass = Messages::class;

        $this->availableIncludes = [
            'Receiver', 'Addresser',  'Children', 'ParentMessage'
        ];
    }

    /**
     * @param Messages $model
     * @return Collection|null
     */
    public function includeChildren(Messages $model): ?Collection
    {
        /** @var Simple $collection */
        $collection = $model->getChildren();

        if ($collection->count() > 0) {
            return $this->collection($model->getChildren(), new static());
        }
        return null;
    }

    /**
     * @param Messages $model
     * @return Item|null
     */
    public function includeParentMessage(Messages $model): ?Item
    {
        $parent = $model->getParentMessage();
        if (null !== $parent) {
            return $this->item($parent, new static());
        }
        return null;
    }

    /**
     * @param Messages $model
     * @return Item
     */
    public function includeReceiver(Messages $model): Item
    {
        return $this->item($model->getReceiver(), new UsersTransformer());
    }

    /**
     * @param Messages $model
     * @return Item
     */
    public function includeAddresser(Messages $model): Item
    {
        return $this->item($model->getAddresser(), new UsersTransformer());
    }
}
