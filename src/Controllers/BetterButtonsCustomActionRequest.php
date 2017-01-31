<?php

namespace UncleCheese\BetterButtons\Controllers;

use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\RequestHandler;
use UncleCheese\BetterButtons\Actions\BetterButtonCustomAction;

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
class BetterButtonsCustomActionRequest extends RequestHandler
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
     * @var GridFieldBetterButtonsItemRequest
     */
    protected $parent;

    /**
     * The parent controller
     * @var GridFieldDetailForm_ItemRequest
     */
    protected $controller;

    /**
     * The record we're editing
     * @var DataObject
     */
    protected $record;

    /**
     * The Form that is editing the record
     * @var  Form
     */
    protected $form;

    /**
     * Buidls the request
     * @param GridFieldBetterButtonsItemRequest $parent     The extension instance
     * @param GridFieldDetailForm_ItemRequest $controller The request that points to the detail form
     */
    public function __construct($parent, $controller, $form)
    {
        $this->parent = $parent;
        $this->controller = $controller;
        $this->form = $form;
        $this->record = $this->controller->record;
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
        if (!$this->record->isCustomActionAllowed($action)) {
            return $this->httpError(403);
        }

        $formAction = $this->record->findActionByName($action);
        if (!$formAction) {
            return $this->httpError(403, "Action $action doesn't exist");
        }

        $message = $this->record->$action($formAction, $this->controller, $r);

        Controller::curr()->getResponse()->addHeader("X-Pjax", "Content");
        Controller::curr()->getResponse()->addHeader('X-Status', $message);

        if ($formAction->getRedirectURL()) {
            return Controller::curr()->redirect($formAction->getRedirectURL());
        }

        if ($formAction->getRedirectType() == BetterButtonCustomAction::GOBACK) {
            return Controller::curr()->redirect(preg_replace('/\?.*/', '', $this->parent->getBackLink()));
        }

        return Controller::curr()->redirect(
            $this->controller->getEditLink($this->record->ID)
        );
    }
}
