<?php

/**
 * Defines the button that creates a new record
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class BetterButton_New extends BetterButton {


    /**
     * Builds the button
     * @param Form                            $form    The form that holds the button
     * @param GridFieldDetailForm_ItemRequest $request The request that points to the form
     */
    public function __construct() {
        parent::__construct("doNew", _t('GridFieldBetterButtons.NEWRECORD','New record'));
    }


    /**
     * Add the necessary classes and icons
     * @return FormAction
     */
    public function baseTransform() {
        parent::baseTransform();
        
        return $this
            ->addExtraClass("ss-ui-action-constructive")
            ->setAttribute('data-icon', 'add');
    }


    /**
     * Determines if the button should show
     * @return boolean
     */
    public function shouldDisplay() {
         // Do not show create new within create new
        if($this->gridFieldRequest->getRequest()->param('ID') == 'new') {
            return false;
        }
        return $this->gridFieldRequest->record->canCreate();
    }
}
