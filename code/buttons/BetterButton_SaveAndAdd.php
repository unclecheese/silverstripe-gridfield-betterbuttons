<?php

/**
 * Defines the button that save a record and redirects to creating a new one
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class BetterButton_SaveAndAdd extends BetterButton {


    /**
     * Builds the button
     * @param Form                            $form    The form that holds the button
     * @param GridFieldDetailForm_ItemRequest $request The request that points to the form
     */
    public function __construct(Form $form, GridFieldDetailForm_ItemRequest $request) {
        parent::__construct("doSaveAndAdd",_t('GridFieldBetterButtons.SAVEANDADDNEW','Save and add new'), $form, $request);
    }


    /**
     * Determines if the record should show
     * @return boolean
     */
    public function shouldDisplay() {
        $record = $this->gridFieldRequest->record;

        return $record->canEdit() && $record->canCreate();
    }


    /**
     * Adds the appropriate style and icon
     * @return FormAction
     */
    public function transformToButton() {
        return parent::transformToButton()
            ->addExtraClass("ss-ui-action-constructive")
            ->setAttribute('data-icon', 'add');
    }


    /**
     * Adds a class so the button can be identified in a group
     * @return FormAction
     */
    public function transformToInput() {
        return parent::transformToInput()
            ->addExtraClass("saveAndAddNew");
    }
}
