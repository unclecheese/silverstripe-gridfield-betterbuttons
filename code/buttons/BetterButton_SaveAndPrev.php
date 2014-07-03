<?php

/**
 * Defines the button that saves a record and goes to the previous one
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class BetterButton_SaveAndPrev extends BetterButton {


    /**
     * Builds the button
     * @param Form                            $form    The form that holds the button
     * @param GridFieldDetailForm_ItemRequest $request The request that points to the form
     */
    public function __construct(Form $form, GridFieldDetailForm_ItemRequest $request) {
        parent::__construct("doSaveAndPrev", _t('GridFieldDetailForm.SAVEANDPREV','Save and go to previous record'), $form, $request);
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
     * Updates the button to be disabled if there is no previous record
     * @return FormAction
     */
    public function baseTransform() {
        parent::baseTransform();
        $disabled = (!$this->gridFieldRequest->getPreviousRecordID());

        return $this->setDisabled($disabled);
    }


    /**
     * Adds a class to help identify the button in a group
     * @return FormAction
     */
    public function transformToInput() {        
        return parent::transformToInput()
            ->addExtraClass("saveAndGoPrev");           
    }
}
