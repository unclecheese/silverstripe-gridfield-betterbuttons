<?php

namespace UncleCheese\BetterButtons\Actions;

use SilverStripe\Core\Manifest\ModuleResourceLoader;
use SilverStripe\Forms\GridField\GridFieldDetailForm_ItemRequest;
use UncleCheese\BetterButtons\Controllers\ItemRequest;
use SilverStripe\Control\Controller;

/**
 * Defines a set of buttons that offers prev/next navigation from within a
 * GridField detail form
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class PrevNext extends Action
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
        $params = [
            'q' => (array)$this->getGridFieldRequest()->getRequest()->getVar('q')
        ];

        $searchVars = (bool)$params ? '?' . http_build_query($params) : '';
        /* @var GridFieldDetailForm_ItemRequest|ItemRequest $gridFieldRequest */
        $gridFieldRequest = $this->getGridFieldRequest();
        $previousRecord = $gridFieldRequest->getPreviousRecord();
        $cssClass = $previousRecord ? 'cms-panel-link' : 'disabled';
        $prevLink = $previousRecord
            ? Controller::join_links(
                $gridFieldRequest->getGridField()->Link(),
                'item',
                $previousRecord->ID . $searchVars
            )
            : 'javascript:void(0);';
        $linkTitle = $previousRecord ? _t('GridFieldBetterButtons.PREVIOUSRECORD', 'Go to the previous record') : "";

        $html .= sprintf(
            "<a class='ss-ui-button btn btn-default gridfield-better-buttons-prevnext gridfield-better-buttons-prev %s' href='%s' title='%s'><img src='%s' alt='previous'></a>",
            $cssClass,
            $prevLink,
            $linkTitle,
            ModuleResourceLoader::singleton()->resolveURL('unclecheese/betterbuttons:images/prev.png')
        );

        $nextRecord = $this->getGridFieldRequest()->getNextRecord();
        $cssClass = $nextRecord ? "cms-panel-link" : "disabled";
        $nextLink = $nextRecord ? Controller::join_links(
            $this->getGridFieldRequest()->getGridField()->Link(),
            'item',
            $nextRecord->ID . $searchVars
        ) : 'javascript:void(0);';

        $linkTitle = $nextRecord ? _t('GridFieldBetterButtons.NEXTRECORD', 'Go to the next record') : "";

        $html .= sprintf(
            "<a class='ss-ui-button btn btn-default gridfield-better-buttons-prevnext gridfield-better-buttons-next %s' href='%s' title='%s'><img src='%s' alt='next'></a>",
            $cssClass,
            $nextLink,
            $linkTitle,
            ModuleResourceLoader::singleton()->resolveURL('unclecheese/betterbuttons:images/next.png')
        );

        $html .= '</div>';

        return $html;
    }
}
