<?php

namespace UncleCheese\BetterButtons\Tests\Actions;

use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\FieldList;
use UncleCheese\BetterButtons\Buttons\SaveAndClose;
use UncleCheese\BetterButtons\Interfaces\BetterButtonInterface;
use UncleCheese\BetterButtons\Tests\Extensions\Stubs\ButtonDataObject;

class BetterButtonDataObjectTest extends SapphireTest
{
    protected $usesDatabase = true;

    protected $extraDataObjects = [
        ButtonDataObject::class
    ];

    /**
     * Nest the configuration so that we can play around with it
     *
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();
        Config::nest();
    }

    /**
     * Test that the getBetterButtonsActions method returns a FieldList containing the configured actions for
     * the DataObject
     */
    public function testGetBetterButtonActions()
    {
        Config::inst()->update('BetterButtonsActions', 'create', [
            'BetterButton_SaveAndClose' => true
        ]);

        Config::inst()->update('BetterButtonsActions', 'delete', [
            'BetterButton_Delete' => false
        ]);

        $object = new ButtonDataObject;
        $result = $object->getBetterButtonsActions();

        $this->assertInstanceOf(FieldList::class, $result);

        $this->assertInstanceOf(SaveAndClose::class, $result->fieldByName('action_doSaveAndQuit'));
        $this->assertNull($result->fieldByName('action_doDelete'));
    }

    /**
     * Test that all fields in the button FieldList are instances of BetterButtonInterface. Uses the default
     * configuration from _config/config.yml
     */
    public function testAllButtonsImplementInterface()
    {
        $object = new ButtonDataObject;
        $fields = $object->getBetterButtonsActions();
        $this->assertContainsOnlyInstancesOf(BetterButtonInterface::class, $fields);
    }

    /**
     * Test that the instantiateButton method throws an exception if the button could not be created, essentially
     * if there is no injector mapping for the given button name
     *
     * @expectedException Exception
     * @expectedExceptionMessage The button type DonkeyLlamaChild doesn't exist.
     */
    public function testInstantiateButtonThrowsExceptionOnInvalidButtonClass()
    {
        Config::inst()->update('BetterButtonsActions', 'create', [
            'MicroHamsterPidgeon' => false, // Will pass, since it's not enabled
            'DonkeyLlamaChild' => true
        ]);

        $object = new ButtonDataObject;
        $object->getBetterButtonsActions();
    }

    /**
     * ButtonDataObject is not versioned, so test that checkVersioned reports that too
     */
    public function testButtonDataObjectIsNotVersioned()
    {
        $this->assertFalse((new ButtonDataObject)->checkVersioned());
    }

    /**
     * Un-nest the configuration - we're finished playing
     *
     * {@inheritDoc}
     */
    public function tearDown()
    {
        Config::unnest();
        parent::tearDown();
    }
}
