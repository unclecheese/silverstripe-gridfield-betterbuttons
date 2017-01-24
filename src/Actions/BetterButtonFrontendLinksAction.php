<?php

namespace UncleCheese\BetterButtons\Actions;

use SilverStripe\Control\Controller;
use UncleCheese\BetterButtons\Actions\BetterButtonAction;

/**
 * Defines the button that provides links to the frontend from within a gridfield detail form.
 * detail form. Only works if your DataObject has a Link() method.
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class BetterButtonFrontendLinksAction extends BetterButtonAction
{
    /**
     * Gets the link for the button
     * @return string
     */
    public function getButtonLink()
    {
        return $this->gridFieldRequest->record->hasMethod('Link') ? $this->gridFieldRequest->record->Link() : "";
    }

    /**
     * Determines if the button should display
     * @return bool
     */
    public function shouldDisplay()
    {
        return $this->gridFieldRequest->record && $this->gridFieldRequest->record->hasMethod('Link');
    }

    /**
     * Generates the HTML that represents the button
     * @return string
     */
    public function getButtonHTML()
    {
        $link = $this->getButtonLink();

        $stageLink = Controller::join_links($link, '?stage=Stage');
        $liveLink = Controller::join_links($link, '?stage=Live');

        return '<span class="better-buttons-frontend-links">
                    <a class="better-buttons-frontend-link" target="_blank" href="' . $stageLink . '">'
                        ._t('GridFieldBetterButtons.VIEWONDRAFTSITE', 'Draft site').
                    '</a> |
                    <a class="better-buttons-frontend-link" target="_blank" href="' . $liveLink . '">'.
                        _t('GridFieldBetterButtons.VIEWONLIVESITE', 'Live site').
                    '</a></span>';
    }
}
