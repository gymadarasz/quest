<?php

namespace Madlib\Account;

use Madlib\Input;
use Madlib\Message;
use Madlib\Mysql;
use Madlib\Redirect;
use Madlib\Validator;

class Activation
{
    protected Input $input;
    protected Message $message;
    protected Validator $validator;
    protected Mysql $mysql;
    protected Redirect $redirect;

    public function __construct(
        Input $input,
        Message $message,
        Validator $validator,
        Mysql $mysql,
        Redirect $redirect
    ) {
        $this->input = $input;
        $this->message = $message;
        $this->validator = $validator;
        $this->mysql = $mysql;
        $this->redirect = $redirect;
    }

    public function activate(): void
    {
        $token = $this->input->getString('token');
        if (!$this->validator->validate('required', $token) || !$this->mysql->update("
            UPDATE user SET activated = 1 WHERE token = '$token'
        ")) {
            $this->message->error('Invalid token');
            $this->redirect->go('login');
            return;
        }
        $this->message->success('Your account is activated. Please log in.');
        $this->redirect->go('login');
    }
}
