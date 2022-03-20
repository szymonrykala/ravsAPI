<?php

/**
 * scrpit is created beacouse of long time without connectiong to SMTP server
 *  may occur with login error while sending emails.
 * This script may be lunched with cron job to prevent this issue
 */

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;


$mail = new PHPMailer();
$mail->IsSMTP();
$mail->Mailer = "smtp";

$mail->setLanguage('pl');
$mail->CharSet = 'UTF-8';
// $mail->SMTPDebug  = getenv('SMTP_DEBUG');
$mail->SMTPDebug  = 1;
$mail->SMTPAuth   = TRUE;
$mail->SMTPSecure = "tls";
$mail->Port       = getenv('SMTP_PORT');
$mail->Host       = getenv('SMTP_HOST');
$mail->Username   = getenv('SMTP_USER');
$mail->Password   = getenv('SMTP_PASSWORD');

$mail->IsHTML(true);
$mail->SetFrom(getenv('SMTP_USER'), 'Ravs System Cron');

$mail->addAddress('szymonrykala@gmail.com');
$mail->Subject = 'Ravs System Cron job';
$mail->msgHTML('<h1>Cron Job</h1>');

$mail->send();

