<?php

/**
 * Defines a button that cancels out of the form
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class BetterButtonCancelAction extends BetterButtonAction {


    /**
     * Builds the button
     * @param Form                            $form    The form that holds the button
     * @param GridFieldDetailForm_ItemRequest $request The request that points to the form
     */
    public function __construct(Form $form, GridFieldDetailForm_ItemRequest $request) {
        parent::__construct(
            _t('GridFieldBetterButtons.CANCEL','Cancel'),
            $form, 
            $request
        );
    }


    /**
     * Gets the link for the button
     * @return string
     */
    public function getButtonLink() {
        return $this->gridFieldRequest->Link("cancel");
    }


    /**
     * Gets the HTML that represents the button
     * @return string
     */
    public function getButtonHTML() {
        $this->addExtraClass("backlink");

        return parent::getButtonHTML($form, $request);
    }
}
