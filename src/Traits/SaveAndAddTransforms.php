<?php

namespace UncleCheese\BetterButtons\Traits;

use SilverStripe\Forms\FormAction;

trait SaveAndAddTransforms
{
    /**
     * Adds the appropriate style and icon
     * @return FormAction
     */
    public function transformToButton()
    {
        return parent::transformToButton()
            ->addExtraClass("ss-ui-action-constructive")
            ->setAttribute('data-icon', 'add');
    }

    /**
     * Adds a class so the button can be identified in a group
     * @return FormAction
     */
    public function transformToInput()
    {
        return parent::transformToInput()
            ->addExtraClass("saveAndAddNew");
    }

}