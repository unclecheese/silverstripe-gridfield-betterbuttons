<?php
/**
 * Created by Nivanka Fonseka (nivanka@silverstripers.com).
 * User: nivankafonseka
 * Date: 1/29/16
 * Time: 9:20 AM
 * To change this template use File | Settings | File Templates.
 */

class BetterButton_NewMultiClass extends BetterButton
{

	private $classes = array();

	public function __construct() {
		parent::__construct("doNewClass", _t('GridFieldBetterButtons.NEWRECORD','New record'));
	}

	public function setClasses($classes)
	{
		$this->classes = $classes;
	}

	public function classDropdown()
	{
		$dropdown = DropdownField::create(sprintf('%s[ClassName]', __CLASS__), '')
			->setSource($this->classes)
			->addExtraClass('add-new-selected')
			->setEmptyString(_t('GridFieldDetailForm.SELECTCLASS', 'Select Class'));
		return $dropdown->Field();
	}

	/**
	 * Add the necessary classes and icons
	 * @return FormAction
	 */
	public function baseTransform() {
		parent::baseTransform();

		return $this
			->setDisabled(true)
			->addExtraClass("ss-ui-action-constructive")
			->setAttribute('data-icon', 'add');
	}


	/**
	 * Determines if the button should show
	 * @return boolean
	 */
	public function shouldDisplay() {
		return $this->gridFieldRequest->record->canCreate();
	}

	public function Field($properties = array ()) {
		$field = parent::Field($properties);
		return $this->classDropdown() . $field;
	}

} 