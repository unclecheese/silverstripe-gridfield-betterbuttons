<?php

namespace UncleCheese\BetterButtons\Buttons;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\FormAction;
use SilverStripe\ORM\DataObject;
use SilverStripe\Versioned\Versioned;
use UncleCheese\BetterButtons\Interfaces\BetterButtonVersioned;

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
        parent::__construct('unpublish', _t(SiteTree::class . '.BUTTONUNPUBLISH', 'Unpublish'));
    }

    /**
     * Adds a class to identify this as a destructive action
     * @return FormAction
     */
    public function baseTransform()
    {
        parent::baseTransform();

        return $this->addExtraClass('btn-outline-danger');
    }

    /**
     * Determines if the button should show
     * @return boolean
     */
    public function shouldDisplay()
    {
        /* @var DataObject|Versioned $record */
        $record = $this->getGridFieldRequest()->getRecord();

        return $record->isPublished() && $record->canEdit();
    }
}
