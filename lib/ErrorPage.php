<?php

namespace Madlib;

class ErrorPage
{
    protected Page $page;

    public function __construct(Page $page)
    {
        $this->page = $page;
    }

    public function show(): void
    {
        $this->page->show('error-page');
    }
}
