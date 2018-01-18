<?php

namespace UncleCheese\BetterButtons\Extensions;

use Exception;
use SilverStripe\Admin\LeftAndMain;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\PjaxResponseNegotiator;
use SilverStripe\Core\Convert;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\GridField\GridFieldDetailForm_ItemRequest;
use SilverStripe\Forms\TabSet;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\ManyManyList;
use SilverStripe\ORM\ValidationException;
use SilverStripe\Versioned\Versioned;
use SilverStripe\View\Requirements;
use UncleCheese\BetterButtons\Controllers\BetterButtonsCustomActionRequest;
use UncleCheese\BetterButtons\Controllers\BetterButtonsNestedFormRequest;
use UncleCheese\BetterButtons\Interfaces\BetterButtonInterface;
use UncleCheese\BetterButtons\Interfaces\BetterButton_Versioned;

/**
 * Decorates {@link GridDetailForm_ItemRequest} to use new form actions and buttons.
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class GridFieldBetterButtonsItemRequest extends DataExtension
{
    /**
     * @var array Allowed controller actions
     */
    private static $allowed_actions = array(
        'addnew',
        'edit',
        'save',
        'cancel',
        'publish',
        'rollback',
        'unpublish',
        'ItemEditForm',
        'doNew',
        'doSaveAndAdd',
        'doSaveAndQuit',
        'doPublishAndAdd',
        'doPublishAndClose',
        'doSaveAndNext',
        'doSaveAndPrev',
        'doDelete',
        'customaction',
        'nestedform',
    );

    /**
     * Handles all custom action from DataObjects and hands them off to a sub-controller.
     * e.g. /customaction/mymethodname
     *
     * Can't handle the actions here because the url_param '$Action!' gets matched, and we don't
     * get to read anything after /customaction/
     *
     * @param  HTTPRequest $r
     * @return BetterButtonsCustomActionRequest
     */
    public function customaction(HTTPRequest $r)
    {
        $req = new BetterButtonsCustomActionRequest($this, $this->owner, $this->owner->ItemEditForm());

        return $req->handleRequest($r);
    }

    /**
     * Handles all custom action from DataObjects and hands them off to a sub-controller.
     * e.g. /nestedform?action=myDataObjectAction
     *
     * @param  HTTPRequest $r
     * @return BetterButtonsNestedFormRequest
     */
    public function nestedform(HTTPRequest $r)
    {
        $req = new BetterButtonsNestedFormRequest($this, $this->owner, $this->owner->ItemEditForm());

        return $req->handleRequest($r);
    }

    /**
     * Redirecting to the current URL doesn't do anything, so this is just a dummy action
     * that gives the request somewhere to go in order to force a reload, and then just
     * redirects back to the original link.
     *
     * @param HTTPRequest The request object
     */
    public function addnew(HTTPRequest $r)
    {
        return Controller::curr()->redirect(Controller::join_links($this->owner->gridField->Link("item"), "new"));
    }

    /**
     * Updates the detail form to include new form actions and buttons
     *
     * @param Form The ItemEditForm object
     */
    public function updateItemEditForm($form)
    {
        if ($this->owner->record->stat('better_buttons_enabled') !== true) {
            return false;
        }
        Requirements::css(BETTER_BUTTONS_DIR.'/css/gridfield_betterbuttons.css');
        Requirements::javascript(BETTER_BUTTONS_DIR.'/javascript/gridfield_betterbuttons.js');


        $actions = $this->owner->record->getBetterButtonsActions();
        $form->setActions($this->filterFieldList($form, $actions));

        if ($form->Fields()->hasTabSet()) {
            $form->Fields()->findOrMakeTab('Root')->setTemplate(TabSet::class);
            $form->addExtraClass('cms-tabset');
        }

        $utils = $this->owner->record->getBetterButtonsUtils();
        $form->Utils = $this->filterFieldList($form, $utils);
        $form->setTemplate([
            'type' => 'Includes',
            'BetterButtons_EditForm',
        ]);
        $form->addExtraClass('better-buttons-form');
    }

    /**
     * Given a list of actions, remove anything that doesn't belong.
     * @param  Form      $form
     * @param  FieldList $actions
     * @return FieldList
     */
    protected function filterFieldList(Form $form, FieldList $actions)
    {
        $list = FieldList::create();

        foreach ($actions as $a) {
            if (!$a instanceof BetterButtonInterface) {
                throw new Exception("{$buttonObj->class} must implement BetterButtonInterface");
            }

            $a->bindGridField($form, $this->owner);

            if (!$a->shouldDisplay()) {
                continue;
            }

            if (($a instanceof BetterButton_Versioned) && !$this->owner->record->checkVersioned()) {
                continue;
            }

            $list->push($a);
        }

        return $list;
    }

    /**
     * Saves the form and forwards to a blank form to continue creating
     *
     * @param array The form data
     * @param Form The form object
     */
    public function doSaveAndAdd($data, $form)
    {
        return $this->saveAndRedirect($data, $form, $this->owner->Link("addnew"));
    }

    /**
     * Saves the form and goes back to list view
     *
     *
     * @param array The form data
     * @param Form The form object
     */
    public function doSaveAndQuit($data, $form)
    {
        Controller::curr()->getResponse()->addHeader("X-Pjax", "Content");
        return $this->saveAndRedirect($data, $form, $this->getBackLink());
    }

    /**
     * Publishes the record and goes to make a new record
     * @param  array $data The form data
     * @param  Form $form The Form object
     * @return HTTPResponse
     */
    public function doPublishAndAdd($data, $form)
    {
        return $this->publish($data, $form, $this->owner, $this->owner->Link('addnew'));
    }

    /**
     * Publishes the record and closes the detail form
     * @param  array $data The form data
     * @param  Form $form The Form object
     * @return HTTPResponse
     */
    public function doPublishAndClose($data, $form)
    {
        Controller::curr()->getResponse()->addHeader("X-Pjax", "Content");
        return $this->publish($data, $form, $this->owner, $this->getBackLink());
    }

    /**
     * Goes back to list view
     *
     * @param array The form data
     * @param Form The form object
     */
    public function cancel()
    {
        Controller::curr()->getResponse()->addHeader("X-Pjax", "Content");
        return Controller::curr()->redirect($this->getBackLink());
    }

    /**
     * Saves the record and goes to the next one
     * @param  arary $data The form data
     * @param  Form $form The Form object
     * @return HTTPResponse
     */
    public function doSaveAndNext($data, $form)
    {
        Controller::curr()->getResponse()->addHeader("X-Pjax", "Content");
        $link = $this->getEditLink($this->getNextRecordID());

        return $this->saveAndRedirect($data, $form, $link);
    }

    /**
     * Saves the record and goes to the previous one
     * @param  arary $data The form data
     * @param  Form $form The Form object
     * @return HTTPResponse
     */
    public function doSaveAndPrev($data, $form)
    {
        Controller::curr()->getResponse()->addHeader("X-Pjax", "Content");
        $link = $this->getEditLink($this->getPreviousRecordID());

        return $this->saveAndRedirect($data, $form, $link);
    }

    /**
     * Gets the edit link for a record
     * @param  int $id The ID of the record in the GridField
     * @return string
     */
    public function getEditLink($id)
    {
        return Controller::join_links($this->owner->gridField->Link(), "item", $id);
    }

    /**
     * Creates a new record. If you're already creating a new record,
     * this forces the URL to change. Hacky UI workaround.
     *
     * @param  arary $data The form data
     * @param  Form $form The Form object
     * @return HTTPResponse
     */
    public function doNew($data, $form)
    {
        return Controller::curr()->redirect($this->owner->Link('addnew'));
    }

    /**
     * Allows us to have our own configurable save button
     * @param  arary $data The form data
     * @param  Form $form The Form object
     * @return HTTPResponse
     */
    public function save($data, $form)
    {
        $origStage = Versioned::get_stage();
        Versioned::set_stage('Stage');
        $action = $this->owner->doSave($data, $form);
        Versioned::set_stage($origStage);

        return $action;
    }

    /**
     * @param  array       $data
     * @param  Form        $form
     * @param  HTTPRequest $request
     * @param  string      $redirectURL
     * @return HTMLText|HTTPResponse|ViewableData_Customised
     */
    public function publish($data, $form, $request = null, $redirectURL = null)
    {
        $new_record = $this->owner->record->ID == 0;
        $controller = Controller::curr();
        $list = $this->owner->gridField->getList();

        if ($list instanceof ManyManyList) {
            // Data is escaped in ManyManyList->add()
            $extraData = (isset($data['ManyMany'])) ? $data['ManyMany'] : null;
        } else {
            $extraData = null;
        }

        if (isset($data['ClassName']) && $data['ClassName'] != $this->owner->record->ClassName) {
            $newClassName = $data['ClassName'];
            // The records originally saved attribute was overwritten by $form->saveInto($record) before.
            // This is necessary for newClassInstance() to work as expected, and trigger change detection
            // on the ClassName attribute
            $this->owner->record->setClassName($this->owner->record->ClassName);
            // Replace $record with a new instance
            $this->owner->record = $this->owner->record->newClassInstance($newClassName);
        }

        if (!$this->owner->record->canEdit()) {
            return $controller->httpError(403);
        }

        try {
            $this->save($data, $form);
            $list->add($this->owner->record, $extraData);
            $this->owner->record->invokeWithExtensions('onBeforePublish', $this->owner->record);
            $this->owner->record->publish('Stage', 'Live');
            $this->owner->record->invokeWithExtensions('onAfterPublish', $this->owner->record);
        } catch (ValidationException $e) {
            $form->sessionMessage($e->getResult()->message(), 'bad');
            $responseNegotiator = new PjaxResponseNegotiator(array(
                'CurrentForm' => function () use (&$form) {
                    return $form->forTemplate();
                },
                'default'     => function () use (&$controller) {
                    return $controller->redirectBack();
                }
            ));
            if ($controller->getRequest()->isAjax()) {
                $controller->getRequest()->addHeader('X-Pjax', 'CurrentForm');
            }

            return $responseNegotiator->respond($controller->getRequest());
        }

        // TODO Save this item into the given relationship

        if ($redirectURL) {
            return $controller->redirect($redirectURL);
        }

        $title = '"' . Convert::raw2xml($this->owner->record->Title) . '"';
        $message = sprintf(
            'Published %s %s',
            $this->owner->record->i18n_singular_name(),
            $title
        );

        $form->sessionMessage($message, 'good');

        if ($new_record) {
            return Controller::curr()->redirect($this->owner->Link());
        } elseif ($this->owner->gridField->getList()->byId($this->owner->record->ID)) {
            // Return new view, as we can't do a "virtual redirect" via the CMS Ajax
            // to the same URL (it assumes that its content is already current, and doesn't reload)
            return $this->owner->edit(Controller::curr()->getRequest());
        } else {
            // Changes to the record properties might've excluded the record from
            // a filtered list, so return back to the main view if it can't be found
            $noActionURL = $controller->removeAction($data['url']);
            $controller->getRequest()->addHeader('X-Pjax', 'Content');

            return $controller->redirect($noActionURL, 302);
        }
    }

    /**
     * Unpublishes the record
     *
     * @return HTMLText|ViewableData_Customised
     */
    public function unPublish()
    {
        $origStage = Versioned::get_stage();
        Versioned::set_stage('Live');

        // This way our ID won't be unset
        $clone = clone $this->owner->record;
        $clone->delete();

        Versioned::set_stage($origStage);

        return $this->owner->edit(Controller::curr()->getRequest());
    }

    /**
     * @param  array $data
     * @param  Form  $form
     * @return HTMLText|ViewableData_Customised
     */
    public function rollback($data, $form)
    {
        if (!$this->owner->record->canEdit()) {
            return Controller::curr()->httpError(403);
        }

        $this->owner->record->doRollbackTo('Live');

        $this->owner->record = DataList::create($this->owner->record->class)->byID($this->owner->record->ID);

        $message = _t(
            'CMSMain.ROLLEDBACKPUBv2',
            "Rolled back to published version."
        );

        $form->sessionMessage($message, 'good');

        return $this->owner->edit(Controller::curr()->getRequest());
    }

    /**
     * Gets the top level controller.
     *
     * @return Controller
     * @todo  This had to be directly copied from {@link GridFieldDetailForm_ItemRequest}
     * because it is a protected method and not visible to a decorator!
     */
    protected function getToplevelController()
    {
        $c = $this->owner->getController();
        while ($c && $c instanceof GridFieldDetailForm_ItemRequest) {
            $c = $c->getController();
        }
        return $c;
    }

    /**
     * Gets the back link
     *
     * @return  string
     * @todo  This had to be directly copied from {@link GridFieldDetailForm_ItemRequest}
     * because it is a protected method and not visible to a decorator!
     */
    public function getBackLink()
    {
        // TODO Coupling with CMS
        $backlink = '';
        $toplevelController = $this->getToplevelController();
        if ($toplevelController && $toplevelController instanceof LeftAndMain) {
            if ($toplevelController->hasMethod('Backlink')) {
                $backlink = $toplevelController->Backlink();
            } elseif ($this->owner->getController()->hasMethod('Breadcrumbs')) {
                $parents = $this->owner->getController()->Breadcrumbs(false)->items;
                $backlink = array_pop($parents)->Link;
            }
        }
        if (!$backlink) {
            $backlink = $toplevelController->Link();
        }

        return $backlink;
    }

    /**
     * Oh, the horror! DRY police be advised. This function is a serious offender.
     * Saves the form data and redirects to a given link
     *
     * @param array  $data         The form data
     * @param Form   $form         The form object
     * @param string $redirectLink The redirect link
     * @todo  GridFieldDetailForm_ItemRequest::doSave is too monolithic, making overloading impossible. Most
     *        of this code is a direct copy.
     * */
    protected function saveAndRedirect($data, $form, $redirectLink)
    {
        $new_record = $this->owner->record->ID == 0;
        $controller = Controller::curr();
        $list = $this->owner->gridField->getList();

        if ($list instanceof ManyManyList) {
            // Data is escaped in ManyManyList->add()
            $extraData = (isset($data['ManyMany'])) ? $data['ManyMany'] : null;
        } else {
            $extraData = null;
        }

        if (!$this->owner->record->canEdit()) {
            return $controller->httpError(403);
        }

        try {
            $form->saveInto($this->owner->record);
            $this->owner->record->write();
            $list->add($this->owner->record, $extraData);
        } catch (ValidationException $e) {
            $form->sessionMessage($e->getResult()->message(), 'bad');
            $responseNegotiator = new PjaxResponseNegotiator(array(
                'CurrentForm' => function () use ($form) {
                    return $form->forTemplate();
                },
                'default' => function () use ($controller) {
                    return $controller->redirectBack();
                }
            ));
            if ($controller->getRequest()->isAjax()) {
                $controller->getRequest()->addHeader('X-Pjax', 'CurrentForm');
            }
            return $responseNegotiator->respond($controller->getRequest());
        }

        return Controller::curr()->redirect($redirectLink);
    }

    /**
     * Gets the ID of the previous record in the list.
     * WARNING: This does not respect the mutated state of the list (e.g. sorting or filtering).
     * Currently the GridField API does not expose this in the detail form view.
     *
     * @todo  This method is very inefficient.
     * @return int
     */
    public function getPreviousRecordID()
    {
        $map = $this->owner->gridField->getManipulatedList()->limit(PHP_INT_MAX, 0)->column('ID');
        $offset = array_search($this->owner->record->ID, $map);
        return isset($map[$offset-1]) ? $map[$offset-1] : false;
    }

    /**
     * Gets the ID of the next record in the list.
     * WARNING: This does not respect the mutated state of the list (e.g. sorting or filtering).
     * Currently the GridField API does not expose this in the detail form view.
     *
     * @todo  This method is very inefficient.
     * @return int
     */
    public function getNextRecordID()
    {
        $map = $this->owner->gridField->getManipulatedList()->limit(PHP_INT_MAX, 0)->column('ID');
        // If there are a million results and they were paginated, this is going to be slow now
        // TODO: Search in the paginated list only somehow (grab the limit + offset and search from there?)
        $offset = array_search($this->owner->record->ID, $map);
        return isset($map[$offset+1]) ? $map[$offset+1] : false;
    }

    /**
     * Determines if the current record is published
     * @return boolean
     */
    public function recordIsPublished()
    {
        if (!$this->owner->record->checkVersioned()) {
            return false;
        }

        if (!$this->owner->record->isInDB()) {
            return false;
        }

        $baseClass = DataObject::getSchema()->baseDataClass($this->owner->record);
        $stageTable = DataObject::getSchema()->tableName($baseClass) . '_Live';

        return (bool) DB::query("SELECT \"ID\" FROM \"{$stageTable}\" WHERE \"ID\" = {$this->owner->record->ID}")
            ->value();
    }

    /**
     * Determines if the current record is deleted from stage
     * @return boolean
     */
    public function recordIsDeletedFromStage()
    {
        // for SiteTree records
        if ($this->owner->hasMethod('getIsDeletedFromStage')) {
            return $this->owner->IsDeletedFromStage;
        }

        if (!$this->owner->record->checkVersioned()) {
            return false;
        }

        if (!$this->owner->record->isInDB()) {
            return true;
        }

        $class = $this->owner->record->class;

        $stageVersion = Versioned::get_versionnumber_by_stage($class, 'Stage', $this->owner->record->ID);

        // Return true for both completely deleted pages and for pages just deleted from stage
        return !($stageVersion);
    }
}
