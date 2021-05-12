<?php

namespace Madlib\Account;

use Madlib\Session;
use Madlib\Input;
use Madlib\Mysql;
use Madlib\Redirect;
use Madlib\Message;
use Madlib\Page;
use Madlib\Validator;
use Madlib\Config;
use Madlib\Anonym;

class Login
{
    protected Session $session;
    protected Input $input;
    protected Mysql $mysql;
    protected Redirect $redirect;
    protected Message $message;
    protected Page $page;
    protected Token $token;
    protected Validator $validator;
    protected Config $config;
    protected Anonym $anonym;

    public function __construct(
        Session $session,
        Input $input,
        Mysql $mysql,
        Redirect $redirect,
        Message $message,
        Page $page,
        Token $token,
        Validator $validator,
        Config $config,
        Anonym $anonym
    ) {
        $this->session = $session;
        $this->input = $input;
        $this->mysql = $mysql;
        $this->redirect = $redirect;
        $this->message = $message;
        $this->page = $page;
        $this->token = $token;
        $this->validator = $validator;
        $this->config = $config;
        $this->anonym = $anonym;
    }

    public function view(): void
    {
        $data = [
            'messages' => $this->message->pop(),
            'redirect' => $this->session->get('redirect'),
        ];
        $this->session->destroy();
        $this->page->show('login', $data);
    }

    public function auth(): void
    {
        $this->session->unset('user_id');

        $this->anonym->deleteExpiredInactiveAnonyms(); // TODO: this could be a cron job too

        if ($this->config::SITE['env'] === $this->config::ENV_LIVE) {
            sleep(rand(1, 3)); // security delay...
        }
        $email = strtolower(trim($this->input->getString('email')));
        $password = $this->input->getString('password');

        if (
            !$this->validator->validate('email', $email) ||
            !$this->validator->validate('password', $password)
        ) {
            $this->message->error('Login failed');
            $this->redirect->go('login');
            return;
        }

        $user = $this->mysql->selectRow("
            SELECT 
                id, password_hash, activated, created_at, NOW() AS now, last_login_at, last_reset_at, admin, 
                subscribed_until_at, subscribed_until_at > NOW() AS subscribed, ref 
            FROM user 
            WHERE email = '$email'
        ");
        if ($user && trim($user['password_hash']) && trim($password) && password_verify($password, $user['password_hash'])) {
            if (!($user = $this->anonym->activateFirstAnonymLogin($email, $user)) || !(int)$user['activated']) {
                $this->message->error('Login failed');
                $this->redirect->go('login');
                return;
            }

            $this->session->set('user_id', $user['id']);
            $this->session->set('user_admin', (int)$user['admin']);
            $this->session->set('user_subscribed_until_at', $user['subscribed_until_at']);
            $this->session->set('user_subscribed', (int)$user['subscribed']);
            $this->session->set('user_ref', $user['ref']);
            $this->message->success('Logged in.' . ($user['last_login_at'] ? ' - Last login was at ' . $user['last_login_at'] : ''));
            $query = "
                UPDATE user SET last_login_at = NOW() WHERE id = {$user['id']}
            ";
            if (!$this->mysql->update($query)) {
                trigger_error('Last login update failed');
            }
            $redirect = $this->input->getString('redirect');
            if ($this->anonym->isAnonymAndPasswordNeedsReset($email, strtotime($user['last_reset_at']), strtotime($user['now']))) {
                $redirect = 'anonym/reset';
            }
            if ($redirect) {
                $this->redirect->jump($redirect . '#redirected');
                return;
            }
            $this->redirect->go('home');
            return;
        }
        if (!(int)$user['activated'] && $this->token->reset($email, 'activation')) {
            if ($this->anonym->isAnonymEmail($email)) {
                $this->message->error('Login failed');
                $this->redirect->go('login');
                return;
            }
            $this->message->alert('Your account is still not activated. We re-sent an activation email to you, please check your emails to activate your account first. (Don\'t forget to check your spam folder.)');
            $this->redirect->go('login');
            return;
        }
        $this->message->error('Login failed');
        $this->redirect->go('login');
    }
}
