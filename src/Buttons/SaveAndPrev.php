<?php

namespace UncleCheese\BetterButtons\Buttons;

use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\Forms\GridField\GridFieldDetailForm_ItemRequest;
use UncleCheese\BetterButtons\Controllers\ItemRequest;

/**
 * Defines the button that saves a record and goes to the previous one
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class SaveAndPrev extends Button
{
    /**
     * Builds the button
     */
    public function __construct()
    {
        parent::__construct("doSaveAndPrev", _t(GridFieldDetailForm::class . '.SAVEANDPREV', 'Save and go to previous record'));
    }

    /**
     * Determines if the button should show
     * @return boolean
     */
    public function shouldDisplay()
    {
        $record = $this->getGridFieldRequest()->getRecord();

        return $record->canEdit();
    }

    /**
     * Updates the button to be disabled if there is no previous record
     * @return FormAction
     */
    public function baseTransform()
    {
        parent::baseTransform();
        /* @var GridFieldDetailForm_ItemRequest|ItemRequest $gridFieldRequest */
        $gridFieldRequest = $this->getGridFieldRequest();

        $disabled = (!$gridFieldRequest->getPreviousRecord());

        return $this->setDisabled($disabled);
    }

    /**
     * Adds a class to help identify the button in a group
     * @return FormAction
     */
    public function transformToInput()
    {
        return parent::transformToInput()
            ->addExtraClass("saveAndGoPrev");
    }
}
