<?php

namespace UncleCheese\BetterButtons\Buttons;

use UncleCheese\BetterButtons\Buttons\BetterButton;
use UncleCheese\BetterButtons\Interfaces\BetterButton_Versioned;

/**
 * Defines the button that publishes a record that uses the {@link Versioned} extension
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class BetterButton_Publish extends BetterButton implements BetterButton_Versioned
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
        $record = $this->gridFieldRequest->record;

        return $record->canEdit();
    }

    /**
     * Updates the button to use appropriate icons
     * @return FormAction
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
     * @return void
     */
    public function transformToButton()
    {
        parent::transformToButton();

        if ($this->gridFieldRequest->recordIsPublished()) {
            $this->setTitle(_t('SiteTree.BUTTONPUBLISHED', 'Published'));
        }

        if ($this->gridFieldRequest->record->stagesDiffer('Stage', 'Live')
            && $this->gridFieldRequest->recordIsDeletedFromStage()
        ) {
            $this->addExtraClass('ss-ui-alternate');
        }

        return $this;
    }
}
