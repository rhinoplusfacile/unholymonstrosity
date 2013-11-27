<?php
namespace um;
require_once $_SERVER['DOCUMENT_ROOT'] . '/core/load.php';
require_once Load::package('set', 'Set');
require_once Load::package('random_string', 'RandomString');

/**
 * Description of IDStore
 *
 * @author Will Heyser
 */
class IDStore
{
	/** @var \um\Set */
	private $store;

	public function __construct()
	{
		$this->store = new Set;
	}

	public function getId($name='')
	{
		if($name)
		{
			$name = preg_replace('/[^a-z]/i', '', $name);
		}
		else
		{
			$name = '' . new RandomString(RandomString::LOWER, array(4, 10));
		}
		$name .= new RandomString(RandomString::LOWER, 3);
		while($this->store->contains($name))
		{
			$name .= new RandomString(RandomString::LOWER, 1);
		}
		$this->store->addItems($name);
		 return $name;
	}
}

?>