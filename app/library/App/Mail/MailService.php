<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 28.09.17
 * Time: 11:10
 */

namespace App\Mail;

use App\Model\Mail;
use App\Model\Users;
use Phalcon\Mailer\Manager;

/**
 * Class MailService
 * @package App\Mail
 */
class MailService
{
    protected $mailer;

    protected $config;

    protected $di;

    /**
     * MailService constructor.
     * @param Manager $mailer
     * @param $di
     * @param $config
     */
    public function __construct(Manager $mailer, $di, $config)
    {
        $this->mailer = $mailer;
        $this->config = $config;
        $this->di = $di;
    }

    /**
     * @param $name
     * @return Mail|\Phalcon\Mvc\Model\ResultInterface
     */
    public function getData($name)
    {
        return Mail::findFirst([
            'conditions' => 'name = :name:',
            'bind'       => [
                'name' => $name
            ]
        ]);
    }

    /**
     * @param Users $user
     * @param null $config
     */
    public function sendRecoveryLetter(Users $user, $config = null): void
    {
        $config = $config ? : $this->config;
        $email = $user->getEmail();
        $link = $config->frontHostName . '/new_password.html?email='.$email.'&recovery_token='.$user->getToken();

        $message = $this->mailer->createMessageFromView('recovery', ['link' => $link])
            ->to($email, $user->getName().' '.$user->getSurname())
            ->subject('Password recovery');
        $message->send();
    }

    /**
     * @param Users $user
     * @param null $config
     */
    public function sendConfirmationLetter(Users $user, $config = null): void
    {
        $config = $config ? : $this->config;
        $email = $user->getEmail();
        $token = $user->getToken();
        $link = $config->frontHostName . '/confirm?email=' . $email . '&confirm_token=' . $token;

        $message = $this->mailer->createMessageFromView('confirm', ['link' => $link])
            ->to($email, $user->getName().' '.$user->getSurname())
            ->subject('Email confirmation');
        $message->send();
    }

    /**
     * @param Users $user
     * @param $name
     * @param array $data
     */
    public function sendLetter(Users $user, $name, array $data = []): void
    {
        $config = $this->config;
        $data['user'] = $user;
        $file = $config->mail->viewsDir.'/' . $name.'phtml';
        /** @var Mail $template */
        $template = $this->getData($name);
        file_put_contents($file, $template->getText());

        $to = $data['to'] ?? $template->getMailTo();
        $recipient = $data['recipient'] ?? $user->getName().' '.$user->getSurname();

        $message = $this->mailer->createMessageFromView($name, $data)
            ->to($to, $recipient)
            ->subject($template->getSubject());
        if (isset($data['attaches'])) {
            $message->attachment($data['attaches']);
        }
        $message->send();
    }

    /**
     * @param array $data
     */
    public function sendAutoResponderLetter(array $data = []): void
    {
        $message = $this->mailer->createMessage()
            ->to($data['to'], $data['recipient'])
            ->subject($data['subject'])
            ->content($data['content']);
        $message->send();
    }

    /**
     * @return Manager
     */
    public function getMailer(): Manager
    {
        return $this->mailer;
    }

    /**
     * @param Manager $mailer
     */
    public function setMailer($mailer): void
    {
        $this->mailer = $mailer;
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param mixed $config
     */
    public function setConfig($config): void
    {
        $this->config = $config;
    }

    /**
     * @return mixed
     */
    public function getDi()
    {
        return $this->di;
    }

    /**
     * @param mixed $di
     */
    public function setDi($di): void
    {
        $this->di = $di;
    }
}
