<?php
require_once 'meta.php';

abstract class Token
{
	protected $pos;
	protected $expression_string;
	
	protected function __construct($exp_string, $pos) 
	{
		$this->expression_string = $exp_string;
		$this->pos = $pos;
	}
	
	public static function create($type, $exp_string, $pos)
	{
		if(is_subclass_of($type, 'Token'))
		{
			return new $type($exp_string, $pos);
		}
	}
	
	public function getPos()
	{
		return $this->pos;
	}
	
	public function getExpression()
	{
		return $this->expression_string;
	}
}

abstract class EnclosureToken extends Token
{
	const OPEN = 1;
	const CLOSE = -1;
	
	static protected $open;
	static protected $close;
	
	private $direction;
	
	public function __construct($exp_string, $pos)
	{
		parent::__construct($exp_string, $pos);
		if($exp_string == self::$open)
		{
			$this->direction = self::OPEN;
		}
		if($exp_string == self::$close)
		{
			$this->direction = self::CLOSE;
		}
	}
	
	public function getDirection()
	{
		return $this->direction;
	}
}

class ParenthesesToken extends EnclosureToken
{
	const OPEN = '(';
	const CLOSE = ')';
	
	public function __construct($exp_string, $pos)
	{
		self::$open = '(';
		self::$close = ')';
		parent::__construct($exp_string, $pos);
	}
}

class BracketToken extends EnclosureToken
{
	const OPEN = '(';
	const CLOSE = ')';
	
	public function __construct($exp_string, $pos)
	{
		self::$open = '{';
		self::$close = '}';
		parent::__construct($exp_string, $pos);
	}
	
}

class MeasureToken extends Token
{
	private $number;
	
	public function __construct($exp_string, $pos)
	{
		parent::__construct($exp_string, $pos);
		$this->number = substr($exp_string, 1);
	}
	
	public function getNumber()
	{
		return $this->number;
	}
}

class QuestionToken extends Token
{
	private $number;
	
	public function __construct($exp_string, $pos)
	{
		parent::__construct($exp_string, $pos);
		$this->number = substr($exp_string, 1);
	}
}

class NDToken extends Token
{
	const N = 'num';
	const D = 'den';
	private $nd;
	
	protected function __construct($exp_string, $pos)
	{
		parent::__construct($exp_string, $pos);
		if($exp_string == self::N)
		{
			$this->nd = self::N;
		}
		elseif($exp_string == self::D)
		{
			$this->nd = self::D;
		}
	}
	
	public function getND()
	{
		return $nd;
	}
}

class NumberToken extends Token
{
	private $value;
	
	protected function __construct($exp_string, $pos)
	{
		parent::__construct($exp_string, $pos);
		$this->value = (int)$exp_string;
	}
}

class OperatorToken extends Token
{
	protected $operator;
	
	protected function __construct($exp_string, $pos)
	{
		parent::__construct($exp_string, $pos);
	}
	
	public function getOperator()
	{
		return $this->operator;
	}
}

class ComparisonToken extends OperatorToken
{
	const EQUAL = '=';
	const NOT_EQUAL = '!=';
	const GREATER = '>';
	const LESS = '<';
	const GREATER_EQUAL = '>=';
	const LESS_EQUAL = '<=';
	
	protected function __construct($exp_string, $pos)
	{
		parent::__construct($exp_string, $pos);
		switch($exp_string)
		{
		case self::EQUAL:
		case self::NOT_EQUAL:
		case self::GREATER:
		case self::LESS:
		case self::GREATER_EQUAL:
		case self::LESS_EQUAL:
			$this->operator = $exp_string;
			break;
		default;
			$this->operator = null;
			break;
		}
	}
}

class LogicalOpToken extends OperatorToken
{
	const ANDOP = '&&';
	const OROP = '||';
	
	protected function __construct($exp_string, $pos)
	{
		parent::__construct($exp_string, $pos);
		if($exp_string == self::ANDOP || $exp_string == self::OROP)
		{
			$this->operator = $exp_string;
		}
	}
}

class ValueToken extends Token
{
	const YES = 'y';
	const NO = 'n';
	const NA = 'x';
	
	private $value;
	private $type;
	
	protected function __construct($exp_string, $pos)
	{
		parent::__construct($exp_string, $pos);
		$this->type = $exp_string;
		switch($exp_string)
		{
			case self::YES:
				$this->value = 1;
				break;
			case self::NO:
				$this->value = 0;
				break;
			case self::NA:
			default:
				$this->value = 2;
				break;
		}
	}
}

class AllToken extends Token
{
	protected function __construct($exp_string, $pos)
	{
		parent::__construct($exp_string, $pos);
	}
}
?>
