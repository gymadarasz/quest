<?php

namespace Madlib;

class Message
{
    protected Session $session;
    protected Config $config;

    public function __construct(Session $session, Config $config)
    {
        $this->session = $session;
        $this->config = $config;
    }

    protected function message(string $type, string $message): void
    {
        if ($type === 'debug' && $this->config::SITE['env'] !== $this->config::ENV_DEV) {
            return;
        }
        
        $messages = $this->session->get('messages');

        if (empty($message[$type])) {
            $messages[$type] = [];
        }
        if (!in_array($message, $messages[$type])) {
            $messages[$type][] = $message;
        }

        $this->session->set('messages', $messages);
    }

    public function pop(): array
    {
        $messages = $this->session->get('messages');
        $this->session->unset('messages');
        
        return $messages ? $messages : [];
    }

    public function success(string $message): void
    {
        $this->message('success', $message);
    }

    public function error(string $message): void
    {
        $this->message('error', $message);
    }

    public function alert(string $message): void
    {
        $this->message('alert', $message);
    }

    public function info(string $message): void
    {
        $this->message('info', $message);
    }

    public function debug(string $message): void
    {
        $this->message('debug', $message);
    }
}
