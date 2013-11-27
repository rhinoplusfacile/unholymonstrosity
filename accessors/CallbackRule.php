<?php
namespace um;
require_once 'Rule.php';

class CallbackRule extends Rule
{	
	protected function getValue()
	{
		return new NullValue;
	}
	
	protected function setValue($value)
	{
		return $value;
	}
	
	protected function getReturn($value)
	{
		$action = $this->getAction();
		if(!$action)
		{
			throw new RuleException($this->getVarName(), 'Callback method must be specified for ' . $this->getVarName() . '.');
		}
		try
		{
			$method = $this->robject->getMethod($action);
			$method->setAccessible(true);
			if($value instanceof NullValue)
			{
				return $method->invoke($this->object);
			}
			else
			{
				return $this->setValue($method->invoke($this->object, $value));
			}
		}
		catch(ReflectionException $e)
		{
			throw new RuleException($this->getVarName(), "Callback method $action doesn't exist in class {$this->robject->getName()}", '', $e);
		}
	}
	
	public function generateFunctionBody($getset, $class_name)
	{
		$func = parent::generateFunctionBody($getset, $class_name);
		if(self::is_get($getset))
		{
			$line = '$retval = ';
			$func->addBodyLine(self::line_it_up(''));
		}
	}
}
?>
