<?php

namespace Madlib\Account;

use Madlib\Input;
use Madlib\Redirect;
use Madlib\Mysql;
use Madlib\Page;
use Madlib\Message;
use Madlib\Mailer;
use Madlib\Validator;
use Madlib\Session;

class Forgot
{
    protected Input $input;
    protected Redirect $redirect;
    protected Mysql $mysql;
    protected Page $page;
    protected Message $message;
    protected Mailer $mailer;
    protected Validator $validator;
    protected Session $session;
    protected Token $token;

    public function __construct(
        Input $input,
        Redirect $redirect,
        Mysql $mysql,
        Page $page,
        Message $message,
        Mailer $mailer,
        Validator $validator,
        Session $session,
        Token $token
    ) {
        $this->input = $input;
        $this->redirect = $redirect;
        $this->mysql = $mysql;
        $this->page = $page;
        $this->message = $message;
        $this->mailer = $mailer;
        $this->validator = $validator;
        $this->session = $session;
        $this->token = $token;
    }

    public function forgot(): void
    {
        $this->page->show('forgot');
    }

    public function reset(): void
    {
        $email = strtolower(trim($this->input->getString('email')));
        if (!$this->validator->validate('email', $email)) {
            $this->message->error('Invalid email format');
            $this->page->show('forgot');
            return;
        }
        if (!$this->token->reset($email, 'reset')) {
            $this->message->error('Password reset failed. Please make sure the email address is correct or try again later...');
            $this->page->show('forgot');
            return;
        }
        $this->message->success('We sent a password reset email to your inbox, please check your emails to reset your password. (Don\'t forget to check your spam folder.)');
        $this->page->show('forgot');
    }

    public function change(): void
    {
        $token = $this->input->getString('token');
        if (!$this->validator->validate('required', $token)) {
            $this->message->error('Missing token');
            $this->redirect->go('reset');
            return;
        }
        $user = $this->mysql->selectRow("
            SELECT id FROM user WHERE token = '$token'
        ");
        $this->session->set('token', $token);
        $this->page->show('change');
    }

    public function replace(): void
    {
        $token = $this->session->get('token');
        if (!$this->validator->validate('required', $token)) {
            $this->message->error('Invalid token');
            $this->redirect->go('reset');
            return;
        }
        $password = $this->input->getString('password');
        if (!$this->validator->validate('password', $password)) {
            $this->message->error('Invalid password given, password is minimum 6 characters and should contains a lowercase and uppercase caracters and a number at least. Try a bit more difficult one...');
            $this->page->show('change');
            return;
        }
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        if (!$this->mysql->update("
            UPDATE user SET activated = 1, password_hash = '$hash' WHERE token = '" . $this->session->get('token') . "'
        ")) {
            $this->message->error('Password change failed. Please try again later...');
            $this->page->show('change');
            return;
        }
        $this->message->success('Password changed. Please log in.');
        $this->redirect->go('login');
    }
}
