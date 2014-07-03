<?php


/**
 * Defines the button that rolls back the version of the record
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class BetterButton_Rollback extends BetterButton implements BetterButton_Versioned {


    /**
     * Builds the button
     * @param Form                            $form    The form that holds the button
     * @param GridFieldDetailForm_ItemRequest $request The request that points to the form
     */
    public function __construct(Form $form, GridFieldDetailForm_ItemRequest $request) {
        parent::__construct('rollback', _t('SiteTree.BUTTONCANCELDRAFT','Cancel draft changes'), $form, $request);
    }


    /**
     * Update the button to show a description
     * @return [type] [description]
     */
    public function baseTransform() {
        parent::baseTransform();
        $this
            ->setDescription(_t(
                            'SiteTree.BUTTONCANCELDRAFTDESC',
                            'Delete your draft and revert to the currently published page'
            ));
    }

    
    /**
     * Determines if the button should display
     * @return boolean
     */
    public function shouldDisplay() {
        return $this->gridFieldRequest->record->stagesDiffer('Stage','Live') && 
               $this->gridFieldRequest->recordIsPublished() && 
               $this->gridFieldRequest->record->canEdit();
    }
}
