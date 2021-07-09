<?php

namespace Madlib\Account;

use Madlib\Exception;
use Madlib\Config;
use Madlib\Redirect;
use Madlib\Session;
use Madlib\Page;
use Madlib\Mysql;
use Madlib\Input;
use Madlib\Message;

class Account
{
    protected Config $config;
    protected Redirect $redirect;
    protected Session $session;
    protected Page $page;
    protected Mysql $mysql;
    protected Input $input;
    protected Message $message;

    public function __construct(
        Config $config,
        Redirect $redirect,
        Session $session,
        Page $page,
        Mysql $mysql,
        Input $input,
        Message $message
    ) {
        $this->config = $config;
        $this->redirect = $redirect;
        $this->session = $session;
        $this->page = $page;
        $this->mysql = $mysql;
        $this->input = $input;
        $this->message = $message;
    }

    public function view(): void
    {
        if (!$user_id = $this->session->get('user_id')) {
            $this->redirect->go('login');
            return;
        }
        
        $this->page->show('home', array_merge(
            $this->session->get(), 
            $this->config::QUEST,
            [
                'paypal_env' => $this->config::SITE['env'] === $this->config::ENV_LIVE ? 'production' : 'sandbox',
                'user_lang' => $this->getUserLang(),
                'langs' => $this->config::SITE['langs'],
            ]
        ));
    }

    public function getUserLang(): string
    {
        if (!$user_id = $this->session->get('user_id')) {
            throw new Exception('User should log in');
        } 
        if (!$this->session->isset('user_lang')) {
            $user = $this->mysql->selectRow("SELECT lang FROM user WHERE id = $user_id LIMIT 1");
            $this->session->set('user_lang', (string)($user['lang'] ?? ''));
        }
        return $this->session->get('user_lang');
    }

    public function isAdmin(): bool
    {
        if (!$user_id = $this->session->get('user_id')) {
            return false;
        } 
        if (!$this->session->isset('user_admin')) {
            $user = $this->mysql->selectRow("SELECT admin FROM user WHERE id = $user_id LIMIT 1");
            $this->session->set('user_admin', (bool)($user['admin'] ?? ''));
        }
        return $this->session->get('user_admin');
    }

    public function setLang(): void 
    {
        $lang = $this->input->getString('lang');
        if ($lang && !in_array($lang, array_keys($this->config::SITE['langs']), true)) {
            throw new Exception('Invalid language');
        }
        $user_id = (int)$this->session->get('user_id');
        if (!$user_id) {
            throw new Exception('Unable to determinate user');
        }
        $this->mysql->update("UPDATE user SET lang = '$lang' WHERE id = $user_id LIMIT 1");
        $this->session->set('user_lang', $lang);
        $this->message->success("Language set to '" . ($this->config::SITE['langs'][$lang]['name'] ?: 'Unknown') . "' ");
        $this->redirect->go('home');
    }

    public function logout(): void
    {
        $this->session->destroy();
        $this->redirect->go('login');
    }

    public function delete(): void
    {
        $userId = (int)$this->session->get('user_id');
        if (!$userId) {
            $this->redirect->go('login');
            return;
        }
        if (!$this->mysql->delete("
            DELETE FROM user WHERE id = $userId
        ")) {
            throw new Exception('Unable to delete');
        };
        $this->logout();
    }
}
