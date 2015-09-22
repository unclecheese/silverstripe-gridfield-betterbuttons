<?php

/**
 * Defines the a button that can contain an arbitrary link, e.g. an external one
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class BetterButtonLink extends BetterButtonAction {


    /**
     * The link, absolute or relative
     * @var string
     */
    protected $link;
    
    /**
     * Should the link open in a new window?
     * @var bool
     */
    protected $newWindow = false;


    /**
     * Builds the button
     * @param string $text
     * @param string $link
     * @param bool $newWindow
     */
    public function __construct($text, $link, $newWindow = false) {
        parent::__construct($text);
        $this->link = $link;
        $this->newWindow = $newWindow;
    }


    /**
     * Gets the link for the button
     * @return string
     */
    public function getButtonLink() {
        return $this->link;
    }

    /**
     * Gets the HTML representing the button
     * @return string
     */
    public function getButtonHTML()
    {
        if($this->newWindow) {
            return sprintf(
                '<a class="ss-ui-button %s" href="%s" target="_blank">%s</a>',
                $this->extraClass(), $this->getButtonLink(), $this->getButtonText()
            );
        }
        return parent::getButtonHTML();
    }
}
