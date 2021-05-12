<?php

namespace Madlib;

class Request
{
    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? '';
    }

    public function getRoute(): string
    {
        return $_GET['route'] ?? '';
    }

    public function get(string $key): string
    {
        return $_POST[$key] ?? ($_GET[$key] ?? ($_REQUEST[$key] ?? ''));
    }

    public function getArray(string $key): array
    {
        return $_POST[$key] ?? ($_GET[$key] ?? ($_REQUEST[$key] ?? []));
    }

    public function getServer($key): string
    {
        return $_SERVER[$key] ?? '';
    }

    public function getServerRequestUri(): string {
        return $_SERVER['REQUEST_URI'];
    }
}
