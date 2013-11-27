<?php

require_once 'meta.php';

class Expression implements ExpressionInterface, ParserChunk
{
	public $exp;
	
	
	
	protected static function recurse_parens($exp)
	{
		$retval = null;
		$paren_count = 0;
		$open_index = $close_index = 0;
		$retval = new Expression;
		$index = 0;
		$counter = 0;
		while($index != strlen($exp) && $counter++ < 10000)
		{
			if($exp[$index] == '(')
			{
				$paren_count++;
				if($open_index !== 0)
				{
					$open_index = $index+1;
				}
			}
			if($exp[$index] == ')')
			{
				$paren_count--;
				$close_index = $index-1;
				if($paren_count == 0)
				{
					$tokens = self::tokenize(substr($exp, $open_index, $close_index));
				}
			}
		}
	}
	
	protected static function recurse_logic($exp)
	{
		$retval = null;
		$tokens = array();
		
		$index = 0;
		$counter = 0;
		$temp_token = '';
		while($index != strlen($exp) && $counter < 10000)
		{
			$counter++;
			$char = $exp[$index];
			if(strstr('()&|', $char))
			{
				if(preg_match('/\S/i',$temp_token))
				{
					//Add the token and flush the buffer.
					$tokens[] = $temp_token;
					$temp_token = '';
				}
				
				if(strstr('()', $char))
				{
					$tokens[] = $char;
				}
				elseif(strstr('&|', $char))
				{
					$op = $char;
					$index++;
					$char = $exp[$index];
					if($op == $char)
					{
						$tokens[] = $op . $char;
					}
					else
					{
						self::parse_error($exp, $index);
					}
				}
			}
			elseif(preg_match('/[q=0-9ynxal]|\s/', $char))
			{
				$temp_token .= $char;
			}
			else
			{
				self::parse_error($exp, $index);
			}
			$index++;
		}
		return $tokens;
	}
	
	protected static function parse_error($exp, $index)
	{
		$char = $exp[$index];
		throw(new ParseError('incorrect character', $exp, $index));
	}
	
	public static function tokenize($exp)
	{
		$tokens = array();
		$index = 0;
		$counter = 0;
		$temp_token = '';
		while($index != strlen($exp) && $counter < 10000)
		{
			$counter++;
			$char = $exp[$index];
			if(strstr('()&|', $char))
			{
				if(preg_match('/\S/i',$temp_token))
				{
					//Add the token and flush the buffer.
					$tokens[] = $temp_token;
					$temp_token = '';
				}
				
				if(strstr('()', $char))
				{
					$tokens[] = $char;
				}
				elseif(strstr('&|', $char))
				{
					$op = $char;
					$index++;
					$char = $exp[$index];
					if($op == $char)
					{
						$tokens[] = $op . $char;
					}
					else
					{
						self::parse_error($exp, $index);
					}
				}
			}
			elseif(preg_match('/[q=0-9ynxal]|\s/', $char))
			{
				$temp_token .= $char;
			}
			else
			{
				self::parse_error($exp, $index);
			}
			$index++;
		}
		return $tokens;
	}
	
	public function evaluate($exp)
	{
		$this->exp_string = trim($exp);
	}
	
	public function getValue()
	{
		return $this->exp->getValue();
	}
}

class LogicExpression extends Expression
{
	/** @var Expression */
	public $exp1;
	/** @var Expression */
	public $exp2;
	
	public $op;
	
	public static function tokenize($exp)
	{
		return;
		$exp = trim($exp);
		$matches = array();
		preg_match('/^([^&|]*?)\s*((&&)|(\|\|))\s*([^&|]*?)$/', $exp, $matches);
		if(isset($matches[1]))
		{
			$exp = $matches[1];
			return self::tokenize($exp);
		}
		ddnd($exp);
	}

	public function evaluate($exp)
	{
		parent::evaluate($exp);
	}
	
	public function getValue()
	{
		
	}
}

class EqualityExpression extends Expression
{
	public $exp_string;
	public $q;
	public $op;
	public $val;

	public static function tokenize($exp)
	{
		return;
	}
	
	public function evaluate($exp)
	{
		parent::evaluate($exp);
	}
	
	public function getValue()
	{
		
	}
}

class Value extends Expression
{
	public $exp_string;
	public $exp;

	public static function tokenize($exp)
	{
		return;
	}
	
	public function getValue()
	{
		
	}

	public function evaluate($exp)
	{
		parent::evaluate($exp);
		$matches = array();
		preg_match('/\(([a-z0-9=|&()]*)\)/i', $this->exp, $matches);
	}	
}

class Question extends Expression
{
	public function evaluate($exp)
	{
		parent::evaluate($exp);		
	}

	public static function tokenize($exp)
	{
		return;
	}

	public function getValue()
	{
		
	}
}
?>
