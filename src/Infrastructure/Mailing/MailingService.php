<?php

declare(strict_types=1);

namespace App\Infrastructure\Mailing;

use App\Application\Settings\SettingsInterface;
use App\Domain\User\User;
use App\Infrastructure\Mailing\Templates\AccountActivatedTemplate;
use App\Infrastructure\Mailing\Templates\AccountBlockedTemplate;
use App\Infrastructure\Mailing\Templates\NewAccountTemplate;
use App\Infrastructure\Mailing\Templates\NewCode;
use App\Infrastructure\Mailing\Templates\NewCodeTemplate;
use App\Infrastructure\Mailing\Templates\Template;
use PHPMailer\PHPMailer\PHPMailer;
use RuntimeException;

class MailingService implements IMailingService
{
    public const NEW_ACCOUNT = 'NEW_ACCOUNT';
    public const ACCOUNT_ACTIVATED = 'ACCOUNT_ACTIVATED';
    public const NEW_CODE_REQUEST = 'NEW_CODE_REQUEST';    
    public const ACCOUNT_BLOCKED = 'ACCOUNT_BLOCKED';

    private PHPMailer $mailer;

    /** Mail reciever */
    private User $reciever;

    /** Template of the email*/
    private Template $template;


    public function __construct(SettingsInterface $settings)
    {
        $smtpSettings = $settings->get('smtp');
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->Mailer = "smtp";

        $mail->setLanguage('pl');
        $mail->CharSet = 'UTF-8';
        $mail->SMTPDebug  = $smtpSettings['debug'];
        $mail->SMTPAuth   = TRUE;
        $mail->SMTPSecure = "tls";
        $mail->Port       = $smtpSettings['port'];
        $mail->Host       = $smtpSettings['host'];
        $mail->Username   = $smtpSettings['username'];
        $mail->Password   = $smtpSettings['password'];

        $mail->IsHTML(true);
        $mail->SetFrom(
            $smtpSettings['username'],
            $smtpSettings['mailerName']
        );

        $this->mailer = $mail;
    }


    /** {@inheritDoc}  */
    public function setReciever(User $user): void
    {
        $this->reciever = $user;
    }


    /** {@inheritDoc}  */
    public function setMessageType(string $type, $context = []): void
    {
        $context['user'] = $this->reciever;
        $templateClass = '';

        switch ($type) {
            case MailingService::NEW_ACCOUNT:
                $templateClass = NewAccountTemplate::class;
                break;

            case MailingService::ACCOUNT_ACTIVATED:
                $templateClass = AccountActivatedTemplate::class;
                break;

            case MailingService::NEW_CODE_REQUEST:
                $templateClass = NewCodeTemplate::class;
                break;

            case MailingService::ACCOUNT_BLOCKED:
                $templateClass = AccountBlockedTemplate::class;
                break;

            default:
                throw new RuntimeException('You have to specify email template');
                break;
        }

        $this->template = new $templateClass($context);
    }


    /** {@inheritDoc}  */
    public function send(): void
    {
        $this->mailer->addAddress($this->reciever->email, $this->reciever->name . ' ' . $this->reciever->surname);
        $this->mailer->Subject = $this->template->getTitle();
        $this->mailer->msgHTML($this->template->render());

        try {
            if (!$this->mailer->send())
                throw new MailingServiceException();
        } catch (\Exception $e) {
            throw new MailingServiceException();
        }
    }
}
