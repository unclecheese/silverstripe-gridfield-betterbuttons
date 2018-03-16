<?php

namespace UncleCheese\BetterButtons\Buttons;

use SilverStripe\Forms\FormAction;
use UncleCheese\BetterButtons\Traits\SaveAndAddTransforms;

/**
 * Defines the button that save a record and redirects to creating a new one
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class SaveAndAdd extends Button
{
    use SaveAndAddTransforms;

    /**
     * Builds the button
     */
    public function __construct()
    {
        parent::__construct("doSaveAndAdd", _t('GridFieldBetterButtons.SAVEANDADDNEW', 'Save and add new'));
    }

    /**
     * Determines if the record should show
     * @return boolean
     */
    public function shouldDisplay()
    {
        $record = $this->gridFieldRequest->record;

        return $record->canEdit() && $record->canCreate();
    }

}
