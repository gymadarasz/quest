<?php

namespace Madlib;

class Redirect
{
    protected Config $config;
    protected Request $request;

    public function __construct(Config $config, Request $request)
    {
        $this->config = $config;
        $this->request = $request;
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
