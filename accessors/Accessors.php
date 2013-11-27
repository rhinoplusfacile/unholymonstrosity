<?php
namespace um;

class Accessors
{
	const MUST_IMPLEMENT = '/Accessible/';
	
	const INIT_GET = 1;
	const INIT_SET = 2;
	const INIT_BOTH = 3;
	
	/** @var Getter */
	private $getter;
	/** @var Setter */
	private $setter;
	
	private $class;
	
	public function __construct($class, $init_flags = self::INIT_BOTH)
	{
		$this->setClass($class);
		if($init_flags & self::INIT_GET)
		{
			$this->addGetter();
		}
		if($init_flags & self::INIT_SET)
		{
			$this->addSetter();
		}
	}
	
	private function setClass($class)
	{
		$interfaces = class_implements($class);
		$match = false;
		foreach($interfaces as $interface)
		{
			$match = (preg_match(self::MUST_IMPLEMENT, $interface) || $match);
		}
		if(!$match)
		{
			throw new RuleException('', "To allow Accessors, $class must implement " . self::MUST_IMPLEMENT . '.');
		}
		$this->class = $class;
	}
	
	public function addGetter()
	{
		$this->getter = new Getter($this->class);
	}
	
	public function addSetter()
	{
		$this->setter = new Setter($this->class);
	}
	
	public function hasGetter()
	{
		return isset($this->getter);
	}
	
	public function hasSetter()
	{
		return isset($this->setter);
	}
	
	public function addGetRule($var_name, $rule, $action='')
	{
		$this->getter->setRule($var_name, $rule, $action='');
	}
	
	public function addSetRule($var_name, $rule, $action='')
	{
		$this->setter->setRule($var_name, $rule, $action='');
	}
	
	/**
	 * 
	 * @param \accessible\Accessible $object
	 * @param string $name
	 * @param array $args
	 * @return mixed|\accessors\NotAnAccessorMethod
	 */
	public function processFunctionCall($object, $name, $args)
	{
		$matches = array();
		if(preg_match('/(get|set)([[:alnum:]]+)/i', $name, $matches))
		{
			$getset = $matches[1];
			$var_name = $matches[2];
			
			$matches = array();
			preg_match_all('/[A-Z][a-z]*/', $var_name, $matches);
			$matches = $matches[0];
			array_walk($matches, create_function('&$val, $index', '$val = strtolower($val);'));
			$var_name = implode('_', $matches);
			$args = array_merge(array($object, $var_name), $args);
			return call_user_func_array(array($this, $getset), $args);
			//return $this->$getset($object, $var_name, $args);
		}
		return new NotAnAccessorMethod();
	}
	
	private function get($object, $var_name, $args='')
	{
		return $this->getter->get($object, $var_name);
	}
	
	private function set($object, $var_name, $args)
	{
		return $this->setter->set($object, $var_name, $args);
	}
}
?>
