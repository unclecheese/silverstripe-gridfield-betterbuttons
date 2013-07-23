<?php



/**
 * Decorates {@link GridDetailForm_ItemRequest} to use new form actions and buttons.
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
		'ItemEditForm'
	);
	
	


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


	protected function addButtonToForm($buttonType, $form) {
		if(substr($buttonType, 0, 6) == "Group_") {
			$groupName = substr($buttonType, 6);
			$groupConfig = Config::inst()->get("BetterButtonsGroups", $groupName);
			$label = (isset($groupConfig['label'])) ? $groupConfig['label'] : $groupName;
			$buttons = (isset($groupConfig['buttons'])) ? $groupConfig['buttons'] : array ();
			$button = DropdownFormAction::create(_t('GridFieldBetterButtons.'.$groupName, $label));
			foreach($buttons as $b) {
				if(class_exists($b)) {
					$buttonObj = Injector::inst()->create($b);
					$button->push($buttonObj);
					$buttonObj->configureFromForm($form, $this->owner);
					$buttonObj->transformToInput();										
				}
				else {
					throw new Exception("The button type $b doesn't exist.");
				}
			}
			$form->Actions()->push($button);
		}
		elseif(class_exists($buttonType)) {
			$button = Injector::inst()->create($buttonType);
			$form->Actions()->push($button);
			$button->configureFromForm($form, $this->owner);
			$button->transformToButton();
		}
		else {
			throw new Exception("The button type $buttonType doesn't exist.");
		}				
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
		$create = Config::inst()->get("BetterButtonsViews", "create", Config::UNINHERITED);
		$edit = Config::inst()->get("BetterButtonsViews", "edit", Config::UNINHERITED);
		if(!$create) $create = array ();
		if(!$edit) $edit = array ();		

		// New records
		if($this->owner->record->ID == 0) {
			foreach($create as $buttonType) {				
				$this->addButtonToForm($buttonType, $form);
			}						
		}

		// Existing records
		else {

			foreach($edit as $buttonType) {
				$this->addButtonToForm($buttonType, $form);
			}

			$nextRecordID = $this->getNextRecordID();			
			$cssClass = $nextRecordID ? "cms-panel-link" : "disabled";
			$prevLink = $nextRecordID ? Controller::join_links($this->owner->gridField->Link(),"item", $nextRecordID) : "javascript:void(0);";
			$linkTitle = $nextRecordID ? _t('GridFieldBetterButtons.NEXTRECORD','Go to the next record') : "";
		
			
			$form->Actions()->push(LiteralField::create("prev_next_open",'<div class="gridfield-better-buttons-prevnext-wrap">'));
			$form->Actions()->push(LiteralField::create("next", 
				sprintf(
					"<a class='ss-ui-button gridfield-better-buttons-prevnext gridfield-better-buttons-prev %s' href='%s' title='%s'><img src='".BETTER_BUTTONS_DIR."/images/next.png' alt='next'  /></a>",
					$cssClass,
					$prevLink,
					$linkTitle
				)
			));

			// Prev/next links. Todo: This doesn't scale well.
			$previousRecordID = $this->getPreviousRecordID();
			$cssClass = $previousRecordID ? "cms-panel-link" : "disabled";
			$prevLink = $previousRecordID ? Controller::join_links($this->owner->gridField->Link(),"item", $previousRecordID) : "javascript:void(0);";
			$linkTitle = $previousRecordID ? _t('GridFieldBetterButtons.PREVIOUSRECORD','Go to the previous record') : "";

			$form->Actions()->push(LiteralField::create("prev", 
				sprintf(
					"<a class='ss-ui-button gridfield-better-buttons-prevnext gridfield-better-buttons-prev %s' href='%s' title='%s'><img src='".BETTER_BUTTONS_DIR."/images/prev.png' alt='previous'  /></a>",
					$cssClass,
					$prevLink,
					$linkTitle
				)
			));
			$form->Actions()->push(LiteralField::create("prev_next_close",'</div>'));


		}

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
	 * @param array The form data
	 * @param Form The form object
	 */	
	public function doSaveAndQuit($data, $form) {		
		Controller::curr()->getResponse()->addHeader("X-Pjax","Content");
		return $this->saveAndRedirect($data, $form, $this->getBackLink());
	}




	/**
	 * Goes back to list view
	 * 
	 * @param array The form data
	 * @param Form The form object
	 */	
	public function doCancel($data, $form) {
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


	/**
	 * Handle the publish action.
	 * 
	 * @param $data array The form data.
	 * @param $form Form The form object.
	 */
	public function doPublish($data, $form) {
		$return = $this->owner->doSave($data, $form);
		$this->owner->record->publish('Stage', 'Live');
		return $return;
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
	protected function getBackLink(){
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

	public function checkVersioned() {
		return ( ! singleton($this->owner->record->ClassName)->hasExtension('Versioned') ) ? false : true;
	}


}


