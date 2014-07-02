<?php


class BetterButtonLink extends BetterButtonAction {

    protected $link;


    public function __construct($text, $link) {
        parent::__construct($text, null, null);
        $this->link = $link;
    }


    public function getButtonLink() {
        return $this->link;
    }
}