<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Traits\Recipients;
use App\Traits\Messenger;
use App\Traits\SearchByRoles;

/**
 * Class MessengerController
 * @package App\Controllers
 */
class MessengerController extends ControllerBase
{
    use Messenger, Recipients, SearchByRoles;

    public static $encodedFields = [
        'title',
        'content'
    ];

    /**
     * @param $id
     * @return mixed
     * @throws \RuntimeException
     */
    public function sendMessagesToEmployer($id)
    {
        if (!$this->isEmployer($id)) {
            return $this->createErrorResponse('Recipient is not Employer');
        }

        $this->sendMessage($id);
        return $this->createOkResponse();
    }

    /**
     * @return mixed
     * @throws \RuntimeException
     */
    public function sendMessagesToEmployers()
    {
        $recipients = $this->_getEmployers();
        $this->sendMessages($recipients);
        return $this->createOkResponse();
    }

    /**
     * @param $id
     * @return mixed
     * @throws \RuntimeException
     */
    public function sendMessagesToApplicant($id)
    {
        if (!$this->isApplicant($id)) {
            return $this->createErrorResponse('Recipient is not Applicant');
        }
        return $this->sendMessage($id);
    }
    /**
     * @param $id
     * @return mixed
     * @throws \RuntimeException
     */
    public function sendMessagesToSupport($id)
    {
        if (!$this->isSupport($id)) {
            return $this->createErrorResponse('Recipient is not Applicant');
        }
        return $this->sendMessage($id);
    }


    /**
     * @return mixed
     * @throws \RuntimeException
     */
    public function sendMessagesToApplicants()
    {
        $recipients = $this->_getApplicant();
        return $this->sendMessages($recipients);
    }
}
