<?php

namespace Madlib;

use Madlib\Config;
use Madlib\Template;
use Madlib\Request;
use Madlib\Session;
use Madlib\Code;
use Madlib\Mysql;
use Madlib\Message;
use Madlib\Input;
use Madlib\Validator;
use Madlib\Redirect;

class Anonym
{
    protected Config $config;
    protected Template $template;
    protected Request $request;
    protected Session $session;
    protected Code $code;
    protected Mysql $mysql;
    protected Message $message;
    protected Input $input;
    protected Validator $validator;
    protected Redirect $redirect;

    public function __construct(
        Config $config,
        Template $template,
        Request $request,
        Session $session,
        Code $code,
        Mysql $mysql,
        Message $message,
        Input $input,
        Validator $validator,
        Redirect $redirect
    ) {
        $this->config = $config;
        $this->template = $template;
        $this->request = $request;
        $this->session = $session;
        $this->code = $code;
        $this->mysql = $mysql;
        $this->message = $message;
        $this->input = $input;
        $this->validator = $validator;
        $this->redirect = $redirect;
    }

    protected function show(array $data = []): void
    {
        $page = $this->template->show(
            $this->config::TEMPLATE['path'] . 'anonym.html.php',
            array_merge(
                [
                    'messages' => $this->message->pop(),
                ],
                $data
            )
        );
    }

    public function get(): void
    {
        $this->session->destroy();
        if ($this->config::SITE['env'] === $this->config::ENV_LIVE) {
            sleep(15);
        }
        $this->show($this->createAnonimAccess());
    }

    public function reset(): void
    {
        $this->show(['reset' => 1]);
    }

    public function postReset(): void
    {
        $passwordOld = trim($this->input->getString('password_old'));
        $passwordNew = trim($this->input->getString('password_new'));
        if (!$this->validator->validate('password', $passwordOld) ||
            !$this->validator->validate('password', $passwordNew) ||
            $passwordOld === $passwordNew
        ) {
            $this->message->error('Invalid password');
            $this->show(['reset' => 1]);
            return;
        }
        $uid = $this->session->get('user_id');
        $user = $this->mysql->selectRow("
            SELECT id, password_hash, activated, created_at, NOW() AS now, last_login_at, last_reset_at FROM user WHERE id = '$uid'
        ");
        $hash = password_hash($passwordNew, PASSWORD_BCRYPT, ['cost' => 12]);
        if ($user && (int)$user['activated'] && trim($user['password_hash']) &&
            trim($passwordOld) && password_verify($passwordOld, $user['password_hash']) &&
            $this->mysql->update("UPDATE user SET password_hash = '$hash', last_reset_at = NOW() WHERE id = $uid")) {
            $this->message->success('Password changed success');
            $this->redirect->go('home');
            return;
        }
        $this->message->error('Password change failed');
        $this->redirect->go('home');
    }

    protected function createAnonimAccess(): array
    {
        $email = $this->code->generate(rand(12, 16), 'abcdefghijklmnopqrstuvwxyz1234567890') . '@anonym.anonym';
        $password = $this->code->generate(rand(12, 16), 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!?,,;:@#%&*-+/');
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $token = bin2hex(random_bytes(32));
        $ref = $this->code->generate(30);
        if (!$this->mysql->insert("INSERT INTO user (email, password_hash, token, ref) VALUES ('$email', '$hash', '$token', '$ref')")) {
            throw new Exception('Key generation error');
        }
        return [
            'email' => $email,
            'password' => $password,
        ];
    }

    public function isAnonymEmail(string $email): bool
    {
        return explode('@', $email)[1] === 'anonym.anonym';
    }

    public function activateFirstAnonymLogin(string $email, array $user): ?array
    {
        if ($this->isAnonymEmail($email)) { // anonym login
            if (!$user['last_login_at']) {  // first anonym login activates the account
                $createdAt = strtotime($user['created_at']);
                $now = strtotime($user['now']);
                if (!(int)$user['activated'] && (($now - $createdAt) / 60) > 10) { // 10 minutes
                    return null;
                }
                $query = "
                    UPDATE user SET activated = 1 WHERE id = {$user['id']}
                ";
                if (!$this->mysql->update($query)) {
                    $this->message->error('Login failed');
                    $this->redirect->go('login');
                    return null;
                }
                $user['activated'] = 1;
            }
        }
        return $user;
    }

    public function deleteExpiredInactiveAnonyms(): int
    {
        return $this->mysql->delete("
            DELETE FROM user                                            -- accounts will be deleted who..
            WHERE
                email LIKE '%@anonym.anonym' AND                         -- has anonym email address and,..
                activated = 0 AND                                       -- who never activated..
                last_login_at IS NULL AND                               -- because never logged in..
                created_at < DATE_SUB(NOW(), INTERVAL 10 MINUTE)   -- and created more then 10 minutes ago
        ");
    }

    public function isAnonymAndPasswordNeedsReset(string $email, int $lastResetAt, int $now): bool
    {
        if (!$this->isAnonymEmail($email)) {
            return false;
        }
        if (!$lastResetAt || ((($now - $lastResetAt) / (60*60*24)) >= 3)) { // 3 days
            return true;
        }
        return false;
    }
}
