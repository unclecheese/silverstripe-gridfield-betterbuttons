<?php

namespace UncleCheese\BetterButtons\Buttons;

use SilverStripe\Forms\FormAction;
use UncleCheese\BetterButtons\Interfaces\BetterButtonVersioned;

/**
 * Defines the button that rolls back the version of the record
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class Rollback extends Button implements BetterButtonVersioned
{
    /**
     * Builds the button
     */
    public function __construct()
    {
        parent::__construct('rollback', _t('SiteTree.BUTTONCANCELDRAFT', 'Cancel draft changes'));
    }

    /**
     * Update the button to show a description
     * @return FormAction
     */
    public function baseTransform()
    {
        parent::baseTransform();

        return $this->setDescription(_t(
            'SiteTree.BUTTONCANCELDRAFTDESC',
            'Delete your draft and revert to the currently published page'
        ));
    }

    /**
     * Determines if the button should display
     * @return boolean
     */
    public function shouldDisplay()
    {
        return $this->gridFieldRequest->record->stagesDiffer('Stage', 'Live')
            && $this->gridFieldRequest->recordIsPublished()
            && $this->gridFieldRequest->record->canEdit();
    }
}
