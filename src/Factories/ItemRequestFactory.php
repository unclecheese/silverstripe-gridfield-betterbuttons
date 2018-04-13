<?php

namespace UncleCheese\BetterButtons\Factories;

use SilverStripe\Core\Injector\Factory;
use UncleCheese\BetterButtons\Extensions\BetterButtons;

class ItemRequestFactory implements Factory
{
    public function create($service, array $params = array())
    {
        return BetterButtons::getGridFieldRequest();
    }
}