<?php


class BetterButtonPrevNextAction extends BetterButtonAction {
    
    public function __construct(Form $form, GridFieldDetailForm_ItemRequest $request) {
        parent::__construct(
            null,
            $form, 
            $request
        );
    }

    public function getButtonHTML() {        
        $html = "";

        // Prev/next links. Todo: This doesn't scale well.

        $previousRecordID = $this->gridFieldRequest->getPreviousRecordID();
        $cssClass = $previousRecordID ? "cms-panel-link" : "disabled";
        $prevLink = $previousRecordID ? Controller::join_links($this->gridFieldRequest->gridField->Link(),"item", $previousRecordID) : "javascript:void(0);";
        $linkTitle = $previousRecordID ? _t('GridFieldBetterButtons.PREVIOUSRECORD','Go to the previous record') : "";
        $linkText = $previousRecordID ? _t('GridFieldBetterButtons.PREVIOUS','Previous') : "";

        $html .= sprintf(
                "<a class='ss-ui-button gridfield-better-buttons-prevnext gridfield-better-buttons-prev %s' href='%s' title='%s'><img src='".BETTER_BUTTONS_DIR."/images/prev.png' alt='previous'  /> %s</a>",
                $cssClass,
                $prevLink,
                $linkTitle,
                $linkText
        );


        $nextRecordID = $this->gridFieldRequest->getNextRecordID();
        $cssClass = $nextRecordID ? "cms-panel-link" : "disabled";
        $prevLink = $nextRecordID ? Controller::join_links($this->gridFieldRequest->gridField->Link(),"item", $nextRecordID) : "javascript:void(0);";

        $linkTitle = $nextRecordID ? _t('GridFieldBetterButtons.NEXTRECORD','Go to the next record') : "";
        $linkText = $nextRecordID ? _t('GridFieldBetterButtons.NEXT','Next') : "";

        $html .= sprintf(
                "<a class='ss-ui-button gridfield-better-buttons-prevnext gridfield-better-buttons-prev %s' href='%s' title='%s'>%s <img src='".BETTER_BUTTONS_DIR."/images/next.png' alt='next'  /></a>",
                $cssClass,
                $prevLink,
                $linkTitle,
                $linkText
        );

        return $html;
    }
}