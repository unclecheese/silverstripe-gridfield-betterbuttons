<?php


/**
 * Defines the button that deletes a record, with confirmation
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class BetterButton_Delete extends BetterButton {


    /**
     * Builds the button
     * @param Form                            $form    The form that holds the button
     * @param GridFieldDetailForm_ItemRequest $request The request that points to the form
     */
    public function __construct(Form $form, GridFieldDetailForm_ItemRequest $request) {
        parent::__construct('doDelete', _t('GridFieldDetailForm.Delete', 'Delete'), $form, $request);
    }


    /**
     * Adds the JS, sets up necessary HTML attributes
     * @return FormAction
     */
    public function baseTransform() {
        parent::baseTransform();
        Requirements::javascript(BETTER_BUTTONS_DIR.'/javascript/gridfield_betterbuttons_delete.js');

        return $this
            ->setUseButtonTag(true)
            ->addExtraClass('gridfield-better-buttons-delete')
            ->setAttribute("data-toggletext", _t('GridFieldBetterButtons.AREYOUSURE','Yes. Delete this item.'))
            ->setAttribute("data-confirmtext", _t('GridFieldDetailForm.CANCELDELETE', 'No. Don\'t delete.'));
    }


    /**
     * Determines if the button should show
     * @return boolean
     */
    public function shouldDisplay() {
        return !$this->gridFieldRequest->recordIsPublished() && $this->gridFieldRequest->record->canDelete();
    }

}
