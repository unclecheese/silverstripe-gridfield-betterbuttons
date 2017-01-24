<?php

namespace UncleCheese\BetterButtons\Buttons;

use UncleCheese\BetterButtons\Buttons\BetterButton;
use UncleCheese\BetterButtons\Interfaces\BetterButton_Versioned;

/**
 * Defines the button that saves a draft
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class BetterButton_SaveDraft extends BetterButton implements BetterButton_Versioned
{
    /**
     * Builds the button
     */
    public function __construct()
    {
        parent::__construct('save', _t('SiteTree.BUTTONSAVED', 'Saved'));
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
     * Updates the button to have the correct style and icon
     * @return FormAction
     */
    public function baseTransform()
    {
        parent::baseTransform();

        return $this
            ->setAttribute('data-icon', 'accept')
            ->setAttribute('data-icon-alternate', 'addpage')
            ->setAttribute('data-text-alternate', _t('CMSMain.SAVEDRAFT', 'Save draft'));
    }

    /**
     * Update the UI to reflect unsaved state
     * @return void
     */
    public function transformToButton()
    {
        parent::transformToButton();

        if ($this->gridFieldRequest->recordIsDeletedFromStage()) {
            $this->addExtraClass('ss-ui-alternate');
        }

        return $this;
    }
}
