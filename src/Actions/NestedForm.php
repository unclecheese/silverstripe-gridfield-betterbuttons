<?php

namespace UncleCheese\BetterButtons\Actions;

use SilverStripe\Forms\FieldList;

/**
 * Defines a button that launches a nested form
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-better-buttons
 */
class NestedForm extends CustomAction
{
    /**
     * @var FieldList
     */
    protected $fields;

    /**
     * Builds the button
     * @param string    $actionName The name of the action (method)
     * @param string    $text       The text for the button
     * @param FieldList $fields
     */
    public function __construct($actionName, $text, FieldList $fields)
    {
        $this->fields = $fields;
        $this->addExtraClass('better-button-nested-form');
        parent::__construct($actionName, $text);
    }

    /**
     * Gets the link for the button
     * @return string
     */
    public function getButtonLink()
    {
        $link = 'nestedform?action='.$this->actionName;

        return $this->gridFieldRequest->Link($link);
    }

    /**
     * Gets the field list
     * @return FieldList
     */
    public function getFields()
    {
        return $this->fields;
    }
}
