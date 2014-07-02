<?php


class BetterButtonCustomAction extends BetterButtonAction {
    
    const GOBACK = 1;

    const REFRESH = 2;

    protected $actionName;

    protected $redirectType;


    public function __construct($actionName, $text, Form $form, GridFieldDetailForm_ItemRequest $request, $redirectType = null) {
        $this->actionName = $actionName;
        $this->redirectType = $redirectType ?: self::REFRESH;

        parent::__construct($text, $form, $request);
    }


    public function setRedirectType($type) {
        if(!in_array($type, array(self::GOBACK, self::REFRESH))) {
            throw new Exception("Redirect type must use either the GOBACK or REFRESH constants on BetterButtonCustomAction");
        }

        $this->redirectType = $type;

        return $this;
    }

    public function getButtonLink() {
        $link = Controller::join_links('customaction',$this->actionName,"?redirectType=".$this->redirectType);
        return $this->gridFieldRequest->Link($link);
    }
}
