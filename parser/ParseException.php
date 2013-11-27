<?php
class InterpreterException extends RuntimeException
{
	protected  $position;
	protected  $expression;
	protected  $reason;
	
	public function __construct($reason='', $exp_string='', $position=null)
	{
		$this->reason = $reason ? $reason : 'unknown reason';
		$this->expression = $exp_string;
		$this->position = (int)$position;
		$message = $this->getMessage();
		parent::__construct($message);
	}
	
	protected function makeMessage()
	{
		return 'Interpreter exception ' . ($this->getPosition() ? (' at position ' . $this->getPosition() ) : '') . ': ' . $this->getReason();		
	}
	
	public function getReason()
	{
		return $this->reason;
	}
	
	public function getExpression()
	{
		return $this->expression;
	}
	
	public function getPosition()
	{
		return $this->position;
	}
}

class ParseError extends InterpreterException
{	
	public function __construct($reason='', $exp_string='', $position=null)
	{
		parent::__construct($reason, $exp_string, $position);
	}
	
	protected function makeMessage()
	{
		return 'Parse error ' . ($this->getPosition() ? (' at position ' . $this->getPosition() ) : '') . ': ' . $this->getReason();		
	}
}

class SyntaxError extends InterpreterException
{
		
	public function __construct($reason='', $exp_string='', $position=null)
	{
		parent::__construct($reason, $exp_string, $position);
	}
	
	protected function makeMessage()
	{
		return 'Syntax error ' . ($this->getPosition() ? (' at position ' . $this->getPosition() ) : '') . ': ' . $this->getReason();		
	}
}
?>