<?php


/**
 * Defines the button that publishes a record, then proceeds to create a new one
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class BetterButton_PublishAndAdd extends BetterButton_SaveAndAdd implements BetterButton_Versioned {


    /**
     * Builds the button
     * @param Form                            $form    The form that holds the button
     * @param GridFieldDetailForm_ItemRequest $request The request that points to the form
     */
    public function __construct(Form $form, GridFieldDetailForm_ItemRequest $request) {
        return parent::__construct('doPublishAndAdd', _t('GridFieldDetailForm.PUBLISHANDADD','Publish and add new'), $form, $request);
    }


    /**
     * Determines if the button should display
     * @return boolean
     */
    public function shouldDisplay() {
        $record = $this->gridFieldRequest->record;
        
        return $record->canEdit() && $record->canCreate();
    }

}
