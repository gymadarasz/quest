<?php

namespace Madlib;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

class Mailer
{
    protected Config $config;
    protected Template $template;
    
    public function __construct(Config $config, Template $template)
    {
        $this->config = $config;
        $this->template = $template;
    }

    public function send(string $mail, array $recipient, array $data): bool
    {
        try {
            $phpmailer = new PHPMailer(true);
            if ($this->config::PHPMAILER['smtp_debug_server']) {
                $phpmailer->SMTPDebug = SMTP::DEBUG_SERVER; // Enable verbose debug output
            }
            switch ($this->config::PHPMAILER['use_mailer']) {
                case 'smtp':
                    $phpmailer->isSMTP();                                                        // Send using SMTP
                    $phpmailer->Host       = $this->config::PHPMAILER['smtp']['host'];       // Set the SMTP server to send through
                    $phpmailer->SMTPAuth   = true;                                               // Enable SMTP authentication
                    $phpmailer->Username   = $this->config::PHPMAILER['smtp']['user'];       // SMTP username
                    $phpmailer->Password   = $this->config::PHPMAILER['smtp']['password'];   // SMTP password
                    $phpmailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;                     // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
                    $phpmailer->Port       = $this->config::PHPMAILER['smtp']['port'];       // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
                    break;
                case 'sendmail':
                    $phpmailer->isSendmail();
                    break;
                case 'mail':
                    $phpmailer->isMail();
                    break;
                default:
                    throw new Exception('Incorrect PHPMailer interface: ' . $this->config::PHPMAILER['use_mailer']);
            }
            if ($this->config::PHPMAILER['use_mailer'] !== 'smtp') {
                $phpmailer->DKIM_domain = $this->config::PHPMAILER['DKIM']['domain'];
                $phpmailer->DKIM_private = $this->config::PHPMAILER['DKIM']['private'];
                $phpmailer->DKIM_selector = $this->config::PHPMAILER['DKIM']['selector'];
                $phpmailer->DKIM_passphrase = $this->config::PHPMAILER['DKIM']['passphrase'];
                $phpmailer->DKIM_identity = $phpmailer->From;
            }
            
            // can be $mail specific?
            $phpmailer->setFrom($this->config::MAILER['sysmail_noreply_email'], $this->config::MAILER['sysmail_noreply_name']);
            $phpmailer->addReplyTo($this->config::MAILER['sysmail_noreply_email'], $this->config::MAILER['sysmail_noreply_name']);

            //$phpmailer->addCC('cc@example.com');
            //$phpmailer->addBCC('bcc@example.com');
        
            // Attachments
            //$phpmailer->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
            //$phpmailer->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

            // Add a recipient
            $phpmailer->addAddress($recipient['email'], $recipient['name'] ?? null);    // Name is optional

            // Content
            $phpmailer->isHTML(true);                                  // Set email format to HTML
            $phpmailer->Subject = $this->config::MAILER['mails'][$mail]['subject'];

            $phpmailer->Body    = $this->template->process($this->config::MAILER['mails'][$mail]['template'], $data);
            $phpmailer->AltBody = $this->config::MAILER['mails'][$mail]['alt_body'] ?? strip_tags($phpmailer->Body);

            if ($this->config::SITE['env'] === $this->config::ENV_LIVE) {
                sleep(rand(2, 5)); // leak emails...
            }
            if ($this->config::MAILER['test_only']) {
                return file_put_contents(
                    $this->config::MAILER['test_mail_folder'] . time() . '.eml.html',
                    "<pre>RECIPIENT: <{$recipient['email']}> " . ($recipient['name'] ?? null) . "\n" .
                    "SUBJECT: $phpmailer->Subject\n".
                    "BODY:\n</pre>" . $phpmailer->Body
                );
            }
            $phpmailer->send();
        } catch (PHPMailerException $e) {
            trigger_error("Message could not be sent. Mailer Error: {$phpmailer->ErrorInfo}");
            return false;
        }
        return true;
    }
}
