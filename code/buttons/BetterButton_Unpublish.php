<?php


/**
 * Defines the button that unpublishes a record
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class BetterButton_Unpublish extends BetterButton implements BetterButton_Versioned {


    /**
     * Builds the button
     * @param Form                            $form    The form that holds the button
     * @param GridFieldDetailForm_ItemRequest $request The request that points to the form
     */
    public function __construct(Form $form, GridFieldDetailForm_ItemRequest $request) {
        parent::__construct('unpublish', _t('SiteTree.BUTTONUNPUBLISH', 'Unpublish'), $form, $request);
    }


    /**
     * Adds a class to identify this as a destructive action
     * @return void
     */
    public function baseTransform() {   
        parent::baseTransform();
        
        return $this->addExtraClass('ss-ui-action-destructive');       
    }


    /**
     * Determines if the button should show
     * @return boolean
     */
    public function shouldDisplay() {   
        return $this->gridFieldRequest->recordIsPublished() && $this->gridFieldRequest->record->canEdit();
    }
}
