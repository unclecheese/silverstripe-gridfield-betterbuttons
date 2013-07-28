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
	static $allowed_actions = array (
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




	/**
	 * Updates the detail form to include new form actions and buttons
	 * 
	 * @param Form The ItemEditForm object	 
	 */
	public function updateItemEditForm($form) {		
		

		Requirements::css(BETTER_BUTTONS_DIR.'/css/gridfield_betterbuttons.css');
		Requirements::javascript(BETTER_BUTTONS_DIR.'/javascript/gridfield_betterbuttons.js');
		
		$actions = FieldList::create();
		// New records
		if($this->owner->record->ID == 0) {
			// Scan for UploadField instances, since those require a save before adding.
			// Todo: there is probably a more intelligent way to do this.
			$files = false;
			foreach($this->owner->record->getCMSFields()->dataFields() as $field) {
				if($field instanceof UploadField) {
					$files = true;
					break;
				}				
			}
			if($files) {
				// If upload fields are present, offer a "save and add files" button
				$actions->push(FormAction::create("doSave", _t('GridFieldBetterButtons.SAVEANDADDFILES','Save and add file(s)'))
						->setUseButtonTag(true)
						->addExtraClass("ss-ui-action-constructive")
				);
			}
			else {
				// Creates a record and offers a blank form to create another
				$actions->push(FormAction::create("doSaveAndAdd", _t('GridFieldBetterButtons.SAVEANDADD','Save and add another'))
						->setUseButtonTag(true)
						->addExtraClass("ss-ui-action-constructive")
				);
				// Creates a record and goes back to list
				$actions->push(FormAction::create("doSaveAndQuit", _t('GridFieldBetterButtons.SAVEANDCLOSE','Save and close'))
						->setUseButtonTag(true)
						->addExtraClass("ss-ui-action-constructive")
				);
			}

		}

		// Existing records
		else {

			// Saves the record and redirects back to same record 
			$actions->push(FormAction::create('doSave', _t('GridFieldDetailForm.SAVE', 'Save'))
					->setUseButtonTag(true)
					->addExtraClass('ss-ui-action-constructive')
					->setAttribute('data-icon', 'accept')
			);

			$actions->push(DropdownFormAction::create(_t('GridFieldBetterButtons.SAVEAND','Save and...'), array(
					FormAction::create("doSaveAndAdd",_t('GridFieldBetterButtons.SAVEANDADDNEW','Save and add new'))->addExtraClass("saveAndAddNew"),
					FormAction::create("doSaveAndQuit", _t('GridFieldDetailForm.SAVEANDCLOSE', 'Save and close'))->addExtraClass("saveAndClose"),
					$n = FormAction::create("doSaveAndNext", _t('GridFieldDetailForm.SAVEANDNEXT','Save and go to next record'))->addExtraClass("saveAndGoNext"),
					$p = FormAction::create("doSaveAndPrev", _t('GridFieldDetailForm.SAVEANDPREV','Save and go to previous record'))->addExtraClass("saveAndGoPrev")
				))
				->addExtraClass("ss-ui-action-constructive")
			);

			if(!$this->getPreviousRecordID()) {				
				$p->addExtraClass('disabled');
			}

			if(!$this->getNextRecordID()) {
				$n->setAttribute('disabled',true);
			}

			// Cancels the delete action
			$actions->push(LiteralField::create('cancelDelete', "<a class='gridfield-better-buttons-undodelete ss-ui-button' href='javascript:void(0)'>"._t('GridFieldDetailForm.CANCELDELETE', 'No. Don\'t delete')."</a>"));

			// Deletes the record
			$actions->push(FormAction::create('doDelete', _t('GridFieldDetailForm.Delete', 'Delete'))
				->setUseButtonTag(true)
				->addExtraClass('gridfield-better-buttons-delete')
				->setAttribute("data-toggletext", _t('GridFieldBetterButtons.AREYOUSURE','Yes. Delete this item.'))
			);

			$nextRecordID = $this->getNextRecordID();			
			$cssClass = $nextRecordID ? "cms-panel-link" : "disabled";
			$prevLink = $nextRecordID ? Controller::join_links($this->owner->gridField->Link(),"item", $nextRecordID) : "javascript:void(0);";
			$linkTitle = $nextRecordID ? _t('GridFieldBetterButtons.NEXTRECORD','Go to the next record') : "";
		
			
			$actions->push(LiteralField::create("prev_next_open",'<div class="gridfield-better-buttons-prevnext-wrap">'));
			$actions->push(LiteralField::create("next", 
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

			$actions->push(LiteralField::create("prev", 
				sprintf(
					"<a class='ss-ui-button gridfield-better-buttons-prevnext gridfield-better-buttons-prev %s' href='%s' title='%s'><img src='".BETTER_BUTTONS_DIR."/images/prev.png' alt='previous'  /></a>",
					$cssClass,
					$prevLink,
					$linkTitle
				)
			));
			$actions->push(LiteralField::create("prev_next_close",'</div>'));


		}

		// Cancels the edit. Same as back button
		$actions->push(LiteralField::create("doCancel",'<a class="backlink ss-ui-button cms-panel-link" href="'.$this->getBackLink().'?" role="button" aria-disabled="false">Cancel</span></a>'));
		$form->setActions($actions);
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

}


