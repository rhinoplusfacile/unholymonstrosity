<?php
namespace um;

class TemplateFunction
{
	private $func;
	
	public function __construct(\ReflectionMethod $func = null)
	{
		$this->func = $func;
		if(isset($this->func))
		{
			$this->func->setAccessible(true);
		}
	}
	
	public function getResult($object, \ReflectionProperty $r_prop, $value)
	{
		$r_prop->setAccessible(true);
		if($value instanceof NullValue)
		{
			if(isset($this->func))
			{
				return $this->func->invoke($object, $r_prop->getName());
			}
			else
			{
				return $r_prop->getValue($object);
			}
		}
		else
		{
			if(isset($this->func))
			{
				return $this->func->invoke($object, $r_prop->getName(), $value);
			}
			else
			{
				$r_prop->setValue($object, $value);
				return $object;
			}
		}
	}
	
	public function is_active()
	{
		return isset($this->func);
	}
	
	public function getFuncCall()
	{
		if(isset($this->func))
		{
			return $this->func->getName();
		}
	}
}
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
