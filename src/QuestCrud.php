<?php

namespace Quest;

use Madlib\Crud;

class QuestCrud extends Crud {

    protected const LIST_PAGE_TEMPLATE = 'quest/quest.list';
    protected const CREATE_PAGE_TEMPLATE = 'quest/quest.new';
    protected const CREATE_REDIRECT = 'quests';
    protected const CREATE_FORM_ACTION = 'create';
    protected const DELETE_REDIRECT = 'quests';
    protected const REQUESTED_ITEM_ID_KEY = 'id';
    protected const EDIT_PAGE_TEMPLATE = 'quest/quest.edit';
    protected const EDIT_FORM_ACTION = 'update';
    protected const UPDATE_REDIRECT = 'quests';
    protected const VIEW_PAGE_TEMPLATE = 'quest/quest.view';

    protected function getCreateQuery(array $data): string {
        return "INSERT INTO quest (name) VALUES ('{$data['name']}')";
    }
    
    protected function getNewInputData(): array {
        return [
            'name' => $this->input->getString('name'),
        ];
    }  
    
    protected function isValidNewInputData(array $data): bool {
        return $this->validator->validate('required', $data['name']);
    }

    protected function getListQuery(): string {
        return "SELECT * FROM quest";
    }

    protected function getDeleteQuery(int $id): string {
        return "DELETE FROM quest WHERE id = $id";
    }

    protected function getItemQuery(int $id): string {
        return "SELECT * FROM quest WHERE id = $id";
    }

    protected function getUpdateQuery(array $data): string {
        return "UPDATE quest SET name = '{$data['name']}' WHERE id = {$data['id']}";
    }

    protected function getEditInputData(): array {
        return [
            'id' => $this->input->getInt('id'),
            'name' => $this->input->getString('name'),
        ];
    }

    protected function isValidEditInputData(array $data): bool {
        return 
            $this->validator->validate('required', $data['id']) && 
            $this->validator->validate('required', $data['name']);
    }

    // questions

    protected function getItem(): array {
        $item = parent::getItem();        
        $item['questions'] = $this->mysql->select("SELECT * FROM question WHERE quest_id = {$item['id']}");
        foreach ($item['questions'] as &$question) {
            $question['answers'] = $this->mysql->select("SELECT * FROM answer WHERE question_id = {$question['id']}");
        }
        return $item;
    }

    public function createQuestion(): void {
        $quest_id = $this->input->getInt('quest_id');
        $label = $this->input->getString('label');

        if (
            !$this->validator->validate('required', $quest_id) ||
            !$this->validator->validate('required', $label)
        ) {
            throw new Exception('Invalid parameters');
        }

        $query = "INSERT INTO question (quest_id, label) VALUES ($quest_id, '$label')";
        if (!$this->mysql->insert($query)) {
            throw new Exception('Creation failed');
        }
        $this->message->success('Created');
        $this->redirect->go('quests/edit?id=' . $quest_id);
    }

    public function updateQuestion(): void {
        $quest_id = $this->input->getInt('quest_id');
        $question_id = $this->input->getInt('question_id');
        $label = $this->input->getString('label');

        if (
            !$this->validator->validate('required', $quest_id) ||
            !$this->validator->validate('required', $question_id) ||
            !$this->validator->validate('required', $label)
        ) {
            throw new Exception('Invalid parameters');
        }

        $query = "UPDATE question SET label = '$label' WHERE id = $question_id";
        if (!$this->mysql->insert($query)) {
            throw new Exception('Update failed');
        }
        $this->message->success('Updated');
        $this->redirect->go('quests/edit?id=' . $quest_id);
    }

    public function deleteQuestion(): void {
        $quest_id = $this->input->getInt('quest_id');
        $question_id = $this->input->getInt('question_id');

        if (
            !$this->validator->validate('required', $quest_id) ||
            !$this->validator->validate('required', $question_id)
        ) {
            throw new Exception('Invalid parameters');
        }

        $query = "DELETE FROM question WHERE id = $question_id";
        if (!$this->mysql->delete($query)) {
            throw new Exception('Delete failed');
        }
        $this->message->success('Deleted');
        $this->redirect->go('quests/edit?id=' . $quest_id);
    }

    public function createAnswer(): void {
        $quest_id = $this->input->getInt('quest_id');
        $question_id = $this->input->getInt('question_id');
        $label = $this->input->getString('label');

        if (
            !$this->validator->validate('required', $quest_id) ||
            !$this->validator->validate('required', $question_id) ||
            !$this->validator->validate('required', $label)
        ) {
            throw new Exception('Invalid parameters');
        }

        $query = "INSERT INTO answer (question_id, label) VALUES ($question_id, '$label')";
        if (!$this->mysql->insert($query)) {
            throw new Exception('Creation failed');
        }
        $this->message->success('Created');
        $this->redirect->go('quests/edit?id=' . $quest_id);
    }

    public function updateAnswer(): void {
        $quest_id = $this->input->getInt('quest_id');
        $answer_id = $this->input->getInt('answer_id');
        $label = $this->input->getString('label');

        if (
            !$this->validator->validate('required', $quest_id) ||
            !$this->validator->validate('required', $answer_id) ||
            !$this->validator->validate('required', $label)
        ) {
            throw new Exception('Invalid parameters');
        }

        $query = "UPDATE answer SET label = '$label' WHERE id = $answer_id";
        if (!$this->mysql->update($query)) {
            throw new Exception('Update failed');
        }
        $this->message->success('Updated');
        $this->redirect->go('quests/edit?id=' . $quest_id);
    }

    public function deleteAnswer(): void {
        $quest_id = $this->input->getInt('quest_id');
        $answer_id = $this->input->getInt('answer_id');

        if (
            !$this->validator->validate('required', $quest_id) ||
            !$this->validator->validate('required', $answer_id)
        ) {
            throw new Exception('Invalid parameters');
        }

        $query = "DELETE FROM answer WHERE id = $answer_id";
        if (!$this->mysql->delete($query)) {
            throw new Exception('Delete failed');
        }
        $this->message->success('Deleted');
        $this->redirect->go('quests/edit?id=' . $quest_id);
    }

    // fill

    public function fillQuest(): void {
        $name = $this->input->getString('name');
        $address = $this->input->getString('address');
        $email = $this->input->getString('email');
        $phone = $this->input->getString('phone');

        $quest_id = $this->input->getInt('quest_id');
        $quest = $this->input->getIntArrayAssoc('quest');

        if (
            !$this->validator->validate('required', $name) ||
            !$this->validator->validate('required', $quest_id) ||
            !$quest
        ) {
            throw new Exception("Invalid parameters");
        }

        $query = "INSERT INTO contact (name, address, email, phone) VALUES ('$name', '$address', '$email', '$phone')";
        if (!$this->mysql->insert($query)) {
            throw new Exception("Insert error");
        }
        $contact_id = (int)$this->mysql->getMysqli()->insert_id;

        $error = 0;
        foreach ($quest as $question_id => $answer_id) {
            $query = "
                INSERT INTO result (contact_id, quest_id, question_id, answer_id) 
                VALUES ($contact_id, $quest_id, $question_id, $answer_id)";
            if (!$this->mysql->insert($query)) {
                $error++;
            }
        }
        if ($error) {
            throw new Exception('Inserts failure: ' . $error);
        }

        $this->message->success('Created');
        $this->redirect->go('quests/view?id=' . $quest_id);
    }

}