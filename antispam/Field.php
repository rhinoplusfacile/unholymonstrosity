<?php
namespace um;
require_once $_SERVER['DOCUMENT_ROOT'] . '/core/load.php';
require_once Load::package('antispam', 'SpamFilter');
/**
 * Base field class.
 *
 * @author Will Heyser
 */
abstract class Field
{
	/** @var string */
	protected $name;
	/** @var \um\SpamFilter */
	protected $filter_link;

	public function __construct($name)
	{
		$this->name = $name;
	}

	public function setFilterLink(SpamFilter $filter)
	{
		$this->filter_link = $filter;
	}

	abstract public function output();
	abstract public function test();

	public function __toString()
	{
		return $this->output();
	}
}

?>