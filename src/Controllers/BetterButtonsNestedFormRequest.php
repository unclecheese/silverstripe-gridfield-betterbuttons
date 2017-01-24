<?php

/**
 * Request handler that deals with nested forms
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class BetterButtonsNestedFormRequest extends BetterButtonsCustomActionRequest
{
    /**
     * Define the allowed controller actions
     * @var array
     */
    private static $allowed_actions = array(
        'Form'
    );

    /**
     * Define URL routes
     * @var array
     */
    private static $url_handlers = array(
        'Form' => 'Form'
    );

    /**
     * Gets a link to this RequestHandler
     */
    public function Link()
    {
        return $this->controller->Link('nestedform');
    }

    /**
     * Create the nested form
     *
     * @return  Form
     */
    public function Form()
    {
        $formAction = $this->getFormActionFromRequest($this->request);
        $fields = $formAction->getFields();
        $fields->push(HiddenField::create('action', '', $formAction->getButtonName()));

        $form = Form::create(
            $this,
            'Form',
            $fields,
            FieldList::create(
                FormAction::create('nestedFormSave', 'Save')
            )
        );

        return $form;
    }

    /**
     * Render the form to the template
     * @param  SS_HTTPRequest $r
     * @return SSViewer
     */
    public function index(SS_HTTPRequest $r)
    {
        Requirements::css(BETTER_BUTTONS_DIR.'/css/betterbuttons_nested_form.css');

        return $this->customise(array(
            'Form' => $this->Form()
        ))->renderWith('BetterButtonNestedForm');
    }

    /**
     * Handles the saving of the nested form. This is essentially a proxy method
     * for the method that the BetterButtonNestedForm button has been configured
     * to use
     *
     * @param  array $data    The form data
     * @param  Form $form     The nested form object
     * @param  SS_HTTPRequest $request
     * @return SS_HTTPResponse
     */
    public function nestedFormSave($data, $form, $request)
    {
        $formAction = $this->getFormActionFromRequest($request);
        $actionName = $formAction->getButtonName();

        $this->record->$actionName($data, $form, $request);

        return Controller::curr()->redirectBack();
    }

    /**
     * Get the action from the request, whether it's part of the form data
     * or in the query string
     *
     * @param  SS_HTTPRequest $r [description]
     * @return BetterButtonNestedForm
     */
    protected function getFormActionFromRequest(SS_HTTPRequest $r)
    {
        $action = $r->requestVar('action');
        $formAction = $this->record->findActionByName($action);

        if (!$formAction instanceof BetterButtonNestedForm) {
            throw new Exception("Action $action doesn't exist or is not a BetterButtonNestedForm");
        }

        return $formAction;
    }
}
