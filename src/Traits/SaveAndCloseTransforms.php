<?php

namespace UncleCheese\BetterButtons\Traits;

use SilverStripe\Forms\FormAction;

trait SaveAndCloseTransforms
{
    /**
     * Adds a class that helps identify the button when in a group
     * @return FormAction
     */
    public function transformToInput()
    {
        return parent::transformToInput()
            ->addExtraClass("saveAndClose");
    }

    /**
     * Adds the correct style and icon
     * @return FormAction
     */
    public function transformToButton()
    {
        return parent::transformToButton()
            ->addExtraClass('btn-primary font-icon-save')
            ->setAttribute('data-icon', 'accept');
    }

}