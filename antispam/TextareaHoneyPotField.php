<?php
namespace um;
require_once 'HoneyPotField.php';
/**
 * Description of TextareaHoneyPotField
 *
 * @author Will Heyser
 */
class TextareaHoneyPotField extends HoneyPotField
{
	public function output()
	{
		$id = $this->generateId();
		return self::getLabel($id) . '<textarea name="' . $this->name . '" id="' . $id . '"></textarea>';
	}
}

?>