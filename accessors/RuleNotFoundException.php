<?php
namespace um;
require_once 'RuleException.php';

class RuleNotFoundException extends RuleException
{
	public function __construct($var_name, $message='', $code=null, $previous=null)
	{
		$message = "Rule not found for property $var_name." . ($message ? '  ' . $message : '');
		parent::__construct($var_name, $message, $code, $previous);
	}
}
?>
