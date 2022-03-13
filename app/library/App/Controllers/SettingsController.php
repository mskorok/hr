<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Validators\SettingsValidator;
use App\Traits\RenderView;

/**
 * Class SettingsController
 * @package App\Controllers
 */
class SettingsController extends ControllerBase
{
    use RenderView;

    public static $encodedFields = [
        'name',
        'stringData'
    ];

    /**
     * @param $data
     * @param $isUpdate
     * @return bool
     */
    protected function postDataValid($data, $isUpdate): bool
    {
        $validator = new SettingsValidator();
        $res = $validator->validate($data);
        $this->messages = $validator->getMessages();
        return $res->count() === 0;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function transformPostData($data): array
    {
        $data = parent::transformPostData($data);
        $result = [];

        foreach ($data as $key => $value) {
            $result[$key] = $this->transformPostDataValue($key, $value, $data);
            if ($value === '' && \in_array($key, ['stringData', 'integerData', 'boolData'], true)) {
                unset($result[$key]);
            }
        }

        return $result;
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
            throw new \RuntimeException('Only admin has permission to remove Settings');
        }
    }
}
