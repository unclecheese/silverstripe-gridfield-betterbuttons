<?php

namespace UncleCheese\BetterButtons\Buttons;

use SilverStripe\Forms\Form;
use SilverStripe\Forms\GridField\GridFieldDetailForm_ItemRequest;
use UncleCheese\BetterButtons\Interfaces\BetterButtonVersioned;
use UncleCheese\BetterButtons\Traits\SaveAndAddTransforms;

/**
 * Defines the button that publishes a record, then proceeds to create a new one
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class PublishAndAdd extends Button implements BetterButtonVersioned
{
    use SaveAndAddTransforms;

    /**
     * @param Form $form
     * @param GridFieldDetailForm_ItemRequest $request
     *
     * Builds the button
     */
    public function __construct(Form $form, GridFieldDetailForm_ItemRequest $request)
    {
        parent::__construct('doPublishAndAdd', _t('GridFieldDetailForm.PUBLISHANDADD', 'Publish and add new'));
    }

    /**
     * Determines if the button should display
     * @return boolean
     */
    public function shouldDisplay()
    {
        $record = $this->gridFieldRequest->record;

        return $record->canEdit() && $record->canCreate();
    }
}
