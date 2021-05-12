<?php

namespace Quest;

use Madlib\Crud;
use Madlib\Page;
use Madlib\Mysql;
use Madlib\Message;
use Madlib\Redirect;
use Madlib\Input;
use Madlib\Validator;
use Madlib\Session;

class ContactCrud extends Crud
{
    protected const LIST_PAGE_TEMPLATE = 'quest/contact.list';
    protected const CREATE_PAGE_TEMPLATE = null;
    protected const CREATE_REDIRECT = null;
    protected const CREATE_FORM_ACTION = null;
    protected const DELETE_REDIRECT = null;
    protected const REQUESTED_ITEM_ID_KEY = null;
    protected const EDIT_PAGE_TEMPLATE = null;
    protected const EDIT_FORM_ACTION = null;
    protected const UPDATE_REDIRECT = null;
    protected const VIEW_PAGE_TEMPLATE = null;

    protected Session $session;

    public function __construct(
        Page $page,
        Mysql $mysql,
        Message $message,
        Redirect $redirect,
        Input $input,
        Validator $validator,
        Session $session
    ) {
        parent::__construct($page, $mysql, $message, $redirect, $input, $validator);
        $this->session = $session;
    }

    protected function getCreateQuery(array $data): string {
        throw new Exception('Unimplemented');
    }

    protected function getNewInputData(): array {
        throw new Exception('Unimplemented');
    }
    
    protected function isValidNewInputData(array $data): bool {
        throw new Exception('Unimplemented');
    }
    
    protected function getListQuery(): string {
        $user_ref = $this->mysql->esc($this->session->get('user_ref'));
        return "SELECT * FROM contact WHERE user_ref = '$user_ref'";
    }
    
    protected function getDeleteQuery(int $id): string {
        throw new Exception('Unimplemented');
    }
    
    protected function getItemQuery(int $id): string {
        $user_ref = $this->mysql->esc($this->session->get('user_ref'));
        return "SELECT * FROM contact WHERE id = $id AND user_ref = '$user_ref'";
    }
    
    protected function getUpdateQuery(array $data): string {
        throw new Exception('Unimplemented');
    }
    
    protected function getEditInputData(): array {
        throw new Exception('Unimplemented');
    }
    
    protected function isValidEditInputData(array $data): bool {
        throw new Exception('Unimplemented');
    }
    

}