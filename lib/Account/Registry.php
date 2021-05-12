<?php

namespace Madlib\Account;

use Madlib\Exception;
use Madlib\Page;
use Madlib\Input;
use Madlib\Message;
use Madlib\Config;
use Madlib\Mysql;
use Madlib\Redirect;
use Madlib\Validator;
use Madlib\Mailer;
use Madlib\Session;
use Madlib\Code;

class Registry
{
    protected Page $page;
    protected Input $input;
    protected Message $message;
    protected Validator $validator;
    protected Mysql $mysql;
    protected Mailer $mailer;
    protected Redirect $redirect;
    protected Session $session;
    protected Code $code;

    public function __construct(
        Page $page,
        Input $input,
        Message $message,
        Validator $validator,
        Mysql $mysql,
        Mailer $mailer,
        Redirect $redirect,
        Session $session,
        Code $code
    ) {
        $this->page = $page;
        $this->input = $input;
        $this->message = $message;
        $this->validator = $validator;
        $this->mysql = $mysql;
        $this->mailer = $mailer;
        $this->redirect = $redirect;
        $this->session = $session;
        $this->code = $code;
    }

    public function view(): void
    {
        $data = [
            'messages' => $this->message->pop(),
        ];
        $this->session->destroy();
        $this->page->show('registry', $data);
    }

    public function signup(): void
    {
        $email = strtolower(trim($this->input->getString('email')));
        $password = $this->input->getString('password');
        if (!$this->validate($email, $password)) {
            $this->message->error('Invalid data are given. Please try again.');
            $this->page->show('registry');
            return;
        }
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $token = bin2hex(random_bytes(32));
        $ref = $this->code->generate(30);
        try {
            if (!$this->mysql->insert("        
                INSERT INTO user (email, password_hash, token, ref) VALUES ('$email', '$hash', '$token', '$ref');
            ") || !$this->mailer->send('activation', ['email' => $email], ['token' => $token])) {
                $this->message->error('Registration failed. Please try again later...');
                $this->page->show('registry');
                return;
            }
        } catch (Exception $e) {
            if ($e->getCode() === 1062) {
                $this->message->error('Email address already registered. Try a different email address or log in.');
                $this->page->show('registry');
                return;
            }
        }
        $this->message->success('User information stored. We sent an activation email to your inbox, please check your emails to activate your account. (Don\'t forget to check your spam folder.)');
        $this->redirect->go('login');
    }

    protected function validate(string $email, string $password): bool
    {
        return
            $this->validator->validate('email', $email) &&
            $this->validator->validate('password', $password);
    }

    public function activate(): void
    {
        echo '[ACTIVATION]';
    }
}
