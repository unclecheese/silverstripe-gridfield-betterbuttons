<?php


class BetterButtonDataObject extends DataExtension
{

    public function getBetterButtonsActions($form, $request) {
        $buttons = $this->getDefaultButtonList("BetterButtonsActions");                
        $actions = $this->createFieldList($buttons, $form, $request);

        $this->owner->extend('updateBetterButtonsActions', $actions, $form, $request);

        return $actions;
    }


    public function getBetterButtonsUtils($form, $request) {
        $buttons = $this->getDefaultButtonList("BetterButtonsUtils");
        $utils = $this->createFieldList($buttons, $form, $request);

        $this->owner->extend('updateBetterButtonsUtils', $utils, $form, $request);

        return $utils;
    }


    protected function getDefaultButtonList($config) {
        $new = ($this->owner->ID == 0);
        $list = $new ?
            Config::inst()->get($config, $this->checkVersioned() ? "versioned_create" : "create") :
            Config::inst()->get($config, $this->checkVersioned() ? "versioned_edit" : "edit");

        return $list ?: array ();
    }    


    protected function createFieldList($buttons, $form, $request) {
        $actions = FieldList::create();        
        foreach($buttons as $buttonType => $bool) {
            if(!$bool || !$buttonType) continue;
            
            if(substr($buttonType, 0, 6) == "Group_") {
                $group = $this->createButtonGroup(substr($buttonType, 6));
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
            throw new Exception("The button type $b doesn't exist.");
        }        
    }


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


    public function isCustomActionAllowed($action) {
        $actions = $this->owner->config()->better_buttons_actions;
        if($actions) {
            return in_array($action, $actions);    
        }

        return false;
        
    }

}