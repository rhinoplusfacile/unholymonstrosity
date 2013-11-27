<?php
namespace um;
require_once 'HoneyPotField.php';
/**
 * Description of SubmitHoneyPotField
 *
 * @author Will Heyser
 */
class SubmitHoneyPotField extends HoneyPotField
{
	public function output()
	{
		return '<input type="submit" name="' . $this->name . '" value="This button should not be clicked by humans." />';
	}
}

?>