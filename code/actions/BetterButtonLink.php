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
     * Builds the button
     * @param string $text
     * @param string $link
     */
    public function __construct($text, $link) {
        parent::__construct($text, null, null);
        $this->link = $link;
    }


    /**
     * Gets the link for the button
     * @return string
     */
    public function getButtonLink() {
        return $this->link;
    }
}