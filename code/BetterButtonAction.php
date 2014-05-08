<?php

class BetterButtonAction extends LiteralField
{
    protected $form;

    protected $gridFieldRequest;

    public function __construct(Form $form, GridFieldDetailForm_ItemRequest $request)
    {
        $this->gridFieldRequest = $request;
        $this->form = $form;

        parent::__construct(
            $this->getButtonName(), 
            $this->getButtonHTML()
        );        

    }


    public function getButtonName()
    {
        $raw = $this->buttonName ?: $this->getButtonText();

        return preg_replace('/[^a-z0-9-_]/','', strtolower($this->getButtonText()));
    }


    public function getButtonLink() { }


    public function shouldDisplay()
    {
        return true;
    }


    public function getButtonHTML()
    {
        return sprintf(
            '<a class="ss-ui-button cms-panel-link %s" href="%s">%s</a>',
            $this->extraClass(),
            $this->getButtonLink(),
            $this->getButtonText()
        );
    }

    public function getButtonText()
    {
        return $this->buttonText;
    }
}


class BetterButtonAction_Cancel extends BetterButtonAction 
{

    protected $buttonName = "doCancel";

    public function getButtonLink()
    {
        return $this->request->Link("cancel");
    }

    public function getButtonText()
    {
        return _t('GridFieldBetterButtons.CANCEL','Cancel');
    }

    public function getButtonHTML()
    {
        $this->addExtraClass("backlink");

        return parent::getButtonHTML();
    }
}


class BetterButtonAction_FrontendLinks extends BetterButtonAction 
{

    protected $buttonName = "draft_link";

    public function getButtonLink()
    {
        return $this->gridFieldRequest->record->hasMethod('Link') ? $this->gridFieldRequest->record->Link() : "";        
    }

    public function shouldDisplay() 
    {
        return $this->gridFieldRequest->record && $this->gridFieldRequest->record->hasMethod('Link');
    }

    public function getButtonHTML()
    {
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


class BetterButtonAction_PrevNext extends BetterButtonAction 
{
    protected $buttonName = "prev_next";

    public function getButtonHTML()
    {
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