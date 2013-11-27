<?php
namespace um;
require_once 'RuleException.php';

class PropertyNotFoundException extends RuleException
{
	public function __construct($class_name, $var_name, $message='', $code=null, $previous=null)
	{
		$message = $class_name . ' does not have property: ' . $var_name . ($message ? '  ' . $message : '');
		parent::__construct($var_name, $message, $code, $previous);
	}	
}
?>
