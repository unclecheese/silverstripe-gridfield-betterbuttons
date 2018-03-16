<?php

namespace UncleCheese\BetterButtons\Buttons;

use UncleCheese\BetterButtons\Interfaces\BetterButtonVersioned;
use SilverStripe\Forms\GridField\GridFieldDetailForm_ItemRequest;
use UncleCheese\BetterButtons\Extensions\ItemRequest;
use SilverStripe\Versioned\Versioned;
use SilverStripe\ORM\DataObject;

/**
 * Defines the button that publishes a record that uses the {@link Versioned} extension
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class Publish extends Button implements BetterButtonVersioned
{
    /**
     * Builds the button
     */
    public function __construct()
    {
        parent::__construct('publish', _t('SiteTree.BUTTONSAVEPUBLISH', 'Save & publish'));
    }

    /**
     * Determines if the button should display
     * @return boolean
     */
    public function shouldDisplay()
    {
        $record = $this->gridFieldRequest->getRecord();

        return $record->canEdit();
    }

    /**
     * Updates the button to use appropriate icons
     * @return Publish
     */
    public function baseTransform()
    {
        parent::baseTransform();
        return $this
            ->addExtraClass('btn-secondary-outline font-icon-check-mark')
            ->setAttribute('data-btn-alternate', 'btn action btn-primary font-icon-rocket')
            ->setAttribute('data-text-alternate', _t('SiteTree.BUTTONSAVEPUBLISH', 'Save & publish'));
    }

    /**
     * Update the UI to reflect published state
     * @return Publish
     */
    public function transformToButton()
    {
        parent::transformToButton();

        /* @var GridFieldDetailForm_ItemRequest|ItemRequest $gridFieldRequest */
        $gridFieldRequest = $this->gridFieldRequest;

        if ($gridFieldRequest->recordIsPublished()) {
            $this->setTitle(_t('SiteTree.BUTTONPUBLISHED', 'Published'));
        }
        /* @var DataObject|Versioned $record */
        $record = $gridFieldRequest->getRecord();
        if ($record->stagesDiffer()
            && $gridFieldRequest->recordIsDeletedFromStage()
        ) {
            $this->addExtraClass('ss-ui-alternate');
        }

        return $this;
    }
}
