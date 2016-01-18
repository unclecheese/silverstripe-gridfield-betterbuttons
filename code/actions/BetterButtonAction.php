<?php

/**
 * Defines the base class for all buttons that link to arbitrary endpoints
 * from a {@link GridFieldDetailForm}
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class BetterButtonAction extends LiteralField implements BetterButtonInterface {


	private static $extensions = array (
		'BetterButtonGroupable'
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
     * @param string                          $text    The text to appear on the button
     */
    public function __construct($text = null) {
        $this->buttonText = $text;
        parent::__construct($this->getButtonName(), "");        
    }


    /**
     * Bind the button to the GridField request
     * @param Form $form
     * @param GridFieldDetailForm_ItemRequest $request
     */
    public function bindGridField(Form $form, GridFieldDetailForm_ItemRequest $request) {
        $this->setForm($form);
        $this->gridFieldRequest = $request;

        return $this;
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
            '<a class="%s %s" href="%s" %s>%s</a>',
            $this->isGrouped() ? '' : 'ss-ui-button cms-panel-link',
            $this->extraClass(),
            $this->getButtonLink(),
            $this->getAttributesHTML(),
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
