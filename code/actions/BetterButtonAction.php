<?php

/**
 * Defines the base class for all buttons that link to arbitrary endpoints
 * from a {@link GridFieldDetailForm}
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class BetterButtonAction extends LiteralField {

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
     * @param string                          $text    The text to appear on the button
     * @param Form                            $form    The form that the button appears on
     * @param GridFieldDetailForm_ItemRequest $request The request that points to the form
     */
    public function __construct($text, Form $form, GridFieldDetailForm_ItemRequest $request) {
        $this->buttonText = $text;
        $this->gridFieldRequest = $request;
        $this->form = $form;

        parent::__construct($this->getButtonName(), "");        
    }


    /**
     * Get the name of the button. Arbitrary.
     * @return string
     */
    public function getButtonName() {
        $raw = $this->buttonName ?: $this->getButtonText();

        return preg_replace('/[^a-z0-9-_]/','', strtolower($this->getButtonText()));
    }


    /**
     * A noop that gets the link for the button
     * @return string
     */
    public function getButtonLink() { }


    /**
     * Determines if the button should display
     * @return bool
     */
    public function shouldDisplay() {
        return true;
    }


    /**
     * Gets the HTML representing the button
     * @return string
     */
    public function getButtonHTML() {
        return sprintf(
            '<a class="ss-ui-button cms-panel-link %s" href="%s">%s</a>',
            $this->extraClass(),
            $this->getButtonLink(),
            $this->getButtonText()
        );
    }


    /**
     * Gets the text for the button
     * @return string
     */
    public function getButtonText() {
        return $this->buttonText;
    }


    /**
     * Generates the button. Updates the literal field with the correct HTML
     * based on any post-contruct updates
     *    
     * @param array $attributes
     * @return SSViewer
     */
    public function FieldHolder($attributes = array ()) {
        if($this->shouldDisplay()) {
            $this->setContent($this->getButtonHTML());
            return parent::FieldHolder($attributes);
        }
    }
}
