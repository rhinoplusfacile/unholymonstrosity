<?php
namespace um;
require_once $_SERVER['DOCUMENT_ROOT'] . '/core/load.php';
require_once Load::package('set', 'Set');
/**
 * Description of CannedReport
 *
 * @author Will Heyser
 */
class CannedReport
{
	private $key;
	/** @var \um\Set */
	private $values = array();

	public function __construct($key)
	{
		$this->key = $key;
	}

	public function addValue($value)
	{
		if(!isset($this->values[$value]))
		{
			$this->values[$value] = 0;
		}
		$this->values[$value]++;
	}
}

?>