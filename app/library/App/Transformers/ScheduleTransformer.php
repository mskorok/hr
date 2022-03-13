<?php
declare(strict_types=1);

namespace App\Transformers;


use App\Model\Schedule;
use League\Fractal\Resource\Item;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class ScheduleTransformer
 * @package App\Transformers
 */
class ScheduleTransformer extends ModelTransformer
{
    /**
     * Transforms are automatically handled
     * based on your model when you extend ModelTransformer
     * and assign the modelClass property
     */


    public function __construct()
    {
        $this->modelClass = Schedule::class;

        $this->availableIncludes = [
            'TeacherSchedule',
            'Teachers'
        ];
    }

    /**
     * @param Schedule $model
     * @return Item
     */
    public function includeTeacherSchedule(Schedule $model): Item
    {
        return $this->item($model->getTeacherSchedule(), new TeacherScheduleTransformer());
    }

    /**
     * @param Schedule $model
     * @return Item
     */
    public function includeTeachers(Schedule $model): Item
    {
        return $this->item($model->getTeachers(), new TeachersTransformer());
    }


}
