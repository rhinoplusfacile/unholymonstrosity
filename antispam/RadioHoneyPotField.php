<?php
namespace um;
require_once $_SERVER['DOCUMENT_ROOT'] . '/core/load.php';
require_once Load::package('antispam', 'MultiOptionHoneyPotField');
require_once Load::package('random_string', 'RandomString');
require_once Load::helper('random');

/**
 * Description of RadioHoneyPotField
 *
 * @author Will Heyser
 */
class RadioHoneyPotField extends MultiOptionHoneyPotField
{
	public function output()
	{
		$retval = array();
		$options = $this->getOptions();
		foreach($options as $option)
		{
			$id = $this->generateId($option);
			$retval[] = self::getLabel($id, $option) . '<input type="radio" name="' . $this->name . '" id="' . $id . '" value="' . $option . '" />';
		}
		return '<fieldset><legend>These fields are not supposed to be filled out by humans. If you can see them, please do not fill them out.</legend>' . PHP_EOL . implode('<br />' . PHP_EOL, $retval) . '</fieldset>';
	}
}

?>