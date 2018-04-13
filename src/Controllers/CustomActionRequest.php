<?php

namespace UncleCheese\BetterButtons\Controllers;

use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\RequestHandler;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\GridField\GridFieldDetailForm_ItemRequest;
use SilverStripe\ORM\DataObject;
use UncleCheese\BetterButtons\Actions\CustomAction;
use UncleCheese\BetterButtons\Extensions\BetterButtons;
use SilverStripe\Control\HTTPResponse;

/**
 * A subcontroller that handles custom actions. The parent controller matches
 * the url_param '$Action!' and doesn't hand off any trailing params. This subcontoller
 * is aware of them
 *
 * /item/4/customaction/my-dataobject-method Invokes "my-dataobject-method" on the record
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class CustomActionRequest extends RequestHandler
{
    /**
     * @var array
     */
    private static $url_handlers = array(
        '$Action!' => 'handleCustomAction'
    );

    /**
     * @var array
     */
    private static $allowed_actions = array(
        'handleCustomAction'
    );

    /**
     * The parent extension. There are actually some useful methods in the extension
     * itself, so we need access to that object
     *
     * @var ItemRequest
     */
    protected $parent;

    /**
     * The record we're editing
     * @var DataObject|BetterButtons
     */
    protected $record;

    /**
     * The Form that is editing the record
     * @var  Form
     */
    protected $form;

    /**
     * Builds the request
     * @param GridFieldDetailForm_ItemRequest|ItemRequest $parent
     * @param Form $form
     */
    public function __construct(GridFieldDetailForm_ItemRequest $parent, Form $form)
    {
        $this->parent = $parent;
        $this->form = $form;
        $this->record = $this->parent->getRecord();
        parent::__construct();
    }

    /**
     * Takes the action at /customaction/my-action-name and feeds it to the DataObject.
     * Checks to see if the method is allowed to be invoked first.
     *
     * @param  HTTPRequest $r
     * @return HTTPResponse
     */
    public function handleCustomAction(HTTPRequest $r)
    {
        $action = $r->param('Action');
        /* @var DataObject|BetterButtons $record */
        $record = $this->record;
        if (!$record->isCustomActionAllowed($action)) {
            return $this->httpError(403);
        }

        /* @var CustomAction $formAction */
        $formAction = $record->findActionByName($action);
        if (!$formAction) {
            return $this->httpError(403, "Action $action doesn't exist");
        }

        $message = $this->record->$action($formAction, $this->controller, $r);

        Controller::curr()->getResponse()->addHeader("X-Pjax", "Content");
        Controller::curr()->getResponse()->addHeader('X-Status', $message);

        if ($formAction->getRedirectURL()) {
            return Controller::curr()->redirect($formAction->getRedirectURL());
        }

        if ($formAction->getRedirectType() == CustomAction::GOBACK) {
            return $this->parent->returnToList();
        }

        return Controller::curr()->redirect(
            $this->parent->Link()
        );
    }
}
