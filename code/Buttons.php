<?php


namespace BetterButtons\Buttons;


abstract class Button {


	protected $form;


	protected $request;


	public function __construct(\GridFieldDetailForm_ItemRequest $request, \Form $form) {
		$this->request = $request;
		$this->form = $form;
		$this->addButtonToActions();
	}



	protected function addButtonToActions() {

	}

	
}





class Button_Save extends Button {

	
	protected function addButtonToActions() {
		$this->form->actions->push(\FormAction::create('doSave', _t('GridFieldDetailForm.SAVE', 'Save'))
			->setUseButtonTag(true)
			->addExtraClass('ss-ui-action-constructive')
			->setAttribute('data-icon','accept')
		);

	}


}





class Button_Cancel extends Button {


	protected function addButtonToActions() {		
		$this->form->actions->push(\FormAction::create("doCancel", _t('GridFieldBetterButtons.CANCEL','Cancel'))
			->setUseButtonTag(true)
		);

	}
}



class Button_Delete extends Button {

	public function addButtonToActions() {
		$this->form->actions->push(\LiteralField::create('cancelDelete', "<a class='gridfield-better-buttons-undodelete ss-ui-button' href='javascript:void(0)'>"._t('GridFieldDetailForm.CANCELDELETE', 'No. Don\'t delete')."</a>"));

		$this->form->actions->push(\FormAction::create('doDelete', _t('GridFieldDetailForm.Delete', 'Delete'))
				->setUseButtonTag(true)
				->addExtraClass('gridfield-better-buttons-delete')
				->setAttribute("data-toggletext", _t('GridFieldBetterButtons.AREYOUSURE','Yes. Delete this item.'))
		);

	}

}



class Button_SaveAndAdd extends Button {

	public function addButtonToActions() {
		$this->form->actions->push(\FormAction::create("doSaveAndAdd",_t('GridFieldBetterButtons.SAVEANDADDNEW','Save and add new'))
			->addExtraClass("saveAndAddNew")
		);
	}
}



class Button_SaveAndClose extends Button {

	public function addButtonToActions() {
		$this->form->actions->push(\FormAction::create("doSaveAndQuit", _t('GridFieldDetailForm.SAVEANDCLOSE', 'Save and close'))
			->addExtraClass("saveAndClose")
		);
	}
}


class Button_SaveAndNext extends Button {

	public function addButtonToActions() {
		$a = \FormAction::create("doSaveAndNext", _t('GridFieldDetailForm.SAVEANDNEXT','Save and go to next record'))
			->addExtraClass("saveAndGoNext");

		if(!$this->request->getNextRecordID()) {
			$a->setAttribute('disabled',true);
		}
		$this->form->actions->push($a);

					

		
	}
}



class Button_SaveAndPrev extends Button {

	public function addButtonToActions() {

		$a = FormAction::create("doSaveAndPrev", _t('GridFieldDetailForm.SAVEANDPREV','Save and go to previous record'))
			->addExtraClass("saveAndGoPrev");

		if(!$this->request->getPrevRecordID()) {
			$a->setAttribute('disabled',true);
		}

		$this->form->actions->push($a);
	}
}