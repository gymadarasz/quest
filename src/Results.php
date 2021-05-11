<?php

namespace Quest;

use Madlib\Exception;
use Madlib\Input;
use Madlib\Validator;
use Madlib\Mysql;
use Madlib\Page;

class Results
{
    protected Input $input;
    protected Validator $validator;
    protected Mysql $mysql;
    protected Page $page;

    public function __construct(Input $input, Validator $validator, Mysql $mysql, Page $page) {
        $this->input = $input;
        $this->validator = $validator;
        $this->mysql = $mysql;
        $this->page = $page;
    }

    public function show(): void {
        $contact_id = $this->input->getInt('contact_id');

        if (!$this->validator->validate('required', $contact_id)) {
            throw new Exception('Invalid parameters');
        }

        $contact = $this->mysql->selectRow("SELECT * FROM contact WHERE id = $contact_id");
        if (!$contact) {
            throw new Exception('Contact is not found');
        }

        $results = $this->mysql->select("
            SELECT 
                result.id AS result_id,               
                quest.id AS quest_id,
                question.id AS question_id,
                answer.id AS answer_id,
                question.label AS question_label,
                answer.label AS answer_label
            FROM result 
            JOIN quest ON quest.id = result.quest_id
            JOIN question ON question.id = result.question_id
            JOIN answer ON answer.id = result.answer_id
            WHERE contact_id = $contact_id
        ");

        if (!$results) {
            throw new Exception('Retrieve results failed');
        }

        $quest = $this->mysql->selectRow("SELECT * FROM quest WHERE id = {$results[0]['quest_id']}");
        if (!$quest) {
            throw new Exception('Quest is not found');
        }
        

        $this->page->show('quest/results', [
            'quest' => $quest,
            'contact' => $contact,
            'results' => $results,
        ]);
    }
}