<?php

namespace Madlib\Account;

use Madlib\Exception;
use Madlib\Config;
use Madlib\Redirect;
use Madlib\Session;
use Madlib\Page;
use Madlib\Mysql;

class Account
{
    protected Config $config;
    protected Redirect $redirect;
    protected Session $session;
    protected Page $page;
    protected Mysql $mysql;

    public function __construct(
        Config $config,
        Redirect $redirect,
        Session $session,
        Page $page,
        Mysql $mysql
    ) {
        $this->config = $config;
        $this->redirect = $redirect;
        $this->session = $session;
        $this->page = $page;
        $this->mysql = $mysql;
    }

    public function view(): void
    {
        if (!$user_id = $this->session->get('user_id')) {
            $this->redirect->go('login');
            return;
        }
        
        $this->page->show('home', []);
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
