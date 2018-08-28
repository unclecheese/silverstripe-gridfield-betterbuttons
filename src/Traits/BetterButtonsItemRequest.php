<?php

namespace UncleCheese\BetterButtons\Traits;

use Exception;
use SilverStripe\Admin\ModelAdmin;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Control\RequestHandler;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBHTMLText;
use UncleCheese\BetterButtons\Controllers\CustomActionRequest;
use UncleCheese\BetterButtons\Controllers\NestedFormRequest;
use UncleCheese\BetterButtons\Extensions\BetterButtons;
use UncleCheese\BetterButtons\Interfaces\BetterButtonInterface;
use UncleCheese\BetterButtons\Interfaces\BetterButtonVersioned;

/**
 * Trait BetterButtonsItemRequest
 * @package UncleCheese\BetterButtons\Traits
 */
trait BetterButtonsItemRequest
{

    /**
     * Handles all custom action from DataObjects and hands them off to a sub-controller.
     * e.g. /customaction/mymethodname
     *
     * Can't handle the actions here because the url_param '$Action!' gets matched, and we don't
     * get to read anything after /customaction/
     *
     * @param  HTTPRequest $r
     * @return RequestHandler|string
     */
    public function customaction(HTTPRequest $r)
    {
        $req = new CustomActionRequest($this, $this->ItemEditForm());

        return $req->handleRequest($r);
    }

    /**
     * Handles all custom action from DataObjects and hands them off to a sub-controller.
     * e.g. /nestedform?action=myDataObjectAction
     *
     * @param  HTTPRequest $r
     * @return RequestHandler|string
     */
    public function nestedform(HTTPRequest $r)
    {
        $req = new NestedFormRequest($this, $this->ItemEditForm());

        return $req->handleRequest($r);
    }

    /**
     * Redirecting to the current URL doesn't do anything, so this is just a dummy action
     * that gives the request somewhere to go in order to force a reload, and then just
     * redirects back to the original link.
     *
     * @param HTTPRequest $request The request object
     * @return HTTPResponse
     */
    public function addnew(HTTPRequest $request)
    {
        $this->getRecord()->ID = 0;
        return $this->redirectAfterSave(true);
    }

    /**
     * Given a list of actions, remove anything that doesn't belong.
     * @param  FieldList $actions
     * @return FieldList
     * @throws Exception
     */
    protected function filterFieldList(FieldList $actions)
    {
        $list = FieldList::create();
        /* @var DataObject|BetterButtons */
        $record = $this->getRecord();
        foreach ($actions as $a) {
            if (!$a instanceof BetterButtonInterface) {
                throw new Exception(sprintf(
                    '%s must implement %s',
                    get_class($a),
                    BetterButtonInterface::class
                ));
            }

            if (!$a->shouldDisplay()) {
                continue;
            }

            if (($a instanceof BetterButtonVersioned) && !$record->checkVersioned()) {
                continue;
            }


            $list->push($a);
        }

        return $list;
    }

    /**
     * Goes back to list view
     *
     * @return HTTPResponse
     */
    public function cancel()
    {
        return $this->returnToList();
    }

    /**
     * Creates a new record. If you're already creating a new record,
     * this forces the URL to change. Hacky UI workaround.
     *
     * @param  array $data The form data
     * @param  Form $form The Form object
     * @return HTTPResponse
     */
    public function doNew(array $data, Form $form)
    {
        /* @var RequestHandler $controller */
        $controller = $this->getToplevelController();
        return $this->addnew(
            $controller->getRequest()
        );
    }

    /**
     * Saves the form and forwards to a blank form to continue creating
     *
     * @param array $data The form data
     * @param Form $form The form object
     * @return HTTPResponse
     */
    public function doSaveAndAdd(array $data, Form $form)
    {
        parent::doSave($data, $form);

        return $this->addnew(
            $this->getToplevelController()->getRequest()
        );
    }

    /**
     * Saves the form and goes back to list view
     *
     *
     * @param array $data The form data
     * @param Form $form The form object
     * @return HTTPResponse
     */
    public function doSaveAndQuit(array $data, Form $form)
    {
        parent::doSave($data, $form);

        return $this->returnToList();
    }


    /**
     * Saves the record and goes to the next one
     * @param  array $data The form data
     * @param  Form $form The Form object
     * @return HTTPResponse
     */
    public function doSaveAndNext(array $data, Form $form)
    {
        parent::doSave($data, $form);
        $record = $this->getNextRecord();
        $this->record = $record;
        return $this->edit(
            $this->getToplevelController()->getRequest()
        );
    }

    /**
     * Saves the record and goes to the previous one
     * @param  array $data The form data
     * @param  Form $form The Form object
     * @return HTTPResponse
     */
    public function doSaveAndPrev(array $data, Form $form)
    {
        parent::doSave($data, $form);
        $record = $this->getPreviousRecord();
        $this->record = $record;
        return $this->edit(
            $this->getToplevelController()->getRequest()
        );
    }

    /**
     * Gets the ID of the previous record in the list.
     *
     * @todo  This method is very inefficient.
     * @return DataObject
     */
    public function getPreviousRecord()
    {
        /* @var GridField $grid */
        $grid = $this->getGridField();
        /* @var DataList $list */
        $list = $grid->getList();
        $unlimitedList = $list->limit(PHP_INT_MAX, 0);
        $map = $unlimitedList->column('ID');
        $offset = array_search($this->getRecord()->ID, $map);
        $id = isset($map[$offset - 1]) ? $map[$offset - 1] : -1;

        return $unlimitedList->byID($id);
    }

    /**
     * Gets the ID of the next record in the list.
     * WARNING: This does not respect the mutated state of the list (e.g. sorting or filtering).
     * Currently the GridField API does not expose this in the detail form view.
     *
     * @todo  This method is very inefficient.
     * @return DataObject
     */
    public function getNextRecord()
    {
        /* @var GridField $grid */
        $grid = $this->getGridField();
        /* @var DataList $list */
        $list = $grid->getList();
        $unlimitedList = $list->limit(PHP_INT_MAX, 0);
        $map = $unlimitedList->column('ID');
        $offset = array_search($this->getRecord()->ID, $map);
        $id = isset($map[$offset + 1]) ? $map[$offset + 1] : -1;

        return $unlimitedList->byID($id);
    }

    /**
     * Leave detail view and go back to the grid field list.
     *
     * @return HTTPResponse|DBHTMLText
     */
    public function returnToList()
    {
        $crumbs = $this->Breadcrumbs();
        if ($crumbs && $crumbs->count() >= 2) {
            $oneLevelUp = $crumbs->offsetGet($crumbs->count() - 2);
            $controller = $this->getToplevelController();
            $controller->getRequest()->addHeader('X-Pjax', 'Content');
            $url = $oneLevelUp->Link;

            // TODO make a proper solution for this
            // HORRIBLE Hack to save filter params in ModelAdmin detail view
            // Better buttons does not maintain the filter params set in URL
            if($controller instanceof ModelAdmin){
                $backURL = $this->getGridField()->getRequest()->postVar('BackURL');
                $parts = explode("?q", $backURL);

                // Dumb assumption that filter params not exist
                if(!isset($parts[1])){
                    return $controller->redirect($url, 302);
                }

                $paramsString = $parts[1];

                $url = Controller::join_links(
                    $url,
                    '?q' . $paramsString
                );
            }

            return $controller->redirect($url, 302);
        }

        $oldID = $this->getRecord()->ID;
        // Hack the ID so the record isn't found in the list
        $this->getRecord()->ID = -1;
        $response = $this->redirectAfterSave(false);
        $this->getRecord()->ID = $oldID;

        return $response;
    }
}