<?php


namespace UncleCheese\BetterButtons\Buttons;


abstract class Button extends \FormAction {


	protected $request;


	public function transformToButton() {
		return $this->setUseButtonTag(true);
	}



	public function transformToInput() {
		return $this->setUseButtonTag(true);
	}


	public function shouldDisplay() {
		return true;
	}



	public function __construct($name, $title = null, \Form $form, \GridFieldDetailForm_ItemRequest $request) {
		$this->request = $request;
		return parent::__construct($name, $title, $form);
	}
}


class Button_Save extends Button {


	public function __construct(\Form $form, \GridFieldDetailForm_ItemRequest $request) {
		parent::__construct('save',_t('GridFieldDetailForm.SAVE', 'Save'), $form, $request);
		return $this;
	}






	public function transformToButton() {
		return parent::transformToButton()
			->addExtraClass('ss-ui-action-constructive')
			->setAttribute('data-icon','accept')
		;

	}


}





class Button_Cancel extends \LiteralField {


	public function __construct(\Form $form, \GridFieldDetailForm_ItemRequest $request) {
		$link = $request->Link("cancel");
		parent::__construct("doCancel", '<a class="backlink ss-ui-button cms-panel-link" href="'.$link.'">'._t('GridFieldBetterButtons.CANCEL','Cancel').'</a>', $form, $request);
	}




}




class Button_New extends Button {


	public function __construct(\Form $form, \GridFieldDetailForm_ItemRequest $request) {
		parent::__construct("doNew", _t('GridFieldBetterButtons.NEWRECORD','New record'), $form, $request);
		$this
			->addExtraClass("ss-ui-action-constructive")
			->setAttribute('data-icon', 'add')
		;

	}
}



class Button_Delete extends Button {

	public function __construct(\Form $form, \GridFieldDetailForm_ItemRequest $request) {
		parent::__construct('doDelete', _t('GridFieldDetailForm.Delete', 'Delete'), $form, $request);
		$this->request = $request;
		\Requirements::javascript(BETTER_BUTTONS_DIR.'/javascript/gridfield_betterbuttons_delete.js');
		$form->Actions()->push(
			\LiteralField::create('cancelDelete', "<a class='gridfield-better-buttons-undodelete ss-ui-button' href='javascript:void(0)'>"._t('GridFieldDetailForm.CANCELDELETE', 'No. Don\'t delete')."</a>")
		);

		return $this
			->setUseButtonTag(true)
			->addExtraClass('gridfield-better-buttons-delete')
			->setAttribute("data-toggletext", _t('GridFieldBetterButtons.AREYOUSURE','Yes. Delete this item.'))
		;

	}


	public function shouldDisplay() {
		return !$this->request->recordIsPublished();
	}




}



class Button_SaveAndAdd extends Button {


	public function __construct(\Form $form, \GridFieldDetailForm_ItemRequest $request) {
		parent::__construct("doSaveAndAdd",_t('GridFieldBetterButtons.SAVEANDADDNEW','Save and add new'), $form, $request);
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

	public function __construct(\Form $form, \GridFieldDetailForm_ItemRequest $request) {
		parent::__construct("doSaveAndQuit", _t('GridFieldDetailForm.SAVEANDCLOSE', 'Save and close'), $form, $request);
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


	public function __construct(\Form $form, \GridFieldDetailForm_ItemRequest $request) {
		parent::__construct("doSaveAndNext", _t('GridFieldDetailForm.SAVEANDNEXT','Save and go to next record'), $form, $request);

		if(!$request->getNextRecordID()) {
			$this->setDisabled(true);
		}

		return $this;
	}


	public function transformToInput() {
		return parent::transformToInput()
			->addExtraClass("saveAndGoNext")
		;
	}



}



class Button_SaveAndPrev extends Button {

	public function __construct(\Form $form, \GridFieldDetailForm_ItemRequest $request) {
		parent::__construct("doSaveAndPrev", _t('GridFieldDetailForm.SAVEANDPREV','Save and go to previous record'), $form, $request);

		if(!$request->getPreviousRecordID()) {
			$this->setDisabled(true);
		}

		return $this;
	}


	public function transformToInput() {
		return parent::transformToInput()
			->addExtraClass("saveAndGoPrev")
		;
	}


}




interface Button_Versioned {}


class Button_SaveDraft extends Button implements Button_Versioned {
	public function __construct(\Form $form, \GridFieldDetailForm_ItemRequest $request) {
        parent::__construct('save', _t('SiteTree.BUTTONSAVED', 'Saved'), $form, $request);
        $this
            ->setAttribute('data-icon', 'accept')
            ->setAttribute('data-icon-alternate', 'addpage')
            ->setAttribute('data-text-alternate', _t('CMSMain.SAVEDRAFT', 'Save draft'))
         ;

         return $this;
    }

}


class Button_Publish extends Button implements Button_Versioned {

	public function __construct(\Form $form, \GridFieldDetailForm_ItemRequest $request) {
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


class Button_PublishAndAdd extends Button_SaveAndAdd implements Button_Versioned {

	public function __construct(\Form $form, \GridFieldDetailForm_ItemRequest $request) {
		return parent::__construct('doPublishAndAdd', _t('GridFieldDetailForm.PUBLISHANDADD','Publish and add new'), $form, $request);
	}
}



class Button_PublishAndClose extends Button_SaveAndClose implements Button_Versioned {

	public function __construct(\Form $form, \GridFieldDetailForm_ItemRequest $request) {
		return parent::__construct('doPublishAndQuit', _t('GridFieldDetailForm.PUBLISHANDQUITE','Publish and close'), $form, $request);

	}
}



class Button_Rollback extends Button implements Button_Versioned {


	public function __construct(\Form $form, \GridFieldDetailForm_ItemRequest $request) {
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



	public function shouldDisplay() {
		return $this->request->record->stagesDiffer('Stage','Live') && $this->request->recordIsPublished();
	}
}



class Button_Unpublish extends Button implements Button_Versioned {


	public function __construct(\Form $form, \GridFieldDetailForm_ItemRequest $request) {
		parent::__construct('unpublish', _t('SiteTree.BUTTONUNPUBLISH', 'Unpublish'), $form, $request);
		$this->addExtraClass('ss-ui-action-destructive')
        ;

        return $this;
	}


}

class Button_FrontendLinks extends \LiteralField {


	public function __construct(\Form $form, \GridFieldDetailForm_ItemRequest $request) {
		$link = $request->record->hasMethod('Link') ? $request->record->Link() : "";
		parent::__construct(
			"draft_link",
			'<span class="better-buttons-frontend-links"><a class="better-buttons-frontend-link" target="_blank" href="'.$link.'?stage=Stage">'._t('GridFieldBetterButtons.VIEWONDRAFTSITE','Draft site').'</a> |
			<a class="better-buttons-frontend-link" target="_blank" href="'.$link.'?stage=Live">'._t('GridFieldBetterButtons.VIEWONLIVESITE','Live site').'</a></span>'
		);
		$this->setRequest($request);
	}



	public function shouldDisplay() {
		return $this->request->record->hasMethod('Link');
	}



}


class Button_PrevNext extends \LiteralField {

	public function __construct(\Form $form, \GridFieldDetailForm_ItemRequest $request) {
			$html = "";

			// Prev/next links. Todo: This doesn't scale well.
			$previousRecordID = $request->getPreviousRecordID();
			$cssClass = $previousRecordID ? "cms-panel-link" : "disabled";
			$prevLink = $previousRecordID ? \Controller::join_links($request->gridField->Link(),"item", $previousRecordID) : "javascript:void(0);";
			$linkTitle = $previousRecordID ? _t('GridFieldBetterButtons.PREVIOUSRECORD','Go to the previous record') : "";

			$html .= sprintf(
					"<a class='ss-ui-button gridfield-better-buttons-prevnext gridfield-better-buttons-prev %s' href='%s' title='%s'><img src='".BETTER_BUTTONS_DIR."/images/prev.png' alt='previous'  /> Previous</a>",
					$cssClass,
					$prevLink,
					$linkTitle
			);

			$nextRecordID = $request->getNextRecordID();
			$cssClass = $nextRecordID ? "cms-panel-link" : "disabled";
			$prevLink = $nextRecordID ? \Controller::join_links($request->gridField->Link(),"item", $nextRecordID) : "javascript:void(0);";
			$linkTitle = $nextRecordID ? _t('GridFieldBetterButtons.NEXTRECORD','Go to the next record') : "";


			$html .= sprintf(
					"<a class='ss-ui-button gridfield-better-buttons-prevnext gridfield-better-buttons-prev %s' href='%s' title='%s'>Next <img src='".BETTER_BUTTONS_DIR."/images/next.png' alt='next'  /></a>",
					$cssClass,
					$prevLink,
					$linkTitle
			);

			parent::__construct("prev_next", $html);
	}

}