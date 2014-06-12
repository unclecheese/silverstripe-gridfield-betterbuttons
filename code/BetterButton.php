<?php

interface BetterButton_Versioned {}

abstract class BetterButton extends FormAction 
{


	protected $request;


	public function transformToButton() 
	{
		return $this->setUseButtonTag(true);
	}


	public function transformToInput() 
	{
		return $this->setUseButtonTag(true);
	}


	public function shouldDisplay() 
	{
		return true;
	}


	public function __construct($name, $title = null, Form $form, GridFieldDetailForm_ItemRequest $request) 
	{
		$this->request = $request;
		return parent::__construct($name, $title, $form);
	}
}


class BetterButton_Save extends BetterButton 
{

	public function __construct(Form $form, GridFieldDetailForm_ItemRequest $request) 
	{
		parent::__construct('save',_t('GridFieldDetailForm.SAVE', 'Save'), $form, $request);
		return $this;
	}


	public function transformToButton() 
	{
		return parent::transformToButton()
			->addExtraClass('ss-ui-action-constructive')
			->setAttribute('data-icon','accept')
		;
	}
}


class BetterButton_New extends BetterButton 
{

	public function __construct(Form $form, GridFieldDetailForm_ItemRequest $request) 
	{
		parent::__construct("doNew", _t('GridFieldBetterButtons.NEWRECORD','New record'), $form, $request);

		$this
			->addExtraClass("ss-ui-action-constructive")
			->setAttribute('data-icon', 'add')
		;
	}
}


class BetterButton_Delete extends BetterButton 
{

	public function __construct(Form $form, GridFieldDetailForm_ItemRequest $request) {
		parent::__construct('doDelete', _t('GridFieldDetailForm.Delete', 'Delete'), $form, $request);
		$this->request = $request;
		Requirements::javascript(BETTER_BUTTONS_DIR.'/javascript/gridfield_betterbuttons_delete.js');
		$form->Actions()->push(
			LiteralField::create('cancelDelete', "<a class='gridfield-better-buttons-undodelete ss-ui-button' href='javascript:void(0)'>"._t('GridFieldDetailForm.CANCELDELETE', 'No. Don\'t delete')."</a>")
		);

		return $this
			->setUseButtonTag(true)
			->addExtraClass('gridfield-better-buttons-delete')
			->setAttribute("data-toggletext", _t('GridFieldBetterButtons.AREYOUSURE','Yes. Delete this item.'))
		;
	}


	public function shouldDisplay() {
		return $this->request->record->canDelete() && !$this->request->recordIsPublished();
	}

}


class BetterButton_SaveAndAdd extends BetterButton 
{

	public function __construct(Form $form, GridFieldDetailForm_ItemRequest $request) 
	{
		parent::__construct("doSaveAndAdd",_t('GridFieldBetterButtons.SAVEANDADDNEW','Save and add new'), $form, $request);
		return $this;
	}


	public function transformToButton() 
	{

		return parent::transformToButton()
			->addExtraClass("ss-ui-action-constructive")
			->setAttribute('data-icon', 'add')
		;

	}


	public function transformToInput() 
	{
		return parent::transformToInput()
			->addExtraClass("saveAndAddNew");
	}
}


class BetterButton_SaveAndClose extends BetterButton 
{

	public function __construct(Form $form, GridFieldDetailForm_ItemRequest $request) 
	{
		parent::__construct("doSaveAndQuit", _t('GridFieldDetailForm.SAVEANDCLOSE', 'Save and close'), $form, $request);
		return $this;

	}


	public function transformToInput() 
	{
		return parent::transformToInput()
			->addExtraClass("saveAndClose")
		;
	}


	public function transformToButton() 
	{
		return parent::transformToButton()
			->addExtraClass("ss-ui-action-constructive")
			->setAttribute('data-icon', 'accept')
		;
	}
}


class BetterButton_SaveAndNext extends BetterButton 
{

	public function __construct(Form $form, GridFieldDetailForm_ItemRequest $request) 
	{
		parent::__construct("doSaveAndNext", _t('GridFieldDetailForm.SAVEANDNEXT','Save and go to next record'), $form, $request);

		if(!$request->getNextRecordID()) {
			$this->setDisabled(true);
		}

		return $this;
	}


	public function transformToInput() 
	{
		return parent::transformToInput()
			->addExtraClass("saveAndGoNext")
		;
	}
}


class BetterButton_SaveAndPrev extends BetterButton 
{

	public function __construct(Form $form, GridFieldDetailForm_ItemRequest $request) 
	{
		parent::__construct("doSaveAndPrev", _t('GridFieldDetailForm.SAVEANDPREV','Save and go to previous record'), $form, $request);

		if(!$request->getPreviousRecordID()) {
			$this->setDisabled(true);

		}

		return $this;
	}


	public function transformToInput() 
	{
		return parent::transformToInput()
			->addExtraClass("saveAndGoPrev")
		;
	}
}


class BetterButton_SaveDraft extends BetterButton implements BetterButton_Versioned 
{
	public function __construct(Form $form, GridFieldDetailForm_ItemRequest $request) 
	{
        parent::__construct('save', _t('SiteTree.BUTTONSAVED', 'Saved'), $form, $request);
        $this
            ->setAttribute('data-icon', 'accept')
            ->setAttribute('data-icon-alternate', 'addpage')
            ->setAttribute('data-text-alternate', _t('CMSMain.SAVEDRAFT', 'Save draft'))
         ;

         return $this;
    }

}


class BetterButton_Publish extends BetterButton implements BetterButton_Versioned 
{

	public function __construct(Form $form, GridFieldDetailForm_ItemRequest $request) 
	{
        parent::__construct('publish',_t('SiteTree.BUTTONSAVEPUBLISH', 'Save & publish'), $form, $request);
       	$this
            ->setAttribute('data-icon', 'accept')
            ->setAttribute('data-icon-alternate', 'disk')
            ->setAttribute('data-text-alternate', _t('SiteTree.BUTTONSAVEPUBLISH', 'Save & publish'))
        ;


		$published = $request->recordIsPublished();
		if($published) {

			$this->setTitle(_t('SiteTree.BUTTONPUBLISHED', 'Published'));
		}
		if($request->record->stagesDiffer('Stage','Live') && $published) {
			$this->addExtraClass('ss-ui-alternate');

		}


        return $this;
	}



}


class BetterButton_PublishAndAdd extends BetterButton_SaveAndAdd implements BetterButton_Versioned 
{

	public function __construct(Form $form, GridFieldDetailForm_ItemRequest $request) 
	{
		return parent::__construct('doPublishAndAdd', _t('GridFieldDetailForm.PUBLISHANDADD','Publish and add new'), $form, $request);
	}
}


class BetterButton_PublishAndClose extends BetterButton_SaveAndClose implements BetterButton_Versioned 
{

	public function __construct(Form $form, GridFieldDetailForm_ItemRequest $request) 
	{
		return parent::__construct('doPublishAndQuit', _t('GridFieldDetailForm.PUBLISHANDQUITE','Publish and close'), $form, $request);
	}
}


class BetterButton_Rollback extends BetterButton implements BetterButton_Versioned 
{

	public function __construct(Form $form, GridFieldDetailForm_ItemRequest $request) 
	{
		parent::__construct('rollback', _t('SiteTree.BUTTONCANCELDRAFT','Cancel draft changes'), $form, $request);
		$this
			->setDescription(_t(
                            'SiteTree.BUTTONCANCELDRAFTDESC',
                            'Delete your draft and revert to the currently published page'
            ))
        ;

        $this->request = $request;
        return $this;
	}


	public function shouldDisplay() 
	{
		return $this->request->record->stagesDiffer('Stage','Live') && $this->request->recordIsPublished();
	}
}


class BetterButton_Unpublish extends BetterButton implements BetterButton_Versioned 
{

	public function __construct(Form $form, GridFieldDetailForm_ItemRequest $request) 
	{
		parent::__construct('unpublish', _t('SiteTree.BUTTONUNPUBLISH', 'Unpublish'), $form, $request);
		$this->addExtraClass('ss-ui-action-destructive');

        $this->request = $request;
        return $this;
	}


	public function shouldDisplay() 
	{
		return $this->request->recordIsPublished();
	}
}
