<?php

namespace UncleCheese\BetterButtons\Actions;

use UncleCheese\BetterButtons\Actions\BetterButtonAction;

/**
 * Defines the a button that can contain an arbitrary link, e.g. an external one
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class BetterButtonLink extends BetterButtonAction
{
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
    public function __construct($text, $link)
    {
        parent::__construct($text);
        $this->link = $link;
    }

    /**
     * Gets the link for the button
     * @return string
     */
    public function getButtonLink()
    {
        return $this->link;
    }

    /**
     * Makes the link to open to a new tab. If not used, the CMS will try to load the link via an AJAX request, which
     * can cause problems if the link target is not a page inside the CMS.
     *
     * @param bool $enable True if omitted
     */
    public function newWindow($enable = true)
    {
        $this->setAttribute('target', $enable ? '_blank' : '');
    }
}
