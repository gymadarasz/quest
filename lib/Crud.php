<?php

namespace Madlib;

abstract class Crud
{
    protected const LIST_PAGE_TEMPLATE = null;
    protected const CREATE_PAGE_TEMPLATE = null;
    protected const CREATE_REDIRECT = null;
    protected const CREATE_FORM_ACTION = null;
    protected const DELETE_REDIRECT = null;
    protected const REQUESTED_ITEM_ID_KEY = null;
    protected const EDIT_PAGE_TEMPLATE = null;
    protected const EDIT_FORM_ACTION = null;
    protected const UPDATE_REDIRECT = null;
    protected const VIEW_PAGE_TEMPLATE = null;

    protected const CREATE_FORM_BUTTON_LABEL = 'Create';
    protected const CREATE_SUCCESS_MESSAGE = 'Data created';
    protected const CREATE_ERROR_MESSAGE = 'Create error, please try again later...';
    protected const INVALID_NEW_INPUT_DATA_MESSAGE = 'Invalid data, please try again...';
    protected const DELETE_SUCCESS_MESSAGE = 'Deleted';
    protected const DELETE_ERROR_MESSAGE = 'Unable to delete, please try again later...';
    protected const EDIT_FORM_BUTTON_LABEL = 'Update';
    protected const UPDATE_SUCCESS_MESSAGE = 'Updated';
    protected const UPDATE_UNCHANGED_MESSAGE = 'Not changed';
    protected const INVALID_EDIT_INPUT_DATA_MESSAGE = 'Invalid data, please try again...';

    protected Page $page;
    protected Mysql $mysql;
    protected Message $message;
    protected Redirect $redirect;
    protected Input $input;
    protected Validator $validator;

    public function __construct(
        Page $page,
        Mysql $mysql,
        Message $message,
        Redirect $redirect,
        Input $input,
        Validator $validator
    ) {
        $this->page = $page;
        $this->mysql = $mysql;
        $this->message = $message;
        $this->redirect = $redirect;
        $this->input = $input;
        $this->validator = $validator;
    }

    /**
     * Use this as a controller end-point for routing to get a List View
     */
    public function list(): void
    {
        $list = $this->mysql->select($this->getListQuery());
        $this->page->show($this->getListPageTemplate(), ['list' => $list]);
    }

    /**
     * Use this as a controller end-point for routing to get a Create Form view
     */
    public function new(array $defaults = []): void
    {
        $this->page->show(
            $this->getCreatePageTemplate(),
            array_merge([
                'action' => $this->getCreateFormAction(),
                'button' => $this->getCreateFormButtonLabel()
            ], $defaults)
        );
    }

    /**
     * Use this as a controller end-point for routing to get Create Form action
     */
    public function create(): void
    {
        $data = $this->getNewInputDataValidated();
        if ($data && $this->createData($data)) {
            $this->message->success($this->getCreateSuccessMessage());
            $this->redirect->go($this->getCreateRedirect());
        }
    }

    /**
     * Use this as a controller end-point for routing to get item Deleted
     */
    public function delete(): void
    {
        if (
            !$this->mysql->delete(
                $this->getDeleteQuery(
                    $this->input->getInt($this->getRequestedItemIdKey())
                )
            )
        ) {
            $this->message->error($this->getDeleteErrorMessage());
        } else {
            $this->message->success($this->getDeleteSuccessMessage());
        }
        $this->redirect->go($this->getDeleteRedirect());
    }

    /**
     * Use this as a controller end-point for routing to get an Edit Form view
     */
    public function edit(): void
    {
        $this->showEditPage($this->getItem());
    }

    /**
     * Use this as a controller end-point for routing to get Edit Form action
     */
    public function update(): void
    {
        $data = $this->getEditInputDataValidated();
        if ($data && $this->updateData($data)) {
            $this->message->success($this->getUpdateSuccessMessage());
            $this->redirect->go($this->getUpdateRedirect());
        }
    }

    /**
     * Use this as a controller end-point for routing to get Item view
     */
    public function view(): void
    {
        $this->showViewPage($this->getItem());
    }

    protected function getCreateFormAction(): string
    {
        if (null === $this::CREATE_FORM_ACTION) {
            throw new Exception('Create form action is not set');
        }
        return $this::CREATE_FORM_ACTION;
    }

    protected function getCreateFormButtonLabel(): string
    {
        return $this::CREATE_FORM_BUTTON_LABEL;
    }

    protected function createData(array $data): bool
    {
        if (!$this->mysql->insert($this->getCreateQuery($data))) {
            $this->message->error($this->getCreateErrorMessage());
            $this->new($data);
            return false;
        }
        return true;
    }


    protected function getCreateRedirect(): string
    {
        if (null === $this::CREATE_REDIRECT) {
            throw new Exception('Create redirect is not set');
        }
        return $this::CREATE_REDIRECT;
    }

    protected function getCreateSuccessMessage(): string
    {
        return $this::CREATE_SUCCESS_MESSAGE;
    }

    protected function getCreateErrorMessage(): string
    {
        return $this::CREATE_ERROR_MESSAGE;
    }

    abstract protected function getCreateQuery(array $data): string;

    protected function getNewInputDataValidated(): ?array
    {
        $data = $this->getNewInputData();
        if (!$this->isValidNewInputData($data)) {
            $this->message->error($this->getInvalidNewInputDataMessage());
            $this->new($data);
            return null;
        }
        return $data;
    }

    protected function getInvalidNewInputDataMessage(): string
    {
        return $this::INVALID_NEW_INPUT_DATA_MESSAGE;
    }

    abstract protected function getNewInputData(): array;
    abstract protected function isValidNewInputData(array $data): bool;
    abstract protected function getListQuery(): string;

    protected function getListPageTemplate(): string
    {
        if (null === $this::LIST_PAGE_TEMPLATE) {
            throw new Exception('List page template is not set');
        }
        return $this::LIST_PAGE_TEMPLATE;
    }

    protected function getCreatePageTemplate(): string
    {
        if (null === $this::CREATE_PAGE_TEMPLATE) {
            throw new Exception('Create page template is not set');
        }
        return $this::CREATE_PAGE_TEMPLATE;
    }

    protected function getDeleteSuccessMessage(): string
    {
        return $this::DELETE_SUCCESS_MESSAGE;
    }

    protected function getDeleteErrorMessage(): string
    {
        return $this::DELETE_ERROR_MESSAGE;
    }

    abstract protected function getDeleteQuery(int $id): string;

    protected function getDeleteRedirect(): string
    {
        if (null === $this::DELETE_REDIRECT) {
            throw new Exception('Delete redirect is not set');
        }
        return $this::DELETE_REDIRECT;
    }

    protected function getRequestedItemIdKey(): string
    {
        if (null === $this::REQUESTED_ITEM_ID_KEY) {
            throw new Exception('Requested item id key is not set');
        }
        return $this::REQUESTED_ITEM_ID_KEY;
    }

    protected function getItem(): array
    {
        $item = $this->mysql->selectRow(
            $this->getItemQuery(
                $this->input->getInt(
                    $this->getRequestedItemIdKey()
                )
            )
        );
        if (!$item) {
            throw new Exception('Requested item is not found');
        }

        return $item;
    }

    protected function showEditPage(array $item): void
    {
        $this->page->show(
            $this->getEditPageTemplate(),
            array_merge([
                'action' => $this->getEditFormAction(),
                'button' => $this->getEditFormButtonLabel()
            ], $item)
        );
    }

    abstract protected function getItemQuery(int $id): string;

    protected function getEditPageTemplate(): string
    {
        if (null === $this::EDIT_PAGE_TEMPLATE) {
            throw new Exception('Edit page template is not set');
        }
        return $this::EDIT_PAGE_TEMPLATE;
    }

    protected function getEditFormAction(): string
    {
        if (null === $this::EDIT_FORM_ACTION) {
            throw new Exception('Edit form action is not set');
        }
        return $this::EDIT_FORM_ACTION;
    }

    protected function getEditFormButtonLabel(): string
    {
        return $this::EDIT_FORM_BUTTON_LABEL;
    }

    protected function updateData(array $data): bool
    {
        if (!$this->mysql->update($this->getUpdateQuery($data))) {
            $this->message->alert($this->getUpdateErrorMessage());
            $this->showEditPage($data);
            return false;
        }
        return true;
    }

    protected function getEditInputDataValidated(): ?array
    {
        $data = $this->getEditInputData();
        if (!$this->isValidEditInputData($data)) {
            $this->message->error($this->getInvalidEditInputDataMessage());
            $this->showEditPage($data);
            return null;
        }
        return $data;
    }

    abstract protected function getUpdateQuery(array $data): string;

    protected function getUpdateSuccessMessage(): string
    {
        return $this::UPDATE_SUCCESS_MESSAGE;
    }

    protected function getUpdateErrorMessage(): string
    {
        return $this::UPDATE_UNCHANGED_MESSAGE;
    }

    protected function getUpdateRedirect(): string
    {
        if (null === $this::UPDATE_REDIRECT) {
            throw new Exception('Update redirect is not set');
        }
        return $this::UPDATE_REDIRECT;
    }

    abstract protected function getEditInputData(): array;
    abstract protected function isValidEditInputData(array $data): bool;

    protected function getInvalidEditInputDataMessage(): string
    {
        return $this::INVALID_EDIT_INPUT_DATA_MESSAGE;
    }

    protected function showViewPage(array $item): void
    {
        $this->page->show($this->getViewPageTemplate(), $item);
    }

    protected function getViewPageTemplate(): string
    {
        if (null === $this::VIEW_PAGE_TEMPLATE) {
            throw new Exception('View page template is not set');
        }
        return $this::VIEW_PAGE_TEMPLATE;
    }
}
