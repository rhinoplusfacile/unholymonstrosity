<?php
namespace PIMS\admin\cme_template;
/**
 * Description of Table
 *
 * @author Will Heyser
 */
class Table
{
	private $name;
	private $join_on;
	private $fields = array();

	public function __construct($name, $join_on)
	{
		$this->name = $name;
		$this->join_on = $join_on;
	}

	public function addField($name, $alias, $value)
	{

	}
}

?>