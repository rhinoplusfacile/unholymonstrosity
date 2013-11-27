<?php
namespace um;
require_once $_SERVER['DOCUMENT_ROOT'] . '/core/load.php';
Load::helper('debug');

/**
 * Description of DBParam
 *
 * @author Will Heyser
 */
class DBParam
{
	private $type;
	private $val;

	public function __construct($type, &$val)
	{
		$this->type = $type;
		$this->val =& $val;
	}

	public function getType()
	{
		return $this->type;
	}

	public function getVal()
	{
		return $this->val;
	}
}

?>