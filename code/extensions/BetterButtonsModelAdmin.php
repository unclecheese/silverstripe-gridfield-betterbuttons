<?php

/**
 * Decorates {@link ModelAdmin} to show all relevant records,
 * including unpublished Versioned DataObjects
 * even if in Live reading mode.
 *
 * @author  Isaiah Keepin <isaiah@bluehousegroup.com>
 * @package  silverstripe-gridfield-betterbuttons
 *
 * */
class BetterButtonsModelAdmin extends Extension {
    public function updateEditForm(&$form) {
        $origStage = Versioned::current_stage();
        if($origStage != "Stage") {
            Versioned::reading_stage('Stage');
            $fields = $form->Fields();
            foreach($fields as &$field) {
                if($field->class == "GridField") {
                    $stage_data = Versioned::get_by_stage($this->owner->modelClass, "Stage");
                    $field->setList($stage_data);
                }
            }
            Versioned::reading_stage($origStage);
        }
    }
}