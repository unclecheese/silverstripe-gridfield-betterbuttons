<?php


/**
 * Defines the button that publishes a record and closes the detail form
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class BetterButton_PublishAndClose extends BetterButton_SaveAndClose implements BetterButton_Versioned {


    /**
     * Builds the button
     * @param Form                            $form    The form that holds the button
     * @param GridFieldDetailForm_ItemRequest $request The request that points to the form
     */
    public function __construct(Form $form, GridFieldDetailForm_ItemRequest $request) {
        return parent::__construct('doPublishAndQuit', _t('GridFieldDetailForm.PUBLISHANDQUITE','Publish and close'), $form, $request);
    }


    /**
     * Determines if the button should display
     * @return boolean
     */
    public function shouldDisplay() {
        $record = $this->gridFieldRequest->record;
        
        return $record->canEdit();
    }

}