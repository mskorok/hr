<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Validators\MessengerCategoryValidator;
use App\Traits\RenderView;

/**
 * Class MessengerCategoryController
 * @package App\Controllers
 */
class MessengerCategoryController extends ControllerBase
{
    use RenderView;

    public static $availableIncludes = [
        'Messages'
    ];

    /**
     * @param $data
     * @param $isUpdate
     * @return bool
     */
    protected function postDataValid($data, $isUpdate): bool
    {
        $validator = new MessengerCategoryValidator();
        $res = $validator->validate($data);
        $this->messages = $validator->getMessages();
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
            throw new \RuntimeException('Only admin has permission to remove Messenger Category');
        }
    }
}
