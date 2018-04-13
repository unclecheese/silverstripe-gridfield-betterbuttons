<?php

namespace UncleCheese\BetterButtons\FormFields;

use Exception;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\GridField\GridFieldDetailForm_ItemRequest;
use SilverStripe\Forms\Tab;
use SilverStripe\Forms\TabSet;
use UncleCheese\BetterButtons\Actions\Action;
use UncleCheese\BetterButtons\Buttons\Button;
use UncleCheese\BetterButtons\Traits\Groupable;
use UncleCheese\BetterButtons\Interfaces\BetterButtonInterface;

/**
 * Defines the button that holds several form actions and exposes them on click
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 */
class DropdownFormAction extends TabSet implements BetterButtonInterface
{
    use Groupable;

    /**
     * @var Tab
     */
    protected $tab;

    /**
     * @var GridFieldDetailForm_ItemRequest
     */
    protected $gridFieldRequest;

    /**
     * DropdownFormAction constructor.
     * @param string $name
     * @param array $buttons
     */
    public function __construct($name = 'DropdownButtons', $buttons = [])
    {
        parent::__construct($name);
        $this->tab = new Tab(
            'MoreOptions',
            _t(SiteTree::class . '.MoreOptions', 'More options', 'Expands a view for more buttons')
        );
        $this->tab->addExtraClass('popover-actions-simulate');
        $this->push($this->tab);
        $this->addExtraClass('ss-ui-action-tabset action-menus noborder');

        $this->addButtons($buttons);
    }

    /**
     * @param array $buttons
     * @return $this
     */
    public function addButtons($buttons = [])
    {
        foreach ($buttons as $button) {
            $this->tab->push($button);
        }

        return $this;
    }

    /**
     * Determines if the button should display
     * @return boolean
     */
    public function shouldDisplay()
    {
        foreach ($this->Tabs() as $tab) {
            foreach ($tab->children as $child) {
                /* @var BetterButtonInterface $child */
                if ($child->shouldDisplay()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Binds to the GridField request, and transforms the buttons
     * @param GridFieldDetailForm_ItemRequest $request
     * @return $this
     * @throws Exception if instances of BetterButton are not passed
     */
    public function setGridFieldRequest(GridFieldDetailForm_ItemRequest $request)
    {
        $this->gridFieldRequest = $request;

        foreach ($this->children as $child) {
            if (!$child instanceof Button && !$child instanceof Action) {
                throw new Exception('DropdownFormAction must be passed instances of BetterButton');
            }

            $child->setGridFieldRequest($request);
            $child->setIsGrouped(true);

            if ($child instanceof FormAction) {
                $child->setUseButtonTag(true);
            }
        }

        return $this;
    }

    /**
     * @return GridFieldDetailForm_ItemRequest
     */
    public function getGridFieldRequest()
    {
        return $this->gridFieldRequest;
    }
}
