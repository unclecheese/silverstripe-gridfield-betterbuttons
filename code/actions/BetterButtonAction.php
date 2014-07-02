<?php

class BetterButtonAction extends LiteralField {

    protected $form;
    

    protected $buttonText;
    

    protected $buttonName;


    protected $gridFieldRequest;


    public function __construct($text, Form $form, GridFieldDetailForm_ItemRequest $request) {
        $this->buttonText = $text;
        $this->gridFieldRequest = $request;
        $this->form = $form;

        parent::__construct($this->getButtonName(), "");        
    }


    public function getButtonName() {
        $raw = $this->buttonName ?: $this->getButtonText();

        return preg_replace('/[^a-z0-9-_]/','', strtolower($this->getButtonText()));
    }


    public function getButtonLink() { }


    public function shouldDisplay() {
        return true;
    }


    public function getButtonHTML() {
        return sprintf(
            '<a class="ss-ui-button cms-panel-link %s" href="%s">%s</a>',
            $this->extraClass(),
            $this->getButtonLink(),
            $this->getButtonText()
        );
    }


    public function getButtonText() {
        return $this->buttonText;
    }


    public function FieldHolder($attributes = array ()) {
        if($this->shouldDisplay()) {
            $this->setContent($this->getButtonHTML());
            return parent::FieldHolder($attributes);
        }
    }
}
