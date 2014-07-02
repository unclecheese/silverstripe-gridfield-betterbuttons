<?php

/**
 * Decorates {@link GridDetailForm_ItemRequest} to use new form actions and buttons.
 *
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  gridfield-betterbuttons
 *
 * */
class GridFieldBetterButtonsItemRequest extends DataExtension {


	/**
	 * @var array Allowed controller actions
	 */
	private static $allowed_actions = array (
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
        'customaction'
	);


    public function customaction(SS_HTTPRequest $r) {
        $req = new BetterButtonsCustomActionRequest($this, $this->owner);

        return $req->handleRequest($r, DataModel::inst());
    }

	/**
	 * Redirecting to the current URL doesn't do anything, so this is just a dummy action
	 * that gives the request somewhere to go in order to force a reload, and then just
	 * redirects back to the original link.
	 *
	 * @param SS_HTTPRequest The request object
	 */
	public function addnew(SS_HTTPRequest $r) {
		return Controller::curr()->redirect(Controller::join_links($this->owner->gridField->Link("item"),"new"));
	}


	/**
	 * Updates the detail form to include new form actions and buttons
	 *
	 * @param Form The ItemEditForm object
	 */
	public function updateItemEditForm($form) {
		Requirements::css(BETTER_BUTTONS_DIR.'/css/gridfield_betterbuttons.css');
		Requirements::javascript(BETTER_BUTTONS_DIR.'/javascript/gridfield_betterbuttons.js');
        $form->setActions(FieldList::create());
		$form->setActions($this->owner->record->getBetterButtonsActions($form, $this->owner));        
		if($form->Fields()->hasTabset()) {
			$form->Fields()->findOrMakeTab('Root')->setTemplate('TabSet');
			$form->addExtraClass('cms-tabset');
		}
		$form->Utils = $this->owner->record->getBetterButtonsUtils($form, $this->owner);
		$form->setTemplate('BetterButtons_EditForm');

	}


	/**
	 * Saves the form and forwards to a blank form to continue creating
	 *
	 * @param array The form data
	 * @param Form The form object
	 */
	public function doSaveAndAdd($data, $form) {
		return $this->saveAndRedirect($data, $form, $this->owner->Link("addnew"));
	}


	/**
	 * Saves the form and goes back to list view
     *
	 *
	 * @param array The form data
	 * @param Form The form object
	 */
	public function doSaveAndQuit($data, $form) {
		Controller::curr()->getResponse()->addHeader("X-Pjax","Content");
		return $this->saveAndRedirect($data, $form, $this->getBackLink());
	}


	public function doPublishAndAdd($data, $form) {
		return $this->publish($data, $form, $this->owner, $this->owner->Link('addnew'));
	}


	public function doPublishAndClose($data, $form) {
		Controller::curr()->getResponse()->addHeader("X-Pjax","Content");
		return $this->publish($data, $form, $this->owner, $this->getBackLink());
	}


	/**
	 * Goes back to list view
	 *
	 * @param array The form data
	 * @param Form The form object
	 */
	public function cancel() {
		Controller::curr()->getResponse()->addHeader("X-Pjax","Content");
		return Controller::curr()->redirect($this->getBackLink());
	}


	public function doSaveAndNext($data, $form) {
		Controller::curr()->getResponse()->addHeader("X-Pjax","Content");
		$link = Controller::join_links($this->owner->gridField->Link(),"item", $this->getNextRecordID());

		return $this->saveAndRedirect($data, $form, $link);
	}


	public function doSaveAndPrev($data, $form) {
		Controller::curr()->getResponse()->addHeader("X-Pjax","Content");
		$link = Controller::join_links($this->owner->gridField->Link(),"item", $this->getPreviousRecordID());

		return $this->saveAndRedirect($data, $form, $link);
	}


	public function doNew($data, $form) {
		return Controller::curr()->redirect($this->owner->Link('addnew'));
	}


	public function save($data, $form) {
		return $this->owner->doSave($data, $form);
	}


    /**
     * @param $data
     * @param $form
     * @return HTMLText|SS_HTTPResponse|ViewableData_Customised
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
            $form->saveInto($this->owner->record);
            $this->owner->record->write();
            $list->add($this->owner->record, $extraData);
            $this->owner->record->publish('Stage', 'Live');
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

        if($redirectURL) {
        	return $controller->redirect($redirectURL);
        }


        $link = '<a href="' . $this->owner->Link('edit') . '">"'
            . Convert::raw2xml($this->owner->record->Title)
            . '"</a>';
        $message = sprintf(
            'Published %s %s',
            $this->owner->record->i18n_singular_name(),
            $link
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
     * @return HTMLText|ViewableData_Customised
     */
    public function unPublish()
    {
        $origStage = Versioned::current_stage();
        Versioned::reading_stage('Live');

        // This way our ID won't be unset
        $clone = clone $this->owner->record;
        $clone->delete();

        Versioned::reading_stage($origStage);

        return $this->owner->edit(Controller::curr()->getRequest());
    }



    /**
     * @param $data
     * @param $form
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
	 * @todo  This had to be directly copied from {@link GridFieldDetailForm_ItemRequest} because it is a protected method and not visible to a decorator!
	 */
	protected function getToplevelController() {
		$c = $this->owner->getController();
		while($c && $c instanceof GridFieldDetailForm_ItemRequest) {
			$c = $c->getController();
		}
		return $c;
	}



	/**
	 * Gets the back link
	 *
	 * @return  string
	 * @todo  This had to be directly copied from {@link GridFieldDetailForm_ItemRequest} because it is a protected method and not visible to a decorator!
	 */
	public function getBackLink(){
		// TODO Coupling with CMS
		$backlink = '';
		$toplevelController = $this->getToplevelController();
		if($toplevelController && $toplevelController instanceof LeftAndMain) {
			if($toplevelController->hasMethod('Backlink')) {
				$backlink = $toplevelController->Backlink();
			} elseif($this->owner->getController()->hasMethod('Breadcrumbs')) {
				$parents = $this->owner->getController()->Breadcrumbs(false)->items;
				$backlink = array_pop($parents)->Link;
			}
		}
		if(!$backlink) $backlink = $toplevelController->Link();

		return $backlink;
	}


	/**
	 * Oh, the horror! DRY police be advised. This function is a serious offender.
	 * Saves the form data and redirects to a given link
	 *
	 * @param array The form data
	 * @param Form The form object
	 * @param string The redirect link
	 * @todo  GridFieldDetailForm_ItemRequest::doSave is too monolithic, making overloading impossible. Most of this code is a direct copy.
	 * */
	protected function saveAndRedirect($data, $form, $redirectLink) {
		$new_record = $this->owner->record->ID == 0;
		$controller = Controller::curr();
		$list = $this->owner->gridField->getList();

		if($list instanceof ManyManyList) {
			// Data is escaped in ManyManyList->add()
			$extraData = (isset($data['ManyMany'])) ? $data['ManyMany'] : null;
		} else {
			$extraData = null;
		}

		if(!$this->owner->record->canEdit()) {
			return $controller->httpError(403);
		}

		try {
			$form->saveInto($this->owner->record);
			$this->owner->record->write();
			$list->add($this->owner->record, $extraData);
		} catch(ValidationException $e) {
			$form->sessionMessage($e->getResult()->message(), 'bad');
			$responseNegotiator = new PjaxResponseNegotiator(array(
				'CurrentForm' => function() use(&$form) {
					return $form->forTemplate();
				},
				'default' => function() use(&$controller) {
					return $controller->redirectBack();
				}
			));
			if($controller->getRequest()->isAjax()){
				$controller->getRequest()->addHeader('X-Pjax', 'CurrentForm');
			}
			return $responseNegotiator->respond($controller->getRequest());
		}
        Controller::curr()->getResponse()->addHeader('X-Reload', true);
            
		return Controller::curr()->redirect($redirectLink);
	}


	public function getPreviousRecordID() {
		$map = $this->owner->gridField->getManipulatedList()->column('ID');
		$offset = array_search($this->owner->record->ID, $map);
		return ($offset > 0) ? $map[$offset-1] : false;
	}


	public function getNextRecordID() {
		$map = $this->owner->gridField->getManipulatedList()->limit(PHP_INT_MAX, 0)->column('ID');
		// If there are a million results and they were paginated, this is going to be slow now
		// TODO: Search in the paginated list only somehow (grab the limit + offset and search from there?)
		$offset = array_search($this->owner->record->ID, $map);
		return isset($map[$offset+1]) ? $map[$offset+1] : false;
	}


	public function recordIsPublished() {

        if(!$this->owner->record->checkVersioned()) return false;
        if (!$this->owner->record->isInDB()) {
            return false;
        }

        $table = $this->owner->record->class;

        while (($p = get_parent_class($table)) !== 'DataObject') {
            $table = $p;
        }

        return (bool) DB::query("SELECT \"ID\" FROM \"{$table}_Live\" WHERE \"ID\" = {$this->owner->record->ID}")->value();

	}

}

class BetterButtonsCustomActionRequest extends RequestHandler {

    private static $url_handlers = array (
        '$Action!' => 'handleCustomAction'
    );


    private static $allowed_actions = array (
        'handleCustomAction'
    );


    protected $parent;


    protected $controller;


    protected $record;

    
    public function __construct($parent, $controller) {
        $this->parent = $parent;
        $this->controller = $controller;
        $this->record = $this->controller->record;
        parent::__construct();
    }


    public function handleCustomAction(SS_HTTPRequest $r) {
        $action = $r->param('Action');
        if(!$this->record->isCustomActionAllowed($action)) {
            return $this->httpError(403);
        }

        $this->record->$action($this->controller, $r);
        
        Controller::curr()->getResponse()->addHeader("X-Pjax","Content");
        
        if($r->getVar('redirectType') == BetterButtonCustomAction::GOBACK) {
            return Controller::curr()->redirect(preg_replace('/\?.*/', '', $this->parent->getBackLink()));
        }
        
        return Controller::curr()->redirect(
            Controller::join_links($this->controller->gridField->Link("item"),$this->record->ID,"edit")
        );
    }
}
