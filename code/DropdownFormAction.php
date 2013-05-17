<?php



class DropdownFormAction extends CompositeField {

	
	protected static $instance_count = 0;	



	public function __construct($title = null, $children = array ()) {
		$this->Title = $title;
		foreach($children as $c) {
			$c->setUseButtonTag(true);
		}
		parent::__construct($children);
		self::$instance_count++;
	}



	public function Field($properties = array ()) {		
		Requirements::css(BETTER_BUTTONS_DIR.'/css/dropdown_form_action.css');
		Requirements::javascript(BETTER_BUTTONS_DIR.'/javascript/dropdown_form_action.js');
		$this->setAttribute('data-form-action-dropdown','#'.$this->DropdownID());		
		return parent::Field();
	}




	public function DropdownID() {
		return 'form-action-dropdown-'.self::$instance_count;
	}


}