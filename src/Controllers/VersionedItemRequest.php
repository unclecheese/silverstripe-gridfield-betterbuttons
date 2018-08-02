<?php

namespace UncleCheese\BetterButtons\Controllers;

use SilverStripe\CMS\Controllers\CMSMain;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Control\HTTPResponse_Exception;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\Versioned\RecursivePublishable;
use SilverStripe\Versioned\Versioned;
use SilverStripe\Versioned\VersionedGridFieldItemRequest;
use SilverStripe\View\Requirements;
use SilverStripe\View\ViewableData_Customised;
use SilverStripe\Forms\CompositeField;
use UncleCheese\BetterButtons\Extensions\BetterButtons;
use UncleCheese\BetterButtons\Traits\BetterButtonsItemRequest;

/**
 * Decorates {@link VersionedGridFieldItemRequest} to use new form actions and buttons.
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 *
 */
class VersionedItemRequest extends VersionedGridFieldItemRequest
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
     * Updates the detail form to include new form actions and buttons
     * @return FieldList
     */
    protected function getFormActions()
    {
        $actions = parent::getFormActions();
        /* @var DataObject|BetterButtons $record */
        $record = $this->getRecord();
        if ($record->config()->get('better_buttons_enabled') !== true) {
            return $actions;
        }

        Requirements::css('unclecheese/betterbuttons:css/gridfield_betterbuttons.css');
        Requirements::javascript('unclecheese/betterbuttons:javascript/gridfield_betterbuttons.js');

        $newButtons = $this->filterFieldList($record->getBetterButtonsActions());

        if ($record->hasMethod("replaceDefaultButtons") &&
            $record->replaceDefaultButtons() === true) {
            $actions = $newButtons;
        } else {
            $actions->merge($newButtons);
        }

        $utils = $record->getBetterButtonsUtils();
        $actions->push(
            CompositeField::create(
                $this->filterFieldList($utils)
            )->addExtraClass('better-buttons-utils')
        );

        return $actions;
    }

    /**
     * Publishes the record and goes to make a new record
     * @param  array $data The form data
     * @param  Form $form The Form object
     * @return HTTPResponse
     */
    public function doPublishAndAdd($data, $form)
    {
        parent::doPublish($data, $form);
        return $this->addnew(
            $this->getToplevelController()->getRequest()
        );
    }

    /**
     * Publishes the record and closes the detail form
     * @param  array $data The form data
     * @param  Form $form The Form object
     * @return HTTPResponse
     */
    public function doPublishAndClose($data, $form)
    {
        parent::doPublish($data, $form);

        return $this->returnToList();
    }

    /**
     * Unpublishes the record
     *
     * @param array $data
     * @param Form $form
     * @return DBHTMLText|ViewableData_Customised
     * @throws HTTPResponse_Exception
     */
    public function unpublish(array $data, Form $form)
    {
        /* @var DataObject|BetterButtons|RecursivePublishable|Versioned $record */
        $record = $this->getRecord();
        $controller = $this->getToplevelController();

        if ($record && !$record->canUnpublish()) {
            return $controller->httpError(403);
        }
        if (!$record || !$record->ID) {
            throw new HTTPResponse_Exception("Bad record ID #" . (int)$data['ID'], 404);
        }

        $record->doUnpublish();
        $message = _t(
            CMSMain::class . '.REMOVEDPAGE',
            "Removed '{title}' from the published site",
            ['title' => $record->Title]
        );

        $form->sessionMessage($message, 'good');

        return $this->edit($controller->getRequest());
    }

    /**
     * @param  array $data
     * @param  Form $form
     * @return DBHTMLText|ViewableData_Customised
     */
    public function rollback(array $data, Form $form)
    {
        $id = $this->getRecord()->ID;
        $controller = $this->getToplevelController();
        $this->extend('onBeforeRollback', $id);

        /** @var DataObject|Versioned $record */
        $record = Versioned::get_latest_version(get_class($this->getRecord()), $id);
        if ($record && !$record->canEdit()) {
            return $controller->httpError(403);
        }

        $record->doRevertToLive();
        $message = _t(
            CMSMain::class . '.ROLLEDBACKPUBv2',
            "Rolled back to published version."
        );

        $form->sessionMessage($message, 'good');

        return $this->edit($controller->getRequest());
    }
}