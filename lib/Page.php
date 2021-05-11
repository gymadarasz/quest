<?php

namespace Madlib;

class Page
{
    protected Config $config;
    protected Template $template;
    protected Message $message;

    public function __construct(Config $config, Template $template, Message $message)
    {
        $this->config = $config;
        $this->template = $template;
        $this->message = $message;
    }

    public function show(string $page, array $data = [])
    {
        if (!isset($this->config::SEO[$page])) {
            throw new Exception("SEO missing for page: [$page]");
        }
        if (!isset($this->config::PAGE_TEMPLATES[$page])) {
            throw new Exception("Page template is missing: [$page]");
        }
        $this->template->show($this->config::TEMPLATE['path'] . 'index.html.php', array_merge([
            'title' => $this->config::SEO[$page]['title'],
            'messages' => $data['messages'] ?? $this->message->pop(),
            'page' => $this->config::PAGE_TEMPLATES[$page],
        ], $data));
    }
}
