<?php

namespace UncleCheese\BetterButtons\Buttons;

use SilverStripe\View\Requirements;
use UncleCheese\BetterButtons\Buttons\BetterButton;

/**
 * Defines the button that deletes a record, with confirmation
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class BetterButton_Delete extends BetterButton
{
    /**
     * Builds the button
     */
    public function __construct()
    {
        parent::__construct('doDelete', _t('GridFieldDetailForm.Delete', 'Delete'));
    }

    /**
     * Adds the JS, sets up necessary HTML attributes
     * @return FormAction
     */
    public function baseTransform()
    {
        parent::baseTransform();
        Requirements::javascript(BETTER_BUTTONS_DIR.'/javascript/gridfield_betterbuttons_delete.js');

        return $this
            ->setUseButtonTag(true)
            ->addExtraClass('gridfield-better-buttons-delete')
            ->setAttribute("data-toggletext", _t('GridFieldBetterButtons.AREYOUSURE', 'Yes. Delete this item.'))
            ->setAttribute("data-confirmtext", _t('GridFieldDetailForm.CANCELDELETE', 'No. Don\'t delete.'));
    }

    /**
     * Determines if the button should show
     * @return boolean
     */
    public function shouldDisplay()
    {
        return !$this->gridFieldRequest->recordIsPublished() && $this->gridFieldRequest->record->canDelete();
    }
}
