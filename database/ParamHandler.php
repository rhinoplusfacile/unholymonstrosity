<?php
namespace um;
require_once $_SERVER['DOCUMENT_ROOT'] . '/core/load.php';
Load::helper('debug');
Load::package('database', 'DBParam');

/**
 * Description of ParamHandler
 *
 * @author Will Heyser
 */
class ParamHandler
{
	private $params = array();
	private $typestring;
	private $param_array;
	private $recache = false;

	public function __construct()
	{
	}

	public function addParam($type, &$val)
	{
		$this->params[] = new DBParam($type, &$val);
		$this->recache = true;
	}

	private function cache()
	{
		if($this->recache == true)
		{
			$this->typestring = '';
			$this->param_array = array();
			foreach($this->params as $param)
			{
				$this->typestring .= $param->getType();
				$this->param_array[] =& $param->getVal();
			}
			$this->recache = false;
		}
	}

	public function getParams()
	{
		$this->cache();
		return $this->param_array;
	}

	public function getTypeString()
	{
		$this->cache();
		return $this->typestring;
	}
}

?>