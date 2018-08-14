<?php

namespace UncleCheese\BetterButtons\Buttons;

use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\ORM\DataObject;
use SilverStripe\Versioned\Versioned;
use SilverStripe\View\Requirements;

/**
 * Defines the button that archives a record, with confirmation
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class Archive extends Button
{
    /**
     * Builds the button
     */
    public function __construct()
    {
        parent::__construct('doArchive', _t(GridFieldDetailForm::class . '.Archive', 'Archive'));
    }

    /**
     * Adds the JS, sets up necessary HTML attributes
     * @return Archive
     */
    public function baseTransform()
    {
        parent::baseTransform();

        return $this
            ->setUseButtonTag(true)
            ->addExtraClass('btn-outline-danger btn-hide-outline font-icon-trash-bin gridfield-better-buttons-archive');
    }

    /**
     * Determines if the button should show
     * @return boolean
     */
    public function shouldDisplay()
    {
        /* @var DataObject|Versioned $record */
        $record = $this->getGridFieldRequest()->getRecord();

        if(!$record->hasExtension(Versioned::class)){
            return $record->canDelete();
        }

        return !$record->isPublished() && $record->canDelete();
    }
}
