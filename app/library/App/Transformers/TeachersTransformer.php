<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Model\Teachers;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class TeachersTransformer
 * @package App\Transformers
 */
class TeachersTransformer extends ModelTransformer
{
    /**
     * Transforms are automatically handled
     * based on your model when you extend ModelTransformer
     * and assign the modelClass property
     */


    public function __construct()
    {
        $this->modelClass = Teachers::class;

        $this->availableIncludes = [
            'TeacherSchedule',
            'Schedule',
            'Users'
        ];
    }

    /**
     * @param Teachers $model
     * @return Collection
     */
    public function includeTeacherSchedule(Teachers $model): Collection
    {
        return $this->collection($model->getTeacherSchedule(), new TeacherScheduleTransformer());
    }

    /**
     * @param Teachers $model
     * @return Collection
     */
    public function includeSchedule(Teachers $model): Collection
    {
        return $this->collection($model->getSchedule(), new ScheduleTransformer());
    }

    /**
     * @param Teachers $model
     * @return Item
     */
    public function includeUsers(Teachers $model): Item
    {
        return $this->item($model->getUsers(), new UsersTransformer());
    }
}
