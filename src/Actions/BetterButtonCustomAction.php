<?php

/**
 * Defines an arbitrary action that can be taken from a grid field detail form
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-better-buttons
 */
class BetterButtonCustomAction extends BetterButtonAction
{
    /**
     * @constant A symbol representing the "go back" behaviour
     */
    const GOBACK = 1;

    /**
     * @constant A symbol representing the "refresh" behaviour
     */
    const REFRESH = 2;

    /**
     * The name of the action (e.g. a method) to take on the model
     * @var string
     */
    protected $actionName;

    /**
     * The type of redirect, see GOBACK and REFRESH constants
     * @var int
     */
    protected $redirectType;

    /**
     * The redirect URL. Overrides $redirectType
     *
     * @var string
     */
    protected $redirectURL;

    /**
     * Builds the button
     * @param string                          $actionName   The name of the action (method)
     * @param string                          $text         The text for the button
     * @param int                             $redirectType The type of redirection on completion of the action
     */
    public function __construct($actionName, $text, $redirectType = null)
    {
        $this->actionName = $actionName;
        $this->redirectType = $redirectType ?: self::REFRESH;

        parent::__construct($text);
    }

    /**
     * Button name in this case has to be predictable so we can find it in a set
     * to call a custom action
     *
     * @return string
     */
    public function getButtonName()
    {
        return $this->actionName;
    }

    /**
     * Sets the behaviour on completion of the action, either refresh or go back to list.
     * See self::GOBACK and self::REFRESH constants
     *
     * @param int $type
     */
    public function setRedirectType($type)
    {
        if (!in_array($type, array(self::GOBACK, self::REFRESH))) {
            throw new Exception(
                "Redirect type must use either the GOBACK or REFRESH constants on BetterButtonCustomAction"
            );
        }

        $this->redirectType = $type;

        return $this;
    }

    /**
     * Gets the redirect type
     * @return int
     */
    public function getRedirectType()
    {
        return $this->redirectType;
    }

    /**
     * Sets the redirect URL. Overrides $redirectType;
     *
     * @param string $url
     */
    public function setRedirectURL($url)
    {
        $this->redirectURL = $url;

        return $this;
    }

    /**
     * Gets the redirect URL
     * @return string
     */
    public function getRedirectURL()
    {
        return $this->redirectURL;
    }

    /**
     * Gets the link for the button
     * @return string
     */
    public function getButtonLink()
    {
        $link = Controller::join_links(
            'customaction',
            $this->actionName
        );
        return $this->gridFieldRequest->Link($link);
    }
}
