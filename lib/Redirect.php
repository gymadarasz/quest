<?php

namespace Madlib;

class Redirect
{
    protected Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function go(string $location): void
    {
        $this->jump($this->config::SITE['base'] . $location);
    }

    public function jump($url): void
    {
        header('Location: ' . $url);
        exit;
    }
}
