<?php
namespace um;
require_once $_SERVER['DOCUMENT_ROOT'] . '/core/load.php';
require_once Load::package('antispam', 'MultiOptionHoneyPotField');
require_once Load::package('random_string', 'RandomString');
require_once Load::helper('random');
/**
 * Description of SelectHoneyPotField
 *
 * @author Will Heyser
 */
class SelectHoneyPotField extends MultiOptionHoneyPotField
{
	private static function applyValues($options)
	{
		$values = range(1, count($options));
		shuffle($values);
		$retval = array();
		for($i = 0; $i < count($options) && $i < count($values); $i++)
		{
			$retval[$values[$i]] = $options[$i];
		}
		return $retval;
	}

	protected function getOptions()
	{
		return self::applyValues(parent::getOptions());
	}

	public function output()
	{
		$retval = array('<option></option>');
		$options = $this->getOptions();
		foreach($options as $value=>$option)
		{
			$retval[] = '<option value="' . $value . '" />' . $option . '</option>';
		}
		$id = $this->generateId();
		return self::getLabel($id) . '<select name=' . $this->name . ' id="' . $id . '">' . PHP_EOL . implode(PHP_EOL, $retval) . '</select>';
	}
}

?>