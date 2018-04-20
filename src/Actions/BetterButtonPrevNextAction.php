<?php

namespace UncleCheese\BetterButtons\Actions;

use SilverStripe\Control\Controller;
use UncleCheese\BetterButtons\Actions\BetterButtonAction;

/**
 * Defines a set of buttons that offers prev/next navigation from within a
 * GridField detail form
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class BetterButtonPrevNextAction extends BetterButtonAction
{
    /**
     * Gets the HTML for the button
     * @return string
     */
    public function getButtonHTML()
    {
        $html = '<div class="btn-group" role="group">';

        // Prev/next links. Todo: This doesn't scale well.

        // Check if the gridfield as been filtered
        $params = array(
            'q' => (array)$this->gridFieldRequest->getRequest()->getVar('q')
        );

        $searchVars = (bool)$params ? '?' . http_build_query($params) : '';

        $previousRecordID = $this->gridFieldRequest->getPreviousRecordID();
        $cssClass = $previousRecordID ? "cms-panel-link" : "disabled";
        $prevLink = $previousRecordID ? Controller::join_links($this->gridFieldRequest->gridField->Link(), "item", $previousRecordID . $searchVars) : "javascript:void(0);";
        $linkTitle = $previousRecordID ? _t('GridFieldBetterButtons.PREVIOUSRECORD', 'Go to the previous record') : "";
        $linkText = $previousRecordID ? _t('GridFieldBetterButtons.PREVIOUS', 'Previous') : "";

        $html .= sprintf(
            "<a class='ss-ui-button btn btn-default gridfield-better-buttons-prevnext gridfield-better-buttons-prev %s' href='%s' title='%s'>%s</a>",
            $cssClass,
            $prevLink,
            $linkTitle,
            $linkText
        );

        $nextRecordID = $this->gridFieldRequest->getNextRecordID();
        $cssClass = $nextRecordID ? "cms-panel-link" : "disabled";
        $nextLink = $nextRecordID ? Controller::join_links($this->gridFieldRequest->gridField->Link(), "item", $nextRecordID . $searchVars) : "javascript:void(0);";

        $linkTitle = $nextRecordID ? _t('GridFieldBetterButtons.NEXTRECORD', 'Go to the next record') : "";
        $linkText = $nextRecordID ? _t('GridFieldBetterButtons.NEXT', 'Next') : "";

        $html .= sprintf(
            "<a class='ss-ui-button btn btn-default gridfield-better-buttons-prevnext gridfield-better-buttons-next %s' href='%s' title='%s'>%s</a>",
            $cssClass,
            $nextLink,
            $linkTitle,
            $linkText
        );

        $html .= '</div>';

        return $html;
    }
}
