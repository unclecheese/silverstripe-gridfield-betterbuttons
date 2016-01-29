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
     * Enable better buttons for this DataObject
     *
     * @config
     * @var bool
     */
    private static $better_buttons_enabled = true;
    
    /**
     * Gets the default actions for all DataObjects. Can be overloaded in subclasses
     * <code>
     *  public function getBetterButtonsActions() {
     *      $actions = parent::getBetterButtonsActions();
     *      $actions->push(BetterButtonCustomAction::create('myaction','Do something to this record'));
     *
     *      return $actions;
     *  }
     * </code>
     * 
     * @return FieldList
     */
    public function getBetterButtonsActions() {
        $buttons = $this->getDefaultButtonList("BetterButtonsActions");                
        $actions = $this->createFieldList($buttons);

        $this->owner->extend('updateBetterButtonsActions', $actions);

        return $actions;
    }


    /**
     * Gets a FormAction or BetterButtonCustomAction by name, in utils or actions
     * @param  string $action  The name of the action to find
     * @return FormAction
     */
    public function findActionByName($action) {
        $actions = $this->owner->getBetterButtonsActions();
        $formAction = false;
        
        foreach($actions as $f) {
        	if($formAction) break;
        	
        	if($f instanceof CompositeField) {
        		$formAction = $f->fieldByName($action);        		
        	}
        	else if($f->getName() === $action) {
        		$formAction = $f;
        	}
        }
        
        if(!$formAction) {
            $utils = $this->owner->getBetterButtonsUtils();
            $formAction = $utils->fieldByName($action);
        }

        return $formAction;
    }    


    /**
     * Gets the default utils for all DataObjects. Can be overloaded in subclasses.
     * Utils are actions that appear in the top of the GridFieldDetailForm
     * <code>
     *  public function getBetterButtonsUtils(GridField $grid) {
     *      $utils = parent::getBetterButtonsUtils();
     *      $utils->push(BetterButtonCustomAction::create('myaction','Do something to this record'));
     *
     *      return $utils;
     *  }
     * </code>
     * 
     * @return FieldList
     */
    public function getBetterButtonsUtils(GridField $grid = null) {
		$buttons = $this->getDefaultButtonList("BetterButtonsUtils");

		$multiClassConfig = null;
		if($grid && isset($buttons['BetterButton_New']) && ClassInfo::exists('GridFieldAddNewMultiClass')){
			$config = $grid->getConfig();
			if($multiClassConfig = $config->getComponentByType('GridFieldAddNewMultiClass')){
				$buttons['BetterButton_NewMultiClass'] = 1;
				unset($buttons['BetterButton_New']);
			}
		}


		$utils = $this->createFieldList($buttons);

		if($multiClassConfig){
			foreach($utils as $button){
				if(is_a($button, 'BetterButton_NewMultiClass')){
					$button->setClasses($multiClassConfig->getClasses($grid));
				}
			}
		}

        $this->owner->extend('updateBetterButtonsUtils', $utils);

        return $utils;

    }


    /**
     * Gets an array of all the default buttons as defined in the config
     * @param  array $config
     * @return array
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
     * @return FieldList
     */
    protected function createFieldList($buttons) {
        $actions = FieldList::create();        
        foreach($buttons as $buttonType => $bool) {
            if(!$bool || !$buttonType) continue;
            
            if(substr($buttonType, 0, 6) == "Group_") {
                $group = $this->createButtonGroup(substr($buttonType, 6));
                if($group->children->exists()) {
                    $actions->push($group);
                }
            }
            else if($b = $this->instantiateButton($buttonType)) {
                $actions->push($b);
            }            
        }

        return $actions;
    }


    /**
     * Transforms a given button class name into an actual object.
     * @param  string                           $className The class of the button
     * @param  Form                             $form      The form that will contain the button
     * @param  GridFieldDetailForm_ItemRequest  $request   The request that points to the form
     * @param  boolean                          $button    If the button should display as an input tag or a button
     * @return FormField             
     */
    protected function instantiateButton($className) {
        if(class_exists($className)) {                    
            $buttonObj = Injector::inst()->create($className);
            return $buttonObj;
        }
        else {
            throw new Exception("The button type $className doesn't exist.");
        }        
    }


    /**
     * Creates a button group {@link DropdownFormAction}
     * @param  string                           $groupName The name of the group
     * @return DropdownFormAction
     */
    protected function createButtonGroup($groupName) {        
        $groupConfig = Config::inst()->get("BetterButtonsGroups", $groupName);
        $label = (isset($groupConfig['label'])) ? $groupConfig['label'] : $groupName;
        $buttons = (isset($groupConfig['buttons'])) ? $groupConfig['buttons'] : array ();
        $button = DropdownFormAction::create(_t('GridFieldBetterButtons.'.$groupName, $label));
        foreach($buttons as $b => $bool) {              
            if($bool) {
                if($child = $this->instantiateButton($b)) {
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
        return $this->owner->hasExtension('Versioned') &&
               count($this->owner->getVersionedStages()) > 1;
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
