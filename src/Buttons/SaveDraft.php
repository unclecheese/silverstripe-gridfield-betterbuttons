<?php

namespace UncleCheese\BetterButtons\Buttons;

use SilverStripe\Forms\FormAction;
use UncleCheese\BetterButtons\Interfaces\BetterButtonVersioned;
use SilverStripe\Forms\GridField\GridFieldDetailForm_ItemRequest;
use UncleCheese\BetterButtons\Extensions\ItemRequest;

/**
 * Defines the button that saves a draft
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class SaveDraft extends Button implements BetterButtonVersioned
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
        $record = $this->gridFieldRequest->getRecord();

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
            ->addExtraClass('btn-secondary-outline font-icon-check-mark')
            ->setAttribute('data-btn-alternate', 'btn action btn-primary font-icon-save')
            ->setAttribute('data-text-alternate', _t('CMSMain.SAVEDRAFT', 'Save draft'));
    }

    /**
     * Update the UI to reflect unsaved state
     * @return FormAction
     */
    public function transformToButton()
    {
        parent::transformToButton();
        /* @var GridFieldDetailForm_ItemRequest|ItemRequest $gridFieldRequest */
        $gridFieldRequest = $this->gridFieldRequest;
        if ($gridFieldRequest->recordIsDeletedFromStage()) {
            $this->addExtraClass('ss-ui-alternate');
        }

        return $this;
    }
}
