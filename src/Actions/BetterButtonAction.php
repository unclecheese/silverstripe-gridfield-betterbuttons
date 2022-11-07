<?php

namespace UncleCheese\BetterButtons\Actions;

use SilverStripe\Core\Convert;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\GridField\GridFieldDetailForm_ItemRequest;
use SilverStripe\Forms\LiteralField;
use UncleCheese\BetterButtons\Extensions\BetterButtonGroupable;
use UncleCheese\BetterButtons\Interfaces\BetterButtonInterface;

/**
 * Defines the base class for all buttons that link to arbitrary endpoints
 * from a {@link GridFieldDetailForm}
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class BetterButtonAction extends LiteralField implements BetterButtonInterface
{
    private static $extensions = array(
        BetterButtonGroupable::class
    );

    /**
     * The form that this action is associated with
     * @var Form
     */
    protected $form;

    /**
     * The text to appear on the button
     * @var string
     */
    protected $buttonText;

    /**
     * The name of the button, used just for uniqueness
     * @var string
     */
    protected $buttonName;

    /**
     * The request that is associated with the gridfield
     * @var GridFieldDetailForm_ItemRequest
     */
    protected $gridFieldRequest;

    /**
     * Builds the action
     * @param string $text The text to appear on the button
     */
    public function __construct($text = null)
    {
        $this->buttonText = $text;
        parent::__construct($this->getButtonName(), "");
    }

    /**
     * Bind the button to the GridField request
     * @param Form $form
     * @param GridFieldDetailForm_ItemRequest $request
     */
    public function bindGridField(Form $form, GridFieldDetailForm_ItemRequest $request)
    {
        $this->setForm($form);
        $this->gridFieldRequest = $request;

        return $this;
    }

    /**
     * Get the name of the button. Arbitrary.
     * @return string
     */
    public function getButtonName()
    {
        $raw = $this->buttonName ?: $this->getButtonText();

        return preg_replace('/[^a-z0-9-_]/', '', strtolower($this->getButtonText() ?? ""));
    }

    /**
     * A noop that gets the link for the button
     * @return string
     */
    public function getButtonLink()
    {
    }

    /**
     * Determines if the button should display
     * @return bool
     */
    public function shouldDisplay()
    {
        return true;
    }

    /**
     * Gets the HTML representing the button
     * @return string
     */
    public function getButtonHTML()
    {
        return sprintf(
            '<a class="%s" href="%s" %s>%s</a>',
            $this->getButtonClasses(),
            $this->getButtonLink(),
            // Prevent outputting the 'class' attribute twice by excluding it from other attributes
            $this->getAttributesHTML('class'),
            $this->getButtonText()
        );
    }

    /**
     * Combines classes from $this->extraClass() with a couple of additional classes if they are
     * applicable for this button.
     * @return string
     */
    private function getButtonClasses()
    {
        $classes = $this->extraClass();
        if ($this->isGrouped()) {
            return $classes; //Do not return the below additional classes
        }
        $classes .= ' btn btn-default ss-ui-button';
        if ($this->getAttribute('target') != '_blank') {
            // Only add this class if this link is targeted inside the CMS. Any links targeted to a new browser
            // window/tab should not have this as the CMS JavaScript would hook to the 'onclick' event and load the
            // content via AJAX to the CMS, which could cause problems.
            $classes .= ' cms-panel-link';
        }

        return $classes;
    }

    /**
     * Gets the text for the button
     * @return string
     */
    public function getButtonText()
    {
        return $this->buttonText;
    }

    /**
     * Sets the confirm text
     * @param  $str
     * @return  BetterButtonAction
     */
    public function setConfirmation($str)
    {
        return $this->setAttribute('data-confirm', Convert::raw2att($str));
    }

    /**
     * Generates the button. Updates the literal field with the correct HTML
     * based on any post-contruct updates
     *
     * @param array $attributes
     * @return SSViewer
     */
    public function FieldHolder($attributes = array ())
    {
        if ($this->shouldDisplay()) {
            $this->setContent($this->getButtonHTML());
            return parent::FieldHolder($attributes);
        }
    }
}
