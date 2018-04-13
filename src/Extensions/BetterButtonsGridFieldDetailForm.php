<?php

namespace UncleCheese\BetterButtons\Extensions;

use SilverStripe\Control\RequestHandler;
use SilverStripe\Core\Extension;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldDetailForm_ItemRequest;
use SilverStripe\ORM\DataObject;
use SilverStripe\Versioned\RecursivePublishable;
use SilverStripe\Versioned\Versioned;
use UncleCheese\BetterButtons\Controllers\ItemRequest;
use UncleCheese\BetterButtons\Controllers\VersionedItemRequest;

/**
 * Extends {@see GridFieldDetailForm}
 */
class BetterButtonsGridFieldDetailForm extends Extension
{
    /**
     * @param string $class
     * @param GridField $gridField
     * @param DataObject|Versioned $record
     * @param RequestHandler $requestHandler
     */
    public function updateItemRequestClass(&$class, $gridField, $record, $requestHandler)
    {
        if ($record->config()->get('better_buttons_enabled') !== true) {
            return;

        }
        $isVersioned = $record && $record->hasExtension(Versioned::class);
        $isPublishable = $record && $record->hasExtension(RecursivePublishable::class);
        // Conditionally use a versioned item handler if it doesn't already have one.
        if ($record
            && ($isVersioned || $isPublishable)
            && $record->config()->get('versioned_gridfield_extensions')
            && (!$class || !is_subclass_of($class, VersionedItemRequest::class))
        ) {
            $class = VersionedItemRequest::class;
        } else {
            $class = ItemRequest::class;
        }
    }

    /**
     * @param GridFieldDetailForm_ItemRequest $handler
     */
    public function updateItemRequestHandler(GridFieldDetailForm_ItemRequest $handler)
    {
        BetterButtons::setGridFieldRequest($handler);
    }

}
