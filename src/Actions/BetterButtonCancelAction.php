<?php

/**
 * Defines a button that cancels out of the form
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class BetterButtonCancelAction extends BetterButtonAction
{
    /**
     * Builds the button
     */
    public function __construct()
    {
        parent::__construct(_t('GridFieldBetterButtons.CANCEL', 'Cancel'));
    }

    /**
     * Gets the link for the button
     * @return string
     */
    public function getButtonLink()
    {
        return $this->gridFieldRequest->Link("cancel");
    }

    /**
     * Gets the HTML that represents the button
     * @return string
     */
    public function getButtonHTML()
    {
        $this->addExtraClass("backlink");

        return parent::getButtonHTML();
    }
}
