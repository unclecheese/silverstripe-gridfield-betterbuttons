<?php

class BetterButtonCancelAction extends BetterButtonAction {

    public function __construct(Form $form, GridFieldDetailForm_ItemRequest $request) {
        parent::__construct(
            _t('GridFieldBetterButtons.CANCEL','Cancel'),
            $form, 
            $request
        );
    }


    public function getButtonLink() {
        return $this->gridFieldRequest->Link("cancel");
    }


    public function getButtonHTML() {
        $this->addExtraClass("backlink");

        return parent::getButtonHTML($form, $request);
    }
}
