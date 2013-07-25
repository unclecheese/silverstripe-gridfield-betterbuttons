<?php


namespace UncleCheese\BetterButtons\Buttons;


abstract class Button extends \FormAction {


	public function configureFromForm(\Form $form, \GridFieldDetailForm_ItemRequest $request) {

	}


	public function transformToButton() {
		return $this->setUseButtonTag(true);
	}



	public function transformToInput() {
		return $this->setUseButtonTag(true);
	}


	public function shouldDisplay(\Form $form, \GridFieldDetailForm_ItemRequest $request) {
		return true;
	}
}


class Button_Save extends Button {

	
	public function __construct() {
		parent::__construct('save',_t('GridFieldDetailForm.SAVE', 'Save'));
		return $this;
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




class Button_New extends Button {


	public function __construct() {
		parent::__construct("doNew", _t('GridFieldBetterButtons.NEWRECORD','New record'));
		$this			
			->setAttribute('data-icon', 'add')
		;

	}
}



class Button_Delete extends Button {

	public function __construct() {
		parent::__construct('doDelete', _t('GridFieldDetailForm.Delete', 'Delete'));
		\Requirements::javascript(BETTER_BUTTONS_DIR.'/javascript/gridfield_betterbuttons_delete.js');		
		return $this
			->setUseButtonTag(true)
			->addExtraClass('gridfield-better-buttons-delete')
			->setAttribute("data-toggletext", _t('GridFieldBetterButtons.AREYOUSURE','Yes. Delete this item.'))
		;
		
	}


	public function shouldDisplay(\Form $form, \GridFieldDetailForm_ItemRequest $request) {
		return !$request->recordIsPublished();
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




interface Button_Versioned {}


class Button_SaveDraft extends Button implements Button_Versioned {
	public function __construct() {	
        parent::__construct('save', _t('SiteTree.BUTTONSAVED', 'Saved'));
        $this
            ->setAttribute('data-icon', 'accept')
            ->setAttribute('data-icon-alternate', 'addpage')
            ->setAttribute('data-text-alternate', _t('CMSMain.SAVEDRAFT', 'Save draft'))
         ;

         return $this;
    }

}


class Button_Publish extends Button implements Button_Versioned {

	public function __construct() {
        parent::__construct('publish',_t('SiteTree.BUTTONSAVEPUBLISH', 'Save & publish'));
       	$this
            ->setAttribute('data-icon', 'accept')
            ->setAttribute('data-icon-alternate', 'disk')
            ->setAttribute('data-text-alternate', _t('SiteTree.BUTTONSAVEPUBLISH', 'Save & publish'))
        ;

        return $this;
		
	}


	public function configureFromForm(\Form $form, \GridFieldDetailForm_ItemRequest $request) {
		$published = $request->recordIsPublished();
		if($published) {
			$this->setTitle(_t('SiteTree.BUTTONPUBLISHED', 'Published'));			
		}
		if($request->record->stagesDiffer('Stage','Live') && $published) {
			$this->addExtraClass('ss-ui-alternate');

		}
	}

}


class Button_PublishAndAdd extends Button_SaveAndAdd implements Button_Versioned {

	public function __construct() {
		return parent::__construct()
			->setName('doPublishAndAdd')
			->setTitle(_t('GridFieldDetailForm.PUBLISHANDADD','Publish and add new'))
		;
	}
}



class Button_PublishAndClose extends Button_SaveAndClose implements Button_Versioned {

	public function __construct() {
		return parent::__construct()
			->setName('doPublishAndQuit')
			->setTitle(_t('GridFieldDetailForm.PUBLISHANDQUITE','Publish and close'))
		;
	}
}



class Button_Rollback extends Button implements Button_Versioned {


	public function __construct() {
		parent::__construct('rollback', _t('SiteTree.BUTTONCANCELDRAFT','Cancel draft changes'));
		$this
			->setDescription(_t(
                            'SiteTree.BUTTONCANCELDRAFTDESC',
                            'Delete your draft and revert to the currently published page'
            ))
        ;

        return $this;
	}



	public function shouldDisplay(\Form $form, \GridFieldDetailForm_ItemRequest $request) {
		return $request->record->stagesDiffer('Stage','Live') && $request->recordIsPublished();
	}
}



class Button_Unpublish extends Button implements Button_Versioned {


	public function __construct() {
		parent::__construct('unpublish', _t('SiteTree.BUTTONUNPUBLISH', 'Unpublish'));
		$this->addExtraClass('ss-ui-action-destructive')
        ;		

        return $this;
	}


}
