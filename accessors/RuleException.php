<?php
namespace um;

class RuleException extends \Exception
{
	public function __construct($var_name='', $message='', $code=null, $previous=null)
	{
		if($var_name)
		{
			$message = "Rule for $var_name: " . $message;
		}
		parent::__construct($message, $code, $previous);
	}
}
?>
