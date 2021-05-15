<?php

namespace Quest;

use Madlib\Page;
class Tos {
    public function __construct(Page $page) {
        $this->page = $page;
    }
    public function view(): void {
        $this->page->show('quest/tos', []);
    }
}