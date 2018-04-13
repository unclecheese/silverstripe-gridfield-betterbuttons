<?php

namespace UncleCheese\BetterButtons\Buttons;

use SilverStripe\Core\Injector\Injectable;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\GridField\GridFieldDetailForm_ItemRequest;
use UncleCheese\BetterButtons\Traits\Groupable;
use UncleCheese\BetterButtons\Interfaces\BetterButtonInterface;

/**
 * Defines the base class for all buttons that submit form data through GridField
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
abstract class Button extends FormAction implements BetterButtonInterface
{
    use Groupable;
    use Injectable;

    /**
     * @var array
     */
    private static $dependencies = [
        'GridFieldRequest' => '%$' . GridFieldDetailForm_ItemRequest::class . '.betterbuttons',
    ];

    /**
     * The request that points to the GridFieldDetailForm
     * @var GridFieldDetailForm_ItemRequest
     */
    protected $gridFieldRequest;

    /**
     * Performs any last-minute transformations to the button in accordance with anything
     * the user may have done after instantiating the button
     *
     * @return Button
     */
    public function baseTransform()
    {
        return $this;
    }

    /**
     * Tells the form action to become a standard form submit button
     * @return FormAction
     */
    public function transformToButton()
    {
        $this->baseTransform();

        return $this->setUseButtonTag(true);
    }

    /**
     * Tells the form action to become a standard input tag, e.g. for usage in a button group
     * @return FormAction
     */
    public function transformToInput()
    {
        $this->baseTransform();

        return $this;
    }

    /**
     * Determines if the button should display or not
     * @return bool
     */
    public function shouldDisplay()
    {
        return true;
    }

    /**
     * Render the field with the correct attributes
     * @param array $properties
     * @return  string
     */
    public function Field($properties = array())
    {
        if ($this->isGrouped()) {
            $this->transformToInput();
        } else {
            $this->transformToButton();
        }

        return parent::Field($properties);
    }

    /**
     * @param GridFieldDetailForm_ItemRequest $request
     * @return $this
     */
    public function setGridFieldRequest(GridFieldDetailForm_ItemRequest $request)
    {
        $this->gridFieldRequest = $request;

        return $this;
    }

    /**
     * @return GridFieldDetailForm_ItemRequest
     */
    public function getGridFieldRequest()
    {
        return $this->gridFieldRequest;
    }
}
