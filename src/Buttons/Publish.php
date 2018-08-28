<?php

namespace UncleCheese\BetterButtons\Buttons;

use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\GridField\GridFieldDetailForm;

/**
 * Defines the button that saves a record
 *
 * @author   Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class Publish extends Button
{
    /**
     * Builds the button
     */
    public function __construct()
    {
        parent::__construct('doPublish', _t('SilverStripe\Versioned\VersionedGridFieldItemRequest.BUTTONPUBLISH', 'Publish'));
    }

    /**
     * Determines if the button should show
     *
     * @return boolean
     */
    public function shouldDisplay()
    {
        $record = $this->getGridFieldRequest()->getRecord();

        return $record->canEdit();
    }

    /**
     * Adds the appropriate icon and style
     *
     * @return FormAction
     */
    public function transformToButton()
    {
        return parent::transformToButton()
            ->addExtraClass('btn-primary font-icon-save')
            ->setAttribute('data-icon', 'accept');
    }
}
