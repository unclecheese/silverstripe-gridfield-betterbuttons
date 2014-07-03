<?php

/**
 * Defines the base class for all buttons that submit form data through GridField
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
abstract class BetterButton extends FormAction {


    /**
     * The request that points to the GridFieldDetailForm
     * @var GridFieldDetailForm_ItemRequest
     */
    protected $gridFieldRequest;


    /**
     * Builds the button
     * @param string                          $name    The name of the form action. Must be a method on the controller 
     * @param string                          $title   The text for the button
     * @param Form                            $form    The form that holds the button
     * @param GridFieldDetailForm_ItemRequest $request The request that points to the form
     */
    public function __construct($name, $title = null, Form $form, GridFieldDetailForm_ItemRequest $request) {
        $this->gridFieldRequest = $request;
        $this->form = $form;

        return parent::__construct($name, $title, $form);
    }


    /**
     * Performs any last-minute transformations to the button in accordance with anything
     * the user may have done after instantiating the button
     *
     * @return BetterButton
     */
    public function baseTransform() {
        return $this;
    }


    /**
     * Tells the form action to become a standard form submit button
     * @return FormAction
     */
    public function transformToButton() {           
        $this->baseTransform();
        return $this->setUseButtonTag(true);
    }


    /**
     * Tells the form action to become a standard input tag, e.g. for usage in a button group
     * @return FormAction
     */
    public function transformToInput() {        
        $this->baseTransform();
        return $this->setUseButtonTag(false);
    }


    /**
     * Determines if the button should display or not
     * @return bool
     */
    public function shouldDisplay() {
        return true;
    }   
}