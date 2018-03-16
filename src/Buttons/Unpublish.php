<?php

namespace UncleCheese\BetterButtons\Buttons;

use SilverStripe\Forms\FormAction;
use UncleCheese\BetterButtons\Interfaces\BetterButtonVersioned;
use SilverStripe\Forms\GridField\GridFieldDetailForm_ItemRequest;
use UncleCheese\BetterButtons\Extensions\ItemRequest;

/**
 * Defines the button that unpublishes a record
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class Unpublish extends Button implements BetterButtonVersioned
{
    /**
     * Builds the button
     */
    public function __construct()
    {
        parent::__construct('unpublish', _t('SiteTree.BUTTONUNPUBLISH', 'Unpublish'));
    }

    /**
     * Adds a class to identify this as a destructive action
     * @return FormAction
     */
    public function baseTransform()
    {
        parent::baseTransform();

        return $this->addExtraClass('ss-ui-action-destructive');
    }

    /**
     * Determines if the button should show
     * @return boolean
     */
    public function shouldDisplay()
    {
        /* @var GridFieldDetailForm_ItemRequest|ItemRequest $gridFieldRequest */
        $gridFieldRequest = $this->gridFieldRequest;

        return $gridFieldRequest->recordIsPublished() && $gridFieldRequest->getRecord()->canEdit();
    }
}
