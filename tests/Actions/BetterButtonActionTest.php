<?php

namespace UncleCheese\BetterButtons\Tests\Actions;

use SilverStripe\Dev\SapphireTest;
use UncleCheese\BetterButtons\Actions\BetterButtonAction;

class BetterButtonActionTest extends SapphireTest
{
    /**
     * Test that the button name (or button text) is sanitized and returned as lowercase
     *
     * @dataProvider buttonNameProvider
     * @param string $buttonName
     * @param string $expected
     */
    public function testGetButtonName($buttonName, $expected)
    {
        $field = new BetterButtonAction($buttonName);
        $this->assertSame($expected, $field->getButtonName());
    }

    /**
     * @return array[]
     */
    public function buttonNameProvider()
    {
        return [
            [
                'MyGenericButton123',
                'mygenericbutton123'
            ],
            [
                '!@#$%^&*()',
                ''
            ],
            [
                '#better!button#',
                'betterbutton'
            ]
        ];
    }
}
