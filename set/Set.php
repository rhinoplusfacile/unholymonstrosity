<?php
namespace um;
/**
 * Simple Set implementation
 * ONLY FOR NUMBERS OR STRINGS, NOT FOR OBJECTS
 * Implemented as a hash table because it might be faster than doing array searches.
 *
 * @author Will Heyser
 */
class Set implements \Countable
{
	protected $_data = array();
	protected $_changed = false;
	protected $_keys = array();

	public function __construct()
	{
		$this->addItems(func_get_args());
	}

	public function addItems()
	{
		return $this->modifyItems(func_get_args(), true);
	}

	public function removeItems()
	{
		return $this->modifyItems(func_get_args(), false);
	}

	protected function modifyItems($args, $add)
	{
		$modified_anything = false;
		if(count($args) > 0)
		{
			$args = self::convertArgs($args);
			foreach($args as $key=>$val)
			{
				if(self::validItems($key, $val))
				{
					$retval = $add ? $this->addItem($key, $val) : $this->removeItem($key, $val);
					$this->_changed = $this->_changed && $retval;
					$modified_anything = $modified_anything && $retval;
				}
			}
		}
		return $modified_anything;
	}

	protected static function validItems($key, $val)
	{
		return !is_object($key) && !is_object($val);
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

	protected function addItem($key, $val)
	{
		if(!$this->contains($val))
		{
			$this->_data[$val] = true;
			return true;
		}
		return false;
	}

	protected function removeItem($key, $val)
	{
		if($this->contains($val))
		{
			unset($this->_data[$val]);
			return true;
		}
		return false;
	}

	public function contains($subset)
	{
		if($subset instanceof Set)
		{
			return(self::intersection($this, $subset)->equal($subset));
		}
		elseif(is_array($subset))
		{
			$subset = new Set($subset);
			return(self::intersection($this, $subset)->equal($subset));
		}
		elseif(!is_object($subset))
		{
			return array_key_exists($subset, $this->_data);
		}
		else
		{
			return false;
		}
	}

	public function toArray()
	{
		if($this->_changed)
		{
			$this->_keys = array_keys($this->_data);
			sort($this->_keys);
		}
		return $this->_keys;
	}

	public function __toString()
	{
		return 'Set(' . implode(',', $this->toArray()) . ')';
	}

	public function equal()
	{
		$class = get_class($this);
		if(func_num_args() == 1)
		{
			$item = func_get_arg(0);
		}
		else
		{
			$item = func_get_args();
		}
		if(!($item instanceof $class))
		{
			$item = new $class($item);
		}
		return $this->toArray() == $item->toArray();
	}

	public function count()
	{
		return count($this->_data);
	}

	/**
	 * Implements a union operation for sets.
	 * @param \um\Set $a
	 * @param \um\Set $b
	 * @return \um\Set
	 */
	public static function union(Set $a, Set $b)
	{
		return new Set(array_unique(array_merge($a->toArray(), $b->toArray())));
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
}
?>
