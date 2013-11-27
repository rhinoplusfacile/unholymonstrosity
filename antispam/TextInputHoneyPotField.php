<?php
namespace um;
require_once $_SERVER['DOCUMENT_ROOT'] . '/core/load.php';
require_once Load::package('antispam', 'HoneyPotField');

/**
 * Description of TextInputHoneyPotField
 *
 * @author Will Heyser
 */
class TextInputHoneyPotField extends HoneyPotField
{
	public function output()
	{
		$id = $this->generateId();
		return self::getLabel($id) . '<input type="text" name="' . $this->name . '" id="' . $id . '" value="" />';
	}
}

?>