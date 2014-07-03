<?php

/**
 * Defines the button that saves a record and closes the detail form
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class BetterButton_SaveAndClose extends BetterButton {


    /**
     * Builds the button
     * @param Form                            $form    The form that holds the button
     * @param GridFieldDetailForm_ItemRequest $request The request that points to the form
     */
    public function __construct(Form $form, GridFieldDetailForm_ItemRequest $request) {
        parent::__construct("doSaveAndQuit", _t('GridFieldDetailForm.SAVEANDCLOSE', 'Save and close'), $form, $request);

    }


    /**
     * Determines if the button should show
     * @return boolean
     */
    public function shouldDisplay() {
        $record = $this->gridFieldRequest->record;
        
        return $record->canEdit();
    }


    /**
     * Adds a class that helps identify the button when in a group
     * @return FormAction
     */
    public function transformToInput() {
        return parent::transformToInput()
            ->addExtraClass("saveAndClose");
    }


    /**
     * Adds the correct style and icon
     * @return FormAction
     */
    public function transformToButton() {   
        return parent::transformToButton()
            ->addExtraClass("ss-ui-action-constructive")
            ->setAttribute('data-icon', 'accept');
    }
}
