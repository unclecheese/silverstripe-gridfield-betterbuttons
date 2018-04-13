<?php

namespace UncleCheese\BetterButtons\Tests\Extensions\Stubs;

use SilverStripe\Dev\TestOnly;
use SilverStripe\ORM\DataObject;
use UncleCheese\BetterButtons\Extensions\BetterButtons;

/**
 * A mock DataObject that has the BetterButtonDataObject extension applied, for testing
 */
class ButtonDataObject extends DataObject implements TestOnly
{
    private static $table_name = 'TestButtonDataObject';

    private static $extensions = [
        BetterButtons::class
    ];
}
