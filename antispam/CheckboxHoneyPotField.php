<?php
namespace um;
require_once $_SERVER['DOCUMENT_ROOT'] . '/core/load.php';
require_once Load::package('antispam', 'HoneyPotField');
require_once Load::package('random_string', 'RandomString');
/**
 * Description of CheckboxHoneyPotField
 *
 * @author Will Heyser
 */
class CheckboxHoneyPotField extends HoneyPotField
{
	public function output()
	{
		$id = $this->generateId();
		return self::getLabel($id) . '<input type="checkbox" name="' . $this->name . '" id="' . $id . '" value="1" />';
	}
}

?>