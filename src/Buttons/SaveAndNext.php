<?php

namespace UncleCheese\BetterButtons\Buttons;

use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\GridField\GridFieldDetailForm_ItemRequest;
use UncleCheese\BetterButtons\Controllers\ItemRequest;

/**
 * Defines the button that saves a record and goes to the next one
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class SaveAndNext extends Button
{
    /**
     * Builds the button
     */
    public function __construct()
    {
        parent::__construct("doSaveAndNext", _t('GridFieldDetailForm.SAVEANDNEXT', 'Save and go to next record'));
    }

    /**
     * Determines if the button should display
     * @return boolean
     */
    public function shouldDisplay()
    {
        $record = $this->getGridFieldRequest()->getRecord();

        return $record->canEdit();
    }

    /**
     * Disables the button if there is no next record
     * @return FormAction
     */
    public function baseTransform()
    {
        parent::baseTransform();
        /* @var GridFieldDetailForm_ItemRequest|ItemRequest $gridFieldRequest */
        $gridFieldRequest = $this->getGridFieldRequest();
        $disabled = (!$gridFieldRequest->getNextRecord());

        return $this->setDisabled($disabled);
    }

    /**
     * Adds a class that helps identify this button in a group
     * @return FormAction
     */
    public function transformToInput()
    {
        return parent::transformToInput()
            ->addExtraClass("saveAndGoNext");
    }
}
