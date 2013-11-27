<?php
namespace um;
require_once $_SERVER['DOCUMENT_ROOT'] . '/core/load.php';
require_once Load::package('set', 'Set');
require_once Load::package('set', 'SetInterface');

/**
 * Description of CountingSet
 *
 * @author Will Heyser
 */
class CountingSet extends Set implements \ArrayAccess, \IteratorAggregate
{
	protected function addItem($key, $val)
	{
		if(!isset($this->_data[$key]))
		{
			$this->_data[$item] = 0;
		}
		if(!is_numeric($val))
		{
			$val = $val ? 1 : 0;
		}
		$this->_data[$item]++;
	}

	protected static function convertArgs($args)
	{
		if(count($args) == 1)
		{
			if(is_array($args[0]))
			{
				return $args[0];
			}
			elseif(is_callable(array($args[0], 'toArray')))
			{
				return $args[0]->toArray();
			}
		}
	}

	public function toArray()
	{
		if($this->_changed)
		{
			$this->_keys = array();
			foreach($this->_data as $key=>$val)
			{
				for($i=0;$i<$val;$i++)
				{
					$this->_keys[] = $key;
				}
			}
			sort($this->_keys);
		}
		return $this->_keys;
	}

	public function equal()
	{
		if(func_num_args() == 1)
		{
			$item = func_get_arg(0);
		}
		else
		{
			$item = func_get_args();
		}
		if(!($item instanceof CountingSet))
		{
			$item = new CountingSet($item);
		}
		return $this->toArray() == $item->toArray();
	}

	/**
	 * Implements a union operation for sets.
	 * @param \um\Set $a
	 * @param \um\Set $b
	 * @return \um\CountingSet
	 */
	public static function union(Set $a, Set $b)
	{
		$arr = $a->toArray();
		foreach($b as $key=>$val)
		{
			if(!isset($arr[$key]) || $arr[$key] < $val)
			{
				$arr[$key] = $val;
			}
		}
		return new CountingSet($arr);
	}

	/**
	 * Implements an intesection operation for sets.
	 * @param \um\Set $a
	 * @param \um\Set $b
	 * @return \um\Set
	 */
	public static function intersection(Set $a, Set $b)
	{
		return new Set(array_intersect($a->toArray(), $b->toArray()));
	}

	/**
	 * Implements a compliment operation for sets.
	 * @param \um\Set $a
	 * @param \um\Set $b
	 * @return \um\Set
	 */
	public static function compliment(Set $a, Set $b)
	{
		return new Set(array_diff($a->toArray(), $b->toArray()));
	}

	public function getIterator()
	{
		return new \ArrayIterator($this->_data);
	}

	public function offsetExists($offset)
	{
		return $this->contains($offset);
	}

	public function offsetGet($offset)
	{
		return $this->_data[$offset];
	}

	public function offsetSet($offset, $value)
	{
		return false;
	}

	public function offsetUnset($offset)
	{
		return false;
	}
}

?>