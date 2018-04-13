<?php

namespace UncleCheese\BetterButtons\Actions;

/**
 * Defines a button that cancels out of the form
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class Cancel extends Action
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
        return $this->getGridFieldRequest()->Link("cancel");
    }

    /**
     * Gets the HTML that represents the button
     * @return string
     */
    public function getButtonHTML()
    {
        $this->addExtraClass("btn btn-default backlink");

        return parent::getButtonHTML();
    }
}
