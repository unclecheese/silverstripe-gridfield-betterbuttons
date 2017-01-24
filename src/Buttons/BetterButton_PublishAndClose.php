<?php

/**
 * Defines the button that publishes a record and closes the detail form
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class BetterButton_PublishAndClose extends BetterButton_SaveAndClose implements BetterButton_Versioned
{
    /**
     * Builds the button
     */
    public function __construct()
    {
        return parent::__construct('doPublishAndQuit', _t('GridFieldDetailForm.PUBLISHANDQUITE', 'Publish and close'));
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
}
