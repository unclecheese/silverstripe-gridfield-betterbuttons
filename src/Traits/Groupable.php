<?php

namespace UncleCheese\BetterButtons\Traits;

use SilverStripe\View\ViewableData;

/**
 * Injects the "isGrouped" flag into Actions and Buttons
 *
 * @author Uncle Cheese <unclecheese@leftandmain.com>
 * @property ViewableData $owner
 */
trait Groupable
{
    /**
     * Is the button part of a group, e.g. DropdownFormAction
     * @var boolean
     */
    protected $isGrouped = false;

    /**
     * Getter for the $isGrouped bool
     * @return boolean
     */
    public function isGrouped()
    {
        return $this->isGrouped;
    }

    /**
     * Sets the $isGrouped flag
     * @param bool $bool
     * @return ViewableData
     */
    public function setIsGrouped($bool)
    {
        $this->isGrouped = $bool;

        return $this->owner;
    }
}
