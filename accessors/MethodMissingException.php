<?php
namespace um;
require_once 'RuleException.php';

class MethodMissingException extends RuleException
{
	public function __construct($var_name='', $object_name='', $method_name='', $message='', $code=null, $previous=null)
	{
		if($object_name && $method_name)
		{
			$message = "Method $method_name does not exist in object $object_name." . ($message ? '  ' . $message : '');
		}
		parent::__construct($var_name, $message, $code, $previous);
	}
}
?>
