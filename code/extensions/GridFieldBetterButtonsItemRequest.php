<?php

/**
 * Decorates {@link GridDetailForm_ItemRequest} to use new form actions and buttons.
 *
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
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


    /**
     * Handles all custom action from DataObjects and hands them off to a sub-controller.
     * e.g. /customaction/mymethodname
     * 
     * Can't handle the actions here because the url_param '$Action!' gets matched, and we don't
     * get to read anything after /customaction/
     * 
     * @param  SS_HTTPRequest $r
     * @return BetterButtonsCustomActionRequest
     */
    public function customaction(SS_HTTPRequest $r) {
        $req = new BetterButtonsCustomActionRequest($this, $this->owner, $this->owner->ItemEditForm());

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
        
        
		$actions = $this->owner->record->getBetterButtonsActions();
        $form->setActions($this->filterFieldList($form, $actions));        

		if($form->Fields()->hasTabset()) {
			$form->Fields()->findOrMakeTab('Root')->setTemplate('TabSet');
			$form->addExtraClass('cms-tabset');
		}
        
        $utils = $this->owner->record->getBetterButtonsUtils();
		$form->Utils = $this->filterFieldList($form, $utils);
		$form->setTemplate('BetterButtons_EditForm');
	}


    /**
     * Given a list of actions, remove anything that doesn't belong.
     * @param  Form      $form    
     * @param  FieldList $actions 
     * @return FieldList
     */
    protected function filterFieldList(Form $form, FieldList $actions) {
        $list = FieldList::create();

        foreach($actions as $a) {

            if(!$a instanceof BetterButtonInterface) {
                throw new Exception("{$buttonObj->class} must implement BetterButtonInterface");
            }

            $a->bindGridField($form, $this->owner);

            if(!$a->shouldDisplay()) {
                continue;
            }
            
            if(($a instanceof BetterButton_Versioned) && !$this->owner->record->checkVersioned()) {
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


    /**
     * Publishes the record and goes to make a new record
     * @param  array $data The form data
     * @param  Form $form The Form object
     * @return SS_HTTPResponse
     */
	public function doPublishAndAdd($data, $form) {
		return $this->publish($data, $form, $this->owner, $this->owner->Link('addnew'));
	}


    /**
     * Publishes the record and closes the detail form
     * @param  array $data The form data
     * @param  Form $form The Form object
     * @return SS_HTTPResponse
     */
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


    /**
     * Saves the record and goes to the next one
     * @param  arary $data The form data
     * @param  Form $form The Form object
     * @return SS_HTTPResponse
     */
	public function doSaveAndNext($data, $form) {
		Controller::curr()->getResponse()->addHeader("X-Pjax","Content");
		$link = Controller::join_links($this->owner->gridField->Link(),"item", $this->getNextRecordID());

		return $this->saveAndRedirect($data, $form, $link);
	}


    /**
     * Saves the record and goes to the previous one
     * @param  arary $data The form data
     * @param  Form $form The Form object
     * @return SS_HTTPResponse
     */
	public function doSaveAndPrev($data, $form) {
		Controller::curr()->getResponse()->addHeader("X-Pjax","Content");
		$link = Controller::join_links($this->owner->gridField->Link(),"item", $this->getPreviousRecordID());

		return $this->saveAndRedirect($data, $form, $link);
	}


    /**
     * Creates a new record. If you're already creating a new record,
     * this forces the URL to change. Hacky UI workaround.
     * 
     * @param  arary $data The form data
     * @param  Form $form The Form object
     * @return SS_HTTPResponse
     */
	public function doNew($data, $form) {
		return Controller::curr()->redirect($this->owner->Link('addnew'));
	}


    /**
     * Allows us to have our own configurable save button
     * @param  arary $data The form data
     * @param  Form $form The Form object
     * @return SS_HTTPResponse
     */
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

        if($redirectURL) {
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
	 * @todo  This had to be directly copied from {@link GridFieldDetailForm_ItemRequest} 
     * because it is a protected method and not visible to a decorator!
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
	 * @todo  This had to be directly copied from {@link GridFieldDetailForm_ItemRequest} 
     * because it is a protected method and not visible to a decorator!
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
    public function getPreviousRecordID() {
		$map = $this->owner->gridField->getManipulatedList()->column('ID');
		$offset = array_search($this->owner->record->ID, $map);
		return ($offset > 0) ? $map[$offset-1] : false;
	}


    /**
     * Gets the ID of the next record in the list.
     * WARNING: This does not respect the mutated state of the list (e.g. sorting or filtering).
     * Currently the GridField API does not expose this in the detail form view.
     *
     * @todo  This method is very inefficient.
     * @return int
     */
	public function getNextRecordID() {
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


/**
 * A subcontroller that handles custom actions. The parent controller matches 
 * the url_param '$Action!' and doesn't hand off any trailing params. This subcontoller
 * is aware of them
 *
 * /item/4/customaction/my-dataobject-method Invokes "my-dataobject-method" on the record
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class BetterButtonsCustomActionRequest extends RequestHandler {


    /**     
     * @var array
     */
    private static $url_handlers = array (
        '$Action!' => 'handleCustomAction'
    );


    /**     
     * @var array
     */
    private static $allowed_actions = array (
        'handleCustomAction'
    );


    /**
     * The parent extension. There are actually some useful methods in the extension
     * itself, so we need access to that object
     * 
     * @var GridFieldBetterButtonsItemRequest
     */
    protected $parent;


    /**
     * The parent controller
     * @var GridFieldDetailForm_ItemRequest
     */
    protected $controller;


    /**
     * The record we're editing
     * @var DataObject
     */
    protected $record;

    
    /**
     * The Form that is editing the record
     * @var  Form
     */
    protected $form;


    /**
     * Buidls the request
     * @param GridFieldBetterButtonsItemRequest $parent     The extension instance
     * @param GridFieldDetailForm_ItemRequest $controller The request that points to the detail form
     */
    public function __construct($parent, $controller, $form) {
        $this->parent = $parent;
        $this->controller = $controller;
        $this->form = $form;
        $this->record = $this->controller->record;
        parent::__construct();
    }


    /**
     * Takes the action at /customaction/my-action-name and feeds it to the DataObject.
     * Checks to see if the method is allowed to be invoked first.
     * 
     * @param  SS_HTTPRequest $r
     * @return SS_HTTPResponse
     */
    public function handleCustomAction(SS_HTTPRequest $r) {
        $action = $r->param('Action');
        if(!$this->record->isCustomActionAllowed($action)) {
            return $this->httpError(403);
        }

        $formAction = $this->record->findActionByName($action);
        if(!$formAction) {
            return $this->httpError(403, "Action $action doesn't exist");
        }

        $this->record->$action($this->controller, $r);
        
        Controller::curr()->getResponse()->addHeader("X-Pjax","Content");
        Controller::curr()->getResponse()->addHeader('X-Status', $formAction->getSuccessMessage());                

        if($formAction->getRedirectURL()) {
            return Controller::curr()->redirect($formAction->getRedirectURL());
        }
        
        if($formAction->getRedirectType() == BetterButtonCustomAction::GOBACK) {
            return Controller::curr()->redirect(preg_replace('/\?.*/', '', $this->parent->getBackLink()));
        }
        
        return Controller::curr()->redirect(
            Controller::join_links($this->controller->gridField->Link("item"),$this->record->ID,"edit")
        );
    }
}
