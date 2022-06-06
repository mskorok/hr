<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Constants\Message as Status;
use App\Model\Logs;
use App\Traits\Recipients;
use App\Traits\Messenger;
use App\Traits\SearchByRoles;
use RuntimeException;

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
     * @throws RuntimeException
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
     * @throws RuntimeException
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
     * @throws RuntimeException
     */
    public function sendMessagesToApplicant($id)
    {
        if (!$this->isApplicant($id)) {
            return $this->createErrorResponse('Recipient is not Applicant');
        }
        return $this->sendMessage($id);
    }

    /**
     * @return mixed
     */
    public function sendMessagesToSupport()
    {
        $recipient = $this->getAdminUser();
        if (!$recipient) {
            $log = new Logs();
            $log->setLog('Admin User not found app/library/App/Controllers/MessengerController.php:68 | public function sendMessagesToSupport');
            $log->save();
            return $this->createErrorResponse('Something went wrong');
        }
        if (!$this->isSupport($recipient->getId())) {
            $log = new Logs();
            $log->setLog('Admin User is not Support app/library/App/Controllers/MessengerController.php:75 | public function sendMessagesToSupport');
            $log->save();
            return $this->createErrorResponse('Something went wrong');
        }
        return $this->sendMessage($recipient->getId(), Status::SUPPORT_STATUS_OPEN);
    }


    /**
     * @return mixed
     * @throws RuntimeException
     */
    public function sendMessagesToApplicants()
    {
        $recipients = $this->_getApplicant();
        return $this->sendMessages($recipients);
    }
}
