<?php

namespace UncleCheese\BetterButtons\Interfaces;

use SilverStripe\Forms\Form;
use SilverStripe\Forms\GridField\GridFieldDetailForm_ItemRequest;

/**
 * Core methods and properties that any action or button must offer
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
interface BetterButtonInterface
{
    /**
     * Determines if the button should display
     * @return bool
     */
    public function shouldDisplay();

    /**
     * Binds the action to a GridField edit page
     * @param  Form $form
     * @param  GridFieldDetailForm_ItemRequest $request
     */
    public function bindGridField(Form $form, GridFieldDetailForm_ItemRequest $request);
}
