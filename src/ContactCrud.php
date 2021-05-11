<?php

namespace Quest;

use Madlib\Crud;

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
        return "SELECT * FROM contact";
    }
    
    protected function getDeleteQuery(int $id): string {
        throw new Exception('Unimplemented');
    }
    
    protected function getItemQuery(int $id): string {
        return "SELECT * FROM contact WHERE id = $id";
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