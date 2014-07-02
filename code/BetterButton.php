<?php

interface BetterButton_Versioned {}

abstract class BetterButton extends FormAction {


	protected $gridFieldRequest;


	public function __construct($name, $title = null, Form $form, GridFieldDetailForm_ItemRequest $request) {
		$this->gridFieldRequest = $request;
		$this->form = $form;

		return parent::__construct($name, $title, $form);
	}


	public function baseTransform() {
		return $this;
	}


	public function transformToButton() { 			
		$this->baseTransform();
		return $this->setUseButtonTag(true);
	}


	public function transformToInput() { 		
		$this->baseTransform();
		return $this->setUseButtonTag(false);
	}


	public function shouldDisplay() {
		return true;
	}	


}


class BetterButton_Save extends BetterButton {

	public function __construct(Form $form, GridFieldDetailForm_ItemRequest $request) {
		parent::__construct('save',_t('GridFieldDetailForm.SAVE', 'Save'), $form, $request);
	}


	public function shouldDisplay() {
		$record = $this->gridFieldRequest->record;
		
		return $record->canEdit();
	}


	public function transformToButton() {
		return parent::transformToButton()
			->addExtraClass('ss-ui-action-constructive')
			->setAttribute('data-icon','accept');
	}
}


class BetterButton_New extends BetterButton {

	public function __construct(Form $form, GridFieldDetailForm_ItemRequest $request) {
		parent::__construct("doNew", _t('GridFieldBetterButtons.NEWRECORD','New record'), $form, $request);

		$this
			->addExtraClass("ss-ui-action-constructive")
			->setAttribute('data-icon', 'add');
	}


	public function shouldDisplay() {
		return $this->gridFieldRequest->record->canCreate();
	}
}


class BetterButton_Delete extends BetterButton {

	public function __construct(Form $form, GridFieldDetailForm_ItemRequest $request) {
		parent::__construct('doDelete', _t('GridFieldDetailForm.Delete', 'Delete'), $form, $request);
	}


	public function baseTransform() {
		parent::baseTransform();
		Requirements::javascript(BETTER_BUTTONS_DIR.'/javascript/gridfield_betterbuttons_delete.js');

		$this
			->setUseButtonTag(true)
			->addExtraClass('gridfield-better-buttons-delete')
			->setAttribute("data-toggletext", _t('GridFieldBetterButtons.AREYOUSURE','Yes. Delete this item.'))
			->setAttribute("data-confirmtext", _t('GridFieldDetailForm.CANCELDELETE', 'No. Don\'t delete.'));
	}


	public function shouldDisplay() {
		return !$this->gridFieldRequest->recordIsPublished() && $this->gridFieldRequest->record->canDelete();
	}

}


class BetterButton_SaveAndAdd extends BetterButton {

	public function __construct(Form $form, GridFieldDetailForm_ItemRequest $request) {
		parent::__construct("doSaveAndAdd",_t('GridFieldBetterButtons.SAVEANDADDNEW','Save and add new'), $form, $request);
	}


	public function shouldDisplay() {
		$record = $this->gridFieldRequest->record;

		return $record->canEdit() && $record->canCreate();
	}


	public function transformToButton() {
		return parent::transformToButton()
			->addExtraClass("ss-ui-action-constructive")
			->setAttribute('data-icon', 'add');
	}


	public function transformToInput() {
		return parent::transformToInput()
			->addExtraClass("saveAndAddNew");
	}
}


class BetterButton_SaveAndClose extends BetterButton {

	public function __construct(Form $form, GridFieldDetailForm_ItemRequest $request) {
		parent::__construct("doSaveAndQuit", _t('GridFieldDetailForm.SAVEANDCLOSE', 'Save and close'), $form, $request);

	}

	public function shouldDisplay() {
		$record = $this->gridFieldRequest->record;
		
		return $record->canEdit();
	}


	public function transformToInput() {
		return parent::transformToInput()
			->addExtraClass("saveAndClose");
	}


	public function transformToButton() {	
		return parent::transformToButton()
			->addExtraClass("ss-ui-action-constructive")
			->setAttribute('data-icon', 'accept');
	}
}


class BetterButton_SaveAndNext extends BetterButton {

	public function __construct(Form $form, GridFieldDetailForm_ItemRequest $request) {
		parent::__construct("doSaveAndNext", _t('GridFieldDetailForm.SAVEANDNEXT','Save and go to next record'), $form, $request);
	}


	public function shouldDisplay() {
		$record = $this->gridFieldRequest->record;
		
		return $record->canEdit();
	}


	public function baseTransform() {
		parent::baseTransform();
		$disabled = (!$this->gridFieldRequest->getNextRecordID());

		return $this->setDisabled($disabled);
	}


	public function transformToInput() {
		return parent::transformToInput()
			->addExtraClass("saveAndGoNext");
	}
}


class BetterButton_SaveAndPrev extends BetterButton {

	public function __construct(Form $form, GridFieldDetailForm_ItemRequest $request) {
		parent::__construct("doSaveAndPrev", _t('GridFieldDetailForm.SAVEANDPREV','Save and go to previous record'), $form, $request);
	}


	public function shouldDisplay() {
		$record = $this->gridFieldRequest->record;
		
		return $record->canEdit();
	}

	public function baseTransform() {
		parent::baseTransform();
		$disabled = (!$this->gridFieldRequest->getPreviousRecordID());

		return $this->setDisabled($disabled);
	}



	public function transformToInput() {		
		return parent::transformToInput()
			->addExtraClass("saveAndGoPrev");			
	}
}


class BetterButton_SaveDraft extends BetterButton implements BetterButton_Versioned {
	
	public function __construct(Form $form, GridFieldDetailForm_ItemRequest $request) {
        parent::__construct('save', _t('SiteTree.BUTTONSAVED', 'Saved'), $form, $request);

    }

	public function shouldDisplay() {
		$record = $this->gridFieldRequest->record;
		
		return $record->canEdit();
	}


    public function baseTransform() {
        $this
            ->setAttribute('data-icon', 'accept')
            ->setAttribute('data-icon-alternate', 'addpage')
            ->setAttribute('data-text-alternate', _t('CMSMain.SAVEDRAFT', 'Save draft'));
    }
}


class BetterButton_Publish extends BetterButton implements BetterButton_Versioned {

	public function __construct(Form $form, GridFieldDetailForm_ItemRequest $request) {
        parent::__construct('publish',_t('SiteTree.BUTTONSAVEPUBLISH', 'Save & publish'), $form, $request);
	}


	public function shouldDisplay() {
		$record = $this->gridFieldRequest->record;
		
		return $record->canEdit();
	}


	public function baseTransform() {
		parent::baseTransform();
       	$this
            ->setAttribute('data-icon', 'accept')
            ->setAttribute('data-icon-alternate', 'disk')
            ->setAttribute('data-text-alternate', _t('SiteTree.BUTTONSAVEPUBLISH', 'Save & publish'));

	}


	public function transformToButton() {
		$published = $this->gridFieldRequest->recordIsPublished();
		if($published) {
			$this->setTitle(_t('SiteTree.BUTTONPUBLISHED', 'Published'));
		}
		if($this->gridFieldRequest->record->stagesDiffer('Stage','Live') && $published) {
			$this->addExtraClass('ss-ui-alternate');

		}
	}
}


class BetterButton_PublishAndAdd extends BetterButton_SaveAndAdd implements BetterButton_Versioned {

	public function __construct(Form $form, GridFieldDetailForm_ItemRequest $request) {
		return parent::__construct('doPublishAndAdd', _t('GridFieldDetailForm.PUBLISHANDADD','Publish and add new'), $form, $request);
	}

	public function shouldDisplay() {
		$record = $this->gridFieldRequest->record;
		
		return $record->canEdit() && $record->canCreate();
	}

}


class BetterButton_PublishAndClose extends BetterButton_SaveAndClose implements BetterButton_Versioned {

	public function __construct(Form $form, GridFieldDetailForm_ItemRequest $request) {
		return parent::__construct('doPublishAndQuit', _t('GridFieldDetailForm.PUBLISHANDQUITE','Publish and close'), $form, $request);
	}

	public function shouldDisplay() {
		$record = $this->gridFieldRequest->record;
		
		return $record->canEdit();
	}

}


class BetterButton_Rollback extends BetterButton implements BetterButton_Versioned {

	public function __construct(Form $form, GridFieldDetailForm_ItemRequest $request) {
		parent::__construct('rollback', _t('SiteTree.BUTTONCANCELDRAFT','Cancel draft changes'), $form, $request);
	}


	public function baseTransform() {
		parent::baseTransform();
		$this
			->setDescription(_t(
                            'SiteTree.BUTTONCANCELDRAFTDESC',
                            'Delete your draft and revert to the currently published page'
            ));
	}

	
	public function shouldDisplay() {
		return $this->gridFieldRequest->record->stagesDiffer('Stage','Live') && $this->gridFieldRequest->recordIsPublished() && $this->gridFieldRequest->record->canEdit();
	}
}


class BetterButton_Unpublish extends BetterButton implements BetterButton_Versioned {

	public function __construct(Form $form, GridFieldDetailForm_ItemRequest $request) {
		parent::__construct('unpublish', _t('SiteTree.BUTTONUNPUBLISH', 'Unpublish'), $form, $request);
	}



	public function baseTransform() {	
		parent::baseTransform();
		$this->addExtraClass('ss-ui-action-destructive');		
	}


	public function shouldDisplay() {	
		return $this->gridFieldRequest->recordIsPublished() && $this->gridFieldRequest->record->canEdit();
	}
}


class BetterButton_CustomAction extends BetterButton {

	public function __construct($name, $title = null, Form $form, GridFieldDetailForm_ItemRequest $request) {
		parent::__construct($name, $title, $form, $request);
	}
}
