<?php

namespace UncleCheese\BetterButtons\Buttons;

use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\ORM\DataObject;
use SilverStripe\Versioned\Versioned;
use SilverStripe\View\Requirements;

/**
 * Defines the button that deletes a record, with confirmation
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class Delete extends Button
{
    /**
     * Builds the button
     */
    public function __construct()
    {
        parent::__construct('doDelete', _t(GridFieldDetailForm::class . '.Delete', 'Delete'));
    }

    /**
     * Adds the JS, sets up necessary HTML attributes
     * @return Delete
     */
    public function baseTransform()
    {
        parent::baseTransform();
        Requirements::javascript('unclecheese/betterbuttons:javascript/gridfield_betterbuttons_delete.js');

        return $this
            ->setUseButtonTag(true)
            ->addExtraClass('btn-outline-danger btn-hide-outline font-icon-trash-bin gridfield-better-buttons-delete')
            ->setAttribute("data-toggletext", _t('GridFieldBetterButtons.AREYOUSURE', 'Yes. Delete this item.'))
            ->setAttribute("data-confirmtext", _t('GridFieldDetailForm.CANCELDELETE', 'No. Don\'t delete.'));
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
