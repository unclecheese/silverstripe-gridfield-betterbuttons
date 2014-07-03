<?php



/**
 * Defines the button that saves a record
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class BetterButton_Save extends BetterButton {


    /**
     * Builds the button
     * @param Form                            $form    The form that holds the button
     * @param GridFieldDetailForm_ItemRequest $request The request that points to the form
     */
    public function __construct(Form $form, GridFieldDetailForm_ItemRequest $request) {
        parent::__construct('save',_t('GridFieldDetailForm.SAVE', 'Save'), $form, $request);
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
     * Adds the appropriate icon and style
     * @return FormAction
     */
    public function transformToButton() {
        return parent::transformToButton()
            ->addExtraClass('ss-ui-action-constructive')
            ->setAttribute('data-icon','accept');
    }
}
