<?php
namespace um;
require_once 'Rule.php';

class AccessRule extends Rule
{	
	protected function getReturn($value)
	{
		if($value instanceof NullValue)
		{
			return $this->getValue();
		}
		else
		{
			return $this->setValue($value);
		}
	}
}
?>
