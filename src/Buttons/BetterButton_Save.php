<?php

namespace UncleCheese\BetterButtons\Buttons;

use UncleCheese\BetterButtons\Buttons\BetterButton;

/**
 * Defines the button that saves a record
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class BetterButton_Save extends BetterButton
{
    /**
     * Builds the button
     */
    public function __construct()
    {
        parent::__construct('save', _t('GridFieldDetailForm.SAVE', 'Save'));
    }

    /**
     * Determines if the button should show
     * @return boolean
     */
    public function shouldDisplay()
    {
        $record = $this->gridFieldRequest->record;

        return $record->canEdit();
    }

    /**
     * Adds the appropriate icon and style
     * @return FormAction
     */
    public function transformToButton()
    {
        return parent::transformToButton()
            ->addExtraClass('btn-primary font-icon-save')
            ->setAttribute('data-icon', 'accept');
    }
}
