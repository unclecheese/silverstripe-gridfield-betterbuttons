<?php


/**
 * Defines the button that publishes a record that uses the {@link Versioned} extension
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class BetterButton_Publish extends BetterButton implements BetterButton_Versioned {


    /**
     * Builds the button
     */
    public function __construct() {
        parent::__construct('publish',_t('SiteTree.BUTTONSAVEPUBLISH', 'Save & publish'));
    }


    /**
     * Determines if the button should display
     * @return boolean
     */
    public function shouldDisplay() {
        $record = $this->gridFieldRequest->record;
        
        return $record->canEdit();
    }


    /**
     * Updates the button to use appropriate icons
     * @return FormAction
     */
    public function baseTransform() {
        parent::baseTransform();
        return $this
            ->setAttribute('data-icon', 'accept')
            ->setAttribute('data-icon-alternate', 'disk')
            ->setAttribute('data-text-alternate', _t('SiteTree.BUTTONSAVEPUBLISH', 'Save & publish'));

    }


    /**
     * Update the UI to reflect published state
     * @return void
     */
    public function transformToButton() {
        parent::transformToButton();
        
        $published = $this->gridFieldRequest->recordIsPublished();
        if($published) {
            $this->setTitle(_t('SiteTree.BUTTONPUBLISHED', 'Published'));
        }
        if($this->gridFieldRequest->record->stagesDiffer('Stage','Live') && $published) {
            $this->addExtraClass('ss-ui-alternate');
        }

        return $this;
    }
}