<?php


/**
 * An extension that offers features to DataObjects that allow them to set their own
 * actions and utilities for {@link GridFieldDetailForm}
 *
 * Default buttons are defined in _config.yml and can be overriden via the Config layer.
 * Due to the way Config merges arrays, set button class names to "false" to remove them from the list.
 * 
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class BetterButtonDataObject extends DataExtension {

    
    /**
     * Gets the default actions for all DataObjects. Can be overloaded in subclasses
     * <code>
     *  public function getBetterButtonsActions($form, $request) {
     *      $actions = parent::getBetterButtonsActions($form, $request);
     *      $actions->push(BetterButtonCustomAction::create('myaction','Do something to this record'));
     *
     *      return $actions;
     *  }
     * </code>
     * 
     * @param  Form                            $form    The form that contains this button
     * @param  GridFieldDetailForm_ItemRequest $request The request that points to the form
     * @return FieldList
     */
    public function getBetterButtonsActions($form, $request) {
        $buttons = $this->getDefaultButtonList("BetterButtonsActions");                
        $actions = $this->createFieldList($buttons, $form, $request);

        $this->owner->extend('updateBetterButtonsActions', $actions, $form, $request);

        return $actions;
    }


    /**
     * Gets a FormAction or BetterButtonCustomAction by name, in utils or actions
     * @param  Form $form    
     * @param  GridFieldDetailForm_ItemRequest $request
     * @param  string $action  The name of the action to find
     * @return FormAction
     */
    public function findActionByName($form, $request, $action) {
        $actions = $this->owner->getBetterButtonsActions($form, $request);
        $formAction = $actions->fieldByName($action);
        if(!$formAction) {
            $utils = $this->owner->getBetterButtonsUtils($form, $request);
            $formAction = $utils->fieldByName($action);
        }

        return $formAction;
    }    


    /**
     * Gets the default utils for all DataObjects. Can be overloaded in subclasses.
     * Utils are actions that appear in the top of the GridFieldDetailForm
     * <code>
     *  public function getBetterButtonsUtils($form, $request) {
     *      $utils = parent::getBetterButtonsUtils($form, $request);
     *      $utils->push(BetterButtonCustomAction::create('myaction','Do something to this record'));
     *
     *      return $utils;
     *  }
     * </code>
     * 
     * @param  Form                            $form    The form that contains this button
     * @param  GridFieldDetailForm_ItemRequest $request The request that points to the form
     * @return FieldList
     */
    public function getBetterButtonsUtils($form, $request) {
        $buttons = $this->getDefaultButtonList("BetterButtonsUtils");
        $utils = $this->createFieldList($buttons, $form, $request);

        $this->owner->extend('updateBetterButtonsUtils', $utils, $form, $request);

        return $utils;
    }


    /**
     * Gets an array of all the default buttons as defined in the config
     * @param  [type] $config [description]
     * @return [type]         [description]
     */
    protected function getDefaultButtonList($config) {
        $new = ($this->owner->ID == 0);
        $list = $new ?
            Config::inst()->get($config, $this->checkVersioned() ? "versioned_create" : "create") :
            Config::inst()->get($config, $this->checkVersioned() ? "versioned_edit" : "edit");

        return $list ?: array ();
    }    


    /**
     * Transforms a list of configured buttons into a usable FieldList
     * @param  array                            $buttons An array of class names
     * @param  Form                             $form    The form that will contain the buttons
     * @param  GridFieldDetailForm_ItemRequest $request The request that points to the form
     * @return FieldList
     */
    protected function createFieldList($buttons, $form, $request) {
        $actions = FieldList::create();        
        foreach($buttons as $buttonType => $bool) {
            if(!$bool || !$buttonType) continue;
            
            if(substr($buttonType, 0, 6) == "Group_") {
                $group = $this->createButtonGroup(substr($buttonType, 6), $form, $request);
                if($group->children->exists()) {
                    $actions->push($group);
                }
            }
            else if($b = $this->instantiateButton($buttonType, $form, $request)) {
                $actions->push($b);
            }            
        }

        return $actions;
    }


    /**
     * Transforms a given button class name into an actual object.
     * Invokes any necessary methods that need to be called per the configuration
     * @param  string                           $className The class of the button
     * @param  Form                             $form      The form that will contain the button
     * @param  GridFieldDetailForm_ItemRequest  $request   The request that points to the form
     * @param  boolean                          $button    If the button should display as an input tag or a button
     * @return FormField             
     */
    protected function instantiateButton($className, $form, $request, $button = true) {
        if(class_exists($className)) {                    
            $buttonObj = Injector::inst()->create($className, $form, $request);

            if($buttonObj->hasMethod('shouldDisplay') && !$buttonObj->shouldDisplay()) return false;

            if($buttonObj instanceof BetterButton) {
                if(($buttonObj instanceof BetterButton_Versioned) && !$this->checkVersioned()) {
                    return false;
                }                
                return $button ? $buttonObj->transformToButton() : $buttonObj->transformToInput();                
            }

            return $buttonObj;
        }
        else {
            throw new Exception("The button type $className doesn't exist.");
        }        
    }


    /**
     * Creates a button group {@link DropdownFormAction}
     * @param  string                           $groupName The name of the group
     * @param  Form                             $form      The form that will contain the group
     * @param  GridFieldDetailForm_ItemRequest $request   The request that points to the form
     * @return DropdownFormAction
     */
    protected function createButtonGroup($groupName, $form, $request) {        
        $groupConfig = Config::inst()->get("BetterButtonsGroups", $groupName);
        $label = (isset($groupConfig['label'])) ? $groupConfig['label'] : $groupName;
        $buttons = (isset($groupConfig['buttons'])) ? $groupConfig['buttons'] : array ();
        $button = DropdownFormAction::create(_t('GridFieldBetterButtons.'.$groupName, $label));
        foreach($buttons as $b => $bool) {              
            if($bool) {
                if($child = $this->instantiateButton($b, $form, $request)) {
                    $button->push($child);
                }
            }
        }

        return $button;
    }


    /**
     * Determines if the record is using the {@link Versioned} extension
     * @return boolean
     */
    public function checkVersioned() {
        $exts = $this->owner->getExtensionInstances();
        if($exts) {
            foreach($exts as $e) {
                if($e instanceof Versioned) {
                    return true;
                }
            }
        }
        return false;
    }


    /**
     * Checks if a custom action is allowed to be called against a model.
     * Prevents security risk of calling arbitrary public methods on the DataObject.
     * 
     * Looks at:
     * <code>
     *     private static $better_buttons_actions = array ()
     * </code>
     * 
     * @param  string  $action The name of the action
     * @return boolean
     */
    public function isCustomActionAllowed($action) {
        $actions = $this->owner->config()->better_buttons_actions;
        if($actions) {
            return in_array($action, $actions);    
        }

        return false;
        
    }

}