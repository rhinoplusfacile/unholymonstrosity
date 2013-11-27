<?php
namespace dvinci\table\output;
/**
 * Description of OutputEngine
 *
 * @author Will Heyser
 */
abstract class OutputEngine
{
	/** @var \dvinci\table\DataTable */
	protected $table;
	/** @var resource */
	protected $handle;
	/** @var bool */
	protected $return_value;
	
	public function __construct(\dvinci\table\DataTable $table, $handle = null, $return_value=false)
	{
		$this->table = $table;
		if(!isset($handle) || !is_resource($handle) || get_resource_type($handle) !== 'file')
		{
			if(!$return_value)
			{
				$handle = fopen('php://output', 'w');
			}
		}
		$this->handle = $handle;
		$this->return_value = $return_value;
	}
	
	public function output($string)
	{
		if($this->return_value)
		{
			return $string;
		}
		else
		{
			fwrite($this->handle, $string);
			fflush($this->handle);
		}
	}
}

?>