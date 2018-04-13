<?php

namespace UncleCheese\BetterButtons\Extensions;

use Exception;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\GridField\GridFieldDetailForm_ItemRequest;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\DataObject;
use SilverStripe\Versioned\Versioned;
use UncleCheese\BetterButtons\Buttons\Button;
use UncleCheese\BetterButtons\FormFields\DropdownFormAction;
use UncleCheese\BetterButtons\Interfaces\BetterButtonInterface;
use InvalidArgumentException;

/**
 * An extension that offers features to DataObjects that allow them to set their own
 * actions and utilities for {@link GridFieldDetailForm}
 *
 * Default buttons are defined in _config.yml and can be overriden via the Config layer.
 * Due to the way Config merges arrays, set button class names to "false" to remove them from the list.
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 * @package  silverstripe-gridfield-betterbuttons
 * @property DataObject|BetterButtons $owner
 */
class BetterButtons extends DataExtension
{
    use Configurable;

    /**
     * Enable better buttons for this DataObject
     *
     * @config
     * @var bool
     */
    private static $better_buttons_enabled = true;

    /**
     * Enable versioned controls like 'Save & Publish' for DataObjects
     * with 'Versioned' extension.
     *
     * Disable this for records where you want a parent DataObject to control the
     * published/unpublished state of its children. ie. User Defined Forms 3.0+.
     *
     * @config
     * @var bool
     */
    private static $better_buttons_versioned_enabled = true;

    /**
     * @var GridFieldDetailForm_ItemRequest
     */
    protected static $currentRequest;

    /**
     * @return GridFieldDetailForm_ItemRequest
     * @throws Exception
     */
    public static function getGridFieldRequest()
    {
        if (!static::$currentRequest) {
            throw new Exception(sprintf(
                '%s::%s no current request handler was available',
                static::class,
                'get_current()'
            ));
        }

        return static::$currentRequest;
    }

    /**
     * @param GridFieldDetailForm_ItemRequest $request
     */
    public static function setGridFieldRequest(GridFieldDetailForm_ItemRequest $request)
    {
        static::$currentRequest = $request;
    }

    /**
     * Gets the default actions for all DataObjects. Can be overloaded in subclasses
     * <code>
     *  public function getBetterButtonsActions()
     *  {
     *      $actions = parent::getBetterButtonsActions();
     *      $actions->push(BetterButtonCustomAction::create('myaction','Do something to this record'));
     *
     *      return $actions;
     *  }
     * </code>
     *
     * @return FieldList
     */
    public function getBetterButtonsActions()
    {
        $buttons = $this->getDefaultButtonList('actions');
        $actions = $this->createFieldList($buttons);
        $this->owner->extend('updateBetterButtonsActions', $actions);

        return $actions;
    }

    /**
     * Gets a FormAction or BetterButtonCustomAction by name, in utils or actions
     * @param  string $action  The name of the action to find
     * @return FormAction
     */
    public function findActionByName($action)
    {
        $actions = $this->owner->getBetterButtonsActions();
        $formAction = false;

        foreach ($actions as $f) {
            if ($formAction) {
                break;
            }

            if ($f instanceof CompositeField) {
                $formAction = $f->fieldByName($action);
            } elseif ($f->getName() === $action) {
                $formAction = $f;
            }
        }

        if (!$formAction) {
            $utils = $this->owner->getBetterButtonsUtils();
            $formAction = $utils->fieldByName($action);
        }

        return $formAction;
    }

    /**
     * Gets the default utils for all DataObjects. Can be overloaded in subclasses.
     * Utils are actions that appear in the top of the GridFieldDetailForm
     * <code>
     *  public function getBetterButtonsUtils()
     *  {
     *      $utils = parent::getBetterButtonsUtils();
     *      $utils->push(BetterButtonCustomAction::create('myaction','Do something to this record'));
     *
     *      return $utils;
     *  }
     * </code>
     *
     * @return FieldList
     */
    public function getBetterButtonsUtils()
    {
        $buttons = $this->getDefaultButtonList('utils');
        $utils = $this->createFieldList($buttons);

        $this->owner->extend('updateBetterButtonsUtils', $utils);

        return $utils;
    }

    /**
     * Gets an array of all the default buttons as defined in the config
     * @param  array $config
     * @return array
     */
    protected function getDefaultButtonList($config)
    {
        $new = ($this->owner->ID == 0);
        $buttonConfig = $this->config()->get($config);
        if (!$buttonConfig) {
            return [];
        }

        $key = $new
            ? ($this->checkVersioned() ? 'versioned_create' : 'create')
            : ($this->checkVersioned() ? 'versioned_edit' : 'edit');

        return isset($buttonConfig[$key]) ? (array) $buttonConfig[$key] : [];
    }

    /**
     * Transforms a list of configured buttons into a usable FieldList
     * @param  array $buttons An array of class names
     * @return FieldList
     * @throws  Exception
     */
    protected function createFieldList($buttons)
    {
        $actions = FieldList::create();
        foreach ($buttons as $buttonType => $config) {
            $button = $this->createButtonFromConfig($buttonType, $config);
            if (!$button) {
                continue;
            }
            if ($button instanceof DropdownFormAction) {
                $this->populateButtonGroup($button, $config);
            }
            $actions->push($button);
        }

        return $actions;
    }

    /**
     * Transforms a given button class name into an actual object.
     * @param  string $buttonName The name of the button
     * @return Button
     * @throws Exception If the requested button type does not exist
     */
    protected function instantiateButton($buttonName)
    {
        try {
            return Injector::inst()->create(BetterButtonInterface::class . '.' . $buttonName);
        } catch (Exception $ex) {
            // Customize the default injector exception
            throw new Exception("The button type $buttonName doesn't exist.");
        }
    }

    /**
     * @param string $buttonType
     * @param array $config
     * @return FormAction
     * @throws Exception
     */
    protected function createButtonFromConfig($buttonType, $config)
    {
        if (!$config || !$buttonType) {
            return null;
        }

        $button = $this->instantiateButton($buttonType);

        if (!$button) {
            throw new Exception(sprintf(
                'Unknown button type %s',
                $buttonType
            ));
        }

        return $button;
    }

    /**
     * Creates a button group {@link DropdownFormAction}
     * @param  DropdownFormAction $group
     * @param array $config
     * @return DropdownFormAction
     */
    protected function populateButtonGroup(DropdownFormAction &$group, array $config)
    {
        if (
            !is_array($config) ||
            !isset($config['label']) ||
            !isset($config['buttons']) ||
            !is_array($config['buttons'])
        ) {
            throw new InvalidArgumentException(
                'Button type %s must be passed an array of config that includes "label" and "buttons"',
                $buttonType
            );
        }

        $group->setName($config['label']);
        $children = [];
        foreach ($config['buttons'] as $childName => $childConfig) {
            $child = $this->createButtonFromConfig($childName, $childConfig);
            if ($child instanceof DropdownFormAction) {
                throw new InvalidArgumentException(sprintf(
                    'Nested groups are not allowed. See betterbuttons config for %s',
                    $config['label']
                ));
            }
            $children[] = $child;
        }
        $group->addButtons($children);
    }

    /**
     * Determines if the record is using the {@link Versioned} extension
     * @return boolean
     */
    public function checkVersioned()
    {
        $isVersioned = false;

        foreach ($this->owner->getExtensionInstances() as $extension) {
            if ($extension instanceof Versioned) {
                $isVersioned = true;
                break;
            }
        }

        return $isVersioned
            && $this->owner->config()->better_buttons_versioned_enabled
            && count($this->owner->getVersionedStages()) > 1;
    }

    /**
     * Checks if a custom action is allowed to be called against a model.
     * Prevents security risk of calling arbitrary public methods on the DataObject.
     *
     * Looks at:
     * <code>
     *     private static $better_buttons_actions = array ()
     * </code>
     *
     * @param  string  $action The name of the action
     * @return boolean
     */
    public function isCustomActionAllowed($action)
    {
        $actions = $this->owner->config()->better_buttons_actions;
        if ($actions) {
            return in_array($action, $actions);
        }

        return false;
    }
}
