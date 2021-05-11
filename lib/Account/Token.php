<?php

namespace Madlib\Account;

use Madlib\Exception;
use Madlib\Mysql;
use Madlib\Mailer;

class Token
{
    protected Mysql $mysql;
    protected Mailer $mailer;

    public function __construct(Mysql $mysql, Mailer $mailer)
    {
        $this->mysql = $mysql;
        $this->mailer = $mailer;
    }
    
    public function reset(string $email, string $mail): bool
    {
        $token = bin2hex(random_bytes(32));
        return $this->mysql->update("        
            UPDATE user SET token = '$token' WHERE email = '$email';
        ") && $this->mailer->send($mail, ['email' => $email], ['token' => $token]);
    }
}
