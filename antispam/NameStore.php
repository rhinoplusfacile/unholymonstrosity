<?php
namespace um;

class NameStore implements \Countable
{
	/** @var \um\Set */
	private $store;

	public function __construct($size, $names = array())
	{
		$this->store = new Set;
		$this->addNames($names);
		$this->padNames($size - $this->count());
	}

	private function padNames($num)
	{
		for($i = 0; $i < $num; $i++)
		{
			do
			{
				$name = '' . new RandomString(RandomString::LOWER, array(4, 10));
			}
			while(!$this->store->addItems($name));
		}
	}

	private function addNames($names)
	{
		foreach($names as $name)
		{
			$name = preg_replace('/[^a-z]/i', '', $name);
			$this->store->addItems($name);
		}
	}

	public function getName()
	{
		$options = $this->store->toArray();
		if(!empty($options))
		{
			$name = random_pick($options);
			$this->store->removeItems($name);
			return $name;
		}
		throw new \OutOfBoundsException('NameStore empty.');
	}

	public function count()
	{
		return $this->store->count();
	}
}
?>
