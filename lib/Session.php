<?php

namespace Madlib;

class Session
{
    public function isStarted(): bool
    {
        return session_status() !== PHP_SESSION_NONE;
    }

    public function start(): void
    {
        if (!$this->isStarted()) {
            session_start();
        }
    }

    public function destroy(): void
    {
        $this->start();
        session_destroy();
    }

    public function get(string $key = null)
    {
        $this->start();
        return null === $key ? $_SESSION : ($_SESSION[$key] ?? null);
    }

    public function set(string $key, $value): void
    {
        $this->start();
        $_SESSION[$key] = $value;
    }

    public function unset(string $key): void
    {
        $this->start();
        unset($_SESSION[$key]);
    }

    public function isset(string $key): bool
    {
        $this->start();
        return isset($_SESSION[$key]);
    }
}
