<?php

namespace UncleCheese\BetterButtons\Buttons;

/**
 * Defines the button that creates a new record
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class Create extends Button
{
    /**
     * Builds the button
     */
    public function __construct()
    {
        parent::__construct("doNew", '+');
    }

    /**
     * Add the necessary classes and icons
     * @return Create
     */
    public function baseTransform()
    {
        parent::baseTransform();

        return $this->addExtraClass("btn-primary ss-ui-action-constructive better-button-add")
            ->setAttribute('title', _t('GridFieldBetterButtons.NEWRECORD', 'New record'));
    }

    /**
     * Determines if the button should show
     * @return boolean
     */
    public function shouldDisplay()
    {
        // Do not show create new within create new
        if ($this->getGridFieldRequest()->getRequest()->param('ID') == 'new') {
            return false;
        }
        return $this->getGridFieldRequest()->getRecord()->canCreate();
    }
}
