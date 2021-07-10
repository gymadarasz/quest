<?php

namespace Quest;

use Madlib\Input;
use Madlib\Page;
use Madlib\Mysql;
use Madlib\Session;

class Search
{
    protected Input $input;
    protected Page $page;
    protected Mysql $mysql;
    protected Session $session;

    public function __construct(Input $input, Page $page, Mysql $mysql, Session $session) {
        $this->input = $input;
        $this->page = $page;
        $this->mysql = $mysql;
        $this->session = $session;
    }

    public function show(): void {
        $keyword = $this->input->getString('keyword');
        $results = [
            'contacts' => [],
            'quests' => [],
        ];
        if ($keyword) {
            $results = [
                'contacts' => $this->mysql->select("
                    SELECT contact.id AS contact_id, contact.name AS name FROM contact 
                    WHERE name LIKE '%$keyword%' OR address LIKE '%$keyword%' OR email LIKE '%$keyword%' OR phone LIKE '%$keyword%'
                "),
                'quests' => $this->mysql->select("
                    SELECT quest.id AS quest_id, quest.name AS name FROM quest 
                    JOIN question ON question.quest_id = quest.id
                    JOIN answer ON answer.question_id = question.id
                    WHERE quest.name LIKE '%$keyword%' OR question.label LIKE '%$keyword%' OR answer.label LIKE '%$keyword%'
                    GROUP BY quest.id
                "),
            ];
        }
        $results['user_ref'] = $this->session->get('user_ref');
        $results['keyword'] = $keyword;
        $this->page->show('quest/search', $results);
    }
}