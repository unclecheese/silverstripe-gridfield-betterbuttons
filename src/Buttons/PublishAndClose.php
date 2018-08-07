<?php

namespace UncleCheese\BetterButtons\Buttons;

use SilverStripe\Forms\GridField\GridFieldDetailForm;
use UncleCheese\BetterButtons\Interfaces\BetterButtonVersioned;
use UncleCheese\BetterButtons\Traits\SaveAndCloseTransforms;

/**
 * Defines the button that publishes a record and closes the detail form
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class PublishAndClose extends Button implements BetterButtonVersioned
{
    use SaveAndCloseTransforms;

    /**
     * Builds the button
     */
    public function __construct()
    {
        return parent::__construct('doPublishAndQuit', _t(GridFieldDetailForm::class . '.PUBLISHANDQUITE', 'Publish and close'));
    }

    /**
     * Determines if the button should display
     * @return boolean
     */
    public function shouldDisplay()
    {
        $record = $this->getGridFieldRequest()->record;

        return $record->canEdit();
    }
}
