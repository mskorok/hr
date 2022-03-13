<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Model\TeacherSchedule;
use League\Fractal\Resource\Item;
use PhalconRest\Transformers\ModelTransformer;

/**
 * Class TeacherScheduleTransformer
 * @package App\Transformers
 */
class TeacherScheduleTransformer extends ModelTransformer
{
    /**
     * Transforms are automatically handled
     * based on your model when you extend ModelTransformer
     * and assign the modelClass property
     */


    public function __construct()
    {
        $this->modelClass = TeacherSchedule::class;

        $this->availableIncludes = [
            'Schedule',
            'Teachers'
        ];
    }

    /**
     * @param TeacherSchedule $model
     * @return Item
     */
    public function includeTeachers(TeacherSchedule $model): Item
    {
        return $this->item($model->getTeachers(), new TeachersTransformer());
    }

    /**
     * @param TeacherSchedule $model
     * @return Item
     */
    public function includeSchedule(TeacherSchedule $model): Item
    {
        return $this->item($model->getSchedule(), new ScheduleTransformer());
    }


}
