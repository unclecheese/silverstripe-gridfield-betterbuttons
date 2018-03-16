<?php

namespace UncleCheese\BetterButtons\FormFields;

use Exception;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\GridField\GridFieldDetailForm_ItemRequest;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\View\Requirements;
use UncleCheese\BetterButtons\Actions\Action;
use UncleCheese\BetterButtons\Buttons\Button;
use UncleCheese\BetterButtons\Traits\Groupable;
use UncleCheese\BetterButtons\Interfaces\BetterButtonInterface;

/**
 * Defines the button that holds several form actions and exposes them on click
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class DropdownFormAction extends CompositeField implements BetterButtonInterface
{
    use Groupable;

    /**
     * To ensure the buttons get unique ids, keep track of the instances
     * @var integer
     */
    protected static $instance_count = 0;

    /**
     * A unique identifier assigned through $instance_count
     * @var string
     */
    protected $identifier;

    /**
     * Builds the button
     * @param string $title    The text for the button
     * @param array  $children Child buttons (FormActions)
     */
    public function __construct($title = null, $children = array ())
    {
        $this->Title = $title;
        foreach ($children as $c) {
            if ($c instanceof FormAction) {
                $c->setUseButtonTag(true);
            }
        }
        parent::__construct($children);
        self::$instance_count++;
        $this->identifier = self::$instance_count;
    }

    /**
     * Renders the button, includes the JS and CSS
     * @param array $properties
     * @return DBHTMLText
     */
    public function Field($properties = [])
    {
        Requirements::css('unclecheese/betterbuttons:css/dropdown_form_action.css');
        Requirements::javascript('unclecheese/betterbuttons:javascript/dropdown_form_action.js');
        $this->setAttribute('data-form-action-dropdown', '#' . $this->DropdownID());

        return parent::Field();
    }

    /**
     * A unique id for the dropdown button
     *
     * @return  string
     */
    public function DropdownID()
    {
        return 'form-action-dropdown-' . $this->identifier;
    }

    /**
     * Determines if the button should display
     * @return boolean
     */
    public function shouldDisplay()
    {
        foreach ($this->children as $child) {
            /* @var BetterButtonInterface $child */
            if ($child->shouldDisplay()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Binds to the GridField request, and transforms the buttons
     * @param Form $form
     * @param GridFieldDetailForm_ItemRequest $request
     * @return $this
     * @throws Exception if instances of BetterButton are not passed
     */
    public function bindGridField(Form $form, GridFieldDetailForm_ItemRequest $request)
    {
        $this->setForm($form);
        $this->gridFieldRequest = $request;

        foreach ($this->children as $child) {
            if (!$child instanceof Button && !$child instanceof Action) {
                throw new Exception("DropdownFormAction must be passed instances of BetterButton");
            }

            $child->bindGridField($form, $request);
            $child->setIsGrouped(true);

            if ($child instanceof FormAction) {
                $child->setUseButtonTag(true);
            }
        }

        return $this;
    }
}
