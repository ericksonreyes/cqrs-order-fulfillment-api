<?php

namespace App\Services;

use EricksonReyes\DomainDrivenDesign\Common\Mailer\EmailAddressInterface;
use EricksonReyes\DomainDrivenDesign\Common\Mailer\EmailInterface;
use EricksonReyes\DomainDrivenDesign\Common\Mailer\EmailTransport;
use Swift_Attachment;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

class SwiftSmtpEmailTransport implements EmailTransport
{
    /**
     * @var Swift_SmtpTransport
     */
    private $transport;

    public function __construct($host = 'localhost', $port = 25, $encryption = null)
    {
        $this->transport = new Swift_SmtpTransport($host, $port, $encryption);
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->transport->setUsername($username);
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->transport->setPassword($password);
    }


    /**
     * @param EmailInterface $email
     * @return bool
     */
    public function send(EmailInterface $email): bool
    {
        $mailer = new Swift_Mailer($this->transport);

        $message = $this->makeEmailMessage($email);
        $this->addSender($email, $message);
        $this->addReplyTo($email, $message);
        $this->addRecipients($email, $message);
        $this->addCcRecipients($email, $message);
        $this->addBccRecipients($email, $message);
        $this->addAttachments($email, $message);

        return $mailer->send($message);
    }

    /**
     * @param EmailInterface $email
     * @param $message
     */
    private function addBccRecipients(EmailInterface $email, Swift_Message $message): void
    {
        foreach ($email->bcc() as $bccRecipient) {
            $message->addBcc(
                $bccRecipient->emailAddress(),
                $bccRecipient->name() !== '' ? $bccRecipient->name() : null
            );
        }
    }

    /**
     * @param EmailInterface $email
     * @param $message
     */
    private function addCcRecipients(EmailInterface $email, Swift_Message $message): void
    {
        foreach ($email->cc() as $ccRecipient) {
            $message->addCc(
                $ccRecipient->emailAddress(),
                $ccRecipient->name() !== '' ? $ccRecipient->name() : null
            );
        }
    }

    /**
     * @param EmailInterface $email
     * @param $message
     */
    private function addRecipients(EmailInterface $email, Swift_Message $message): void
    {
        foreach ($email->recipients() as $recipient) {
            $message->setTo(
                $recipient->emailAddress(),
                $recipient->name() !== '' ? $recipient->name() : null
            );
        }
    }

    /**
     * @param EmailInterface $email
     * @param $message
     */
    private function addReplyTo(EmailInterface $email, Swift_Message $message): void
    {
        if ($email->replyTo() instanceof EmailAddressInterface) {
            $message->addReplyTo(
                $email->replyTo()->emailAddress(),
                $email->replyTo()->name() !== '' ? $email->replyTo()->name() : null
            );
        }
    }

    /**
     * @param EmailInterface $email
     * @param $message
     */
    private function addSender(EmailInterface $email, Swift_Message $message): void
    {
        $message->setFrom(
            $email->sender()->emailAddress(),
            $email->sender()->name() !== '' ? $email->sender()->name() : null
        );
    }

    /**
     * @param EmailInterface $email
     * @param $message
     */
    private function addAttachments(EmailInterface $email, Swift_Message $message): void
    {
        foreach ($email->attachments() as $attachment) {
            $dirSeparator = DIRECTORY_SEPARATOR;
            $path = rtrim($attachment->filePath(), $dirSeparator) . $dirSeparator . trim($attachment->fileName());
            $message->attach(Swift_Attachment::fromPath($path, $attachment));
        }
    }

    /**
     * @param EmailInterface $email
     * @return Swift_Message
     */
    private function makeEmailMessage(EmailInterface $email): Swift_Message
    {
        $message = new Swift_Message(
            $email->subject(),
            $email->body()->content(),
            $email->body()->type()
        );

        return $message;
    }
}
