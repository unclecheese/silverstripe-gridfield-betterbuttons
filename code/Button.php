<?php


abstract class Button extends \FormAction {


	public function configureFromForm(\Form $form, \GridFieldDetailForm_ItemRequest $request) {

	}


	public function transformToButton() {
		return $this->setUseButtonTag(true);
	}



	public function transformToInput() {
		return $this->setUseButtonTag(true);
	}
}


class Button_Save extends Button {

	
	public function __construct() {
		parent::__construct('doSave',_t('GridFieldDetailForm.SAVE', 'Save'));
		return $this;
	}



	public function transformToInput() {
		return parent::transformToInput()
			->addExtraClass("saveAndAddNew");			
	}




	public function transformToButton() {
		return parent::transformToButton()			
			->addExtraClass('ss-ui-action-constructive')
			->setAttribute('data-icon','accept')
		;

	}


}





class Button_Cancel extends Button {


	public function __construct() {
		parent::__construct("doCancel", _t('GridFieldBetterButtons.CANCEL','Cancel'));
	}

}



class Button_Delete extends Button {

	public function __construct() {
		parent::__construct('doDelete', _t('GridFieldDetailForm.Delete', 'Delete'));
		Requirements::javascript(BETTER_BUTTONS_DIR.'/javascript/gridfield_betterbuttons_delete.js');		
		return $this
			->setUseButtonTag(true)
			->addExtraClass('gridfield-better-buttons-delete')
			->setAttribute("data-toggletext", _t('GridFieldBetterButtons.AREYOUSURE','Yes. Delete this item.'))
		;
		
	}


	public function transformToInput() {

	}



	public function configureFromForm(\Form $form, \GridFieldDetailForm_ItemRequest $request) {
		$form->Actions()->insertBefore(
			\LiteralField::create('cancelDelete', "<a class='gridfield-better-buttons-undodelete ss-ui-button' href='javascript:void(0)'>"._t('GridFieldDetailForm.CANCELDELETE', 'No. Don\'t delete')."</a>"),
			"action_doDelete"
		);
		return $this;
	}
	
}



class Button_SaveAndAdd extends Button {

	
	public function __construct() {
		parent::__construct("doSaveAndAdd",_t('GridFieldBetterButtons.SAVEANDADDNEW','Save and add new'));
		return $this;		
	}


	public function transformToButton() {
		return parent::transformToButton()			
			->addExtraClass("ss-ui-action-constructive")			
			->setAttribute('data-icon', 'add')
		;

	}



	public function transformToInput() {
		return parent::transformToInput()
			->addExtraClass("saveAndAddNew");
			
	}
}



class Button_SaveAndClose extends Button {

	public function __construct() {
		parent::__construct("doSaveAndQuit", _t('GridFieldDetailForm.SAVEANDCLOSE', 'Save and close'));
		return $this;

	}



	public function transformToInput() {
		return parent::transformToInput()
			->addExtraClass("saveAndClose")			
		;			
	}



	public function transformToButton() {
		return parent::transformToButton()			
			->addExtraClass("ss-ui-action-constructive")
			->setAttribute('data-icon', 'accept')
		;

	}
}


class Button_SaveAndNext extends Button {


	public function __construct() {
		parent::__construct("doSaveAndNext", _t('GridFieldDetailForm.SAVEANDNEXT','Save and go to next record'));
		return $this;		
	}


	public function transformToInput() {
		return parent::transformToInput()
			->addExtraClass("saveAndGoNext")
		;
	}



	public function configureFromForm(\Form $form, \GridFieldDetailForm_ItemRequest $request) {
		if(!$request->getNextRecordID()) {			
			return $this->setDisabled(true);
		}		
	}
}



class Button_SaveAndPrev extends Button {

	public function __construct() {
		parent::__construct("doSaveAndPrev", _t('GridFieldDetailForm.SAVEANDPREV','Save and go to previous record'));
		return $this;
	}


	public function transformToInput() {
		return parent::transformToInput()
			->addExtraClass("saveAndGoPrev")
		;
	}



	public function configureFromForm(\Form $form, \GridFieldDetailForm_ItemRequest $request) {
		if(!$request->getPreviousRecordID()) {
			return $this->setDisabled(true);
		}		
	}
}
