<?php

namespace UncleCheese\BetterButtons\Controllers;

use Exception;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\HiddenField;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\View\Requirements;
use UncleCheese\BetterButtons\Actions\NestedForm;

/**
 * Request handler that deals with nested forms
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class NestedFormRequest extends CustomActionRequest
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
     * @param string $action
     * @return string
     */
    public function Link($action = null)
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
     * @param  HTTPRequest $r
     * @return DBHTMLText
     */
    public function index(HTTPRequest $r)
    {
        Requirements::css('unclecheese/betterbuttons:css/betterbuttons_nested_form.css');

        return $this->customise(array(
            'Form' => $this->Form()
        ))->renderWith(NestedForm::class);
    }

    /**
     * Handles the saving of the nested form. This is essentially a proxy method
     * for the method that the BetterButtonNestedForm button has been configured
     * to use
     *
     * @param  array $data    The form data
     * @param  Form $form     The nested form object
     * @param  HTTPRequest $request
     * @return HTTPResponse
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
     * @param  HTTPRequest $r
     * @return NestedForm
     * @throws Exception If the action doesn't exist, or isn't a BetterButtonNestedForm
     */
    protected function getFormActionFromRequest(HTTPRequest $r)
    {
        $action = $r->requestVar('action');
        $formAction = $this->record->findActionByName($action);

        if (!$formAction instanceof NestedForm) {
            throw new Exception("Action $action doesn't exist or is not a BetterButtonNestedForm");
        }

        return $formAction;
    }
}
