<?php

namespace UncleCheese\BetterButtons\Controllers;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridFieldDetailForm_ItemRequest;
use SilverStripe\ORM\DataObject;
use SilverStripe\View\Requirements;
use SilverStripe\Forms\CompositeField;
use UncleCheese\BetterButtons\Extensions\BetterButtons;
use UncleCheese\BetterButtons\Traits\BetterButtonsItemRequest;

/**
 * Decorates {@link GridDetailForm_ItemRequest} to use new form actions and buttons.
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class ItemRequest extends GridFieldDetailForm_ItemRequest
{
    use BetterButtonsItemRequest;

    /**
     * @var array Allowed controller actions
     */
    private static $allowed_actions = [
        'addnew',
        'edit',
        'save',
        'cancel',
        'ItemEditForm',
        'doNew',
        'doSaveAndAdd',
        'doSaveAndQuit',
        'doSaveAndNext',
        'doSaveAndPrev',
        'doDelete',
        'customaction',
        'nestedform',
    ];

    /**
     *
     * /**
     * Updates the detail form to include new form actions and buttons
     * @return FieldList
     */
    protected function getFormActions()
    {
        /* @var DataObject|BetterButtons $record */
        $record = $this->getRecord();
        if ($record->config()->get('better_buttons_enabled') !== true) {
            return parent::getFormActions();
        }

        Requirements::css('unclecheese/betterbuttons:css/gridfield_betterbuttons.css');
        Requirements::javascript('unclecheese/betterbuttons:javascript/gridfield_betterbuttons.js');

        $actions = $this->filterFieldList($record->getBetterButtonsActions($this));
        $utils = $record->getBetterButtonsUtils();

        $actions->push(
            CompositeField::create(
                $this->filterFieldList($utils)
            )->addExtraClass('better-buttons-utils')
        );

        return $actions;
    }

}
