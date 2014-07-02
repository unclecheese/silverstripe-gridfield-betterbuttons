<?php


class BetterButtonFrontendLinksAction extends BetterButtonAction {

    public function __construct(Form $form, GridFieldDetailForm_ItemRequest $request) {
        parent::__construct(
            null,
            $form, 
            $request
        );
    }


    public function getButtonLink() {
        return $this->gridFieldRequest->record->hasMethod('Link') ? $this->gridFieldRequest->record->Link() : "";        
    }


    public function shouldDisplay() {
        return $this->gridFieldRequest->record && $this->gridFieldRequest->record->hasMethod('Link');
    }

    
    public function getButtonHTML() {
        $link = $this->getButtonLink();

        return '<span class="better-buttons-frontend-links">
                    <a class="better-buttons-frontend-link" target="_blank" href="'.$link.'?stage=Stage">'
                        ._t('GridFieldBetterButtons.VIEWONDRAFTSITE','Draft site').
                    '</a> |
                    <a class="better-buttons-frontend-link" target="_blank" href="'.$link.'?stage=Live">'.
                        _t('GridFieldBetterButtons.VIEWONLIVESITE','Live site').
                    '</a></span>';

    }
}
