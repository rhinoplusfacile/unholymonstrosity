<?php
require_once 'meta.php';
require_once 'tokens.php';

//INCLUSIVE i.e. slice('1234', 0, 1) returns '12' not just '1'
function slice($string, $start, $end)
{
	$length = ($end - $start) + 1;
	return substr($string, $start, $length);
}

class Lexer
{
	private static $tokentypes = array();
	/** @var Tokens */
	private $tokens;
	/** @var string */
	private $exp;
	
	public function __construct($exp)
	{
		$this->exp = $exp;
		self::$tokentypes[] = new MeasureType;
		self::$tokentypes[] = new NDType;
		self::$tokentypes[] = new QuestionType;
		self::$tokentypes[] = new WhitespaceType;
		self::$tokentypes[] = new ParenthesisType;
		self::$tokentypes[] = new BracketType;
		self::$tokentypes[] = new ComparisonType;
		self::$tokentypes[] = new NumberType;
		self::$tokentypes[] = new LogicalOpType;
		self::$tokentypes[] = new ValueType;
		self::$tokentypes[] = new AllType;
	}
	
	public function getTokens()
	{
		if(!isset($this->tokens))
		{
			$this->tokens = array();
			$this->parse();
		}
		return $this->tokens;
	}
	
	private function parse()
	{
		$index = 0;
		$success = false;
		while($index < strlen($this->exp))
		{
			foreach(self::$tokentypes as /* @var $token_type TokenType */$token_type)
			{
				$temp = $token_type->parse($this->exp, $index);
				if($temp > -1)
				{
					$index = $temp;
					$success = true;
					$token = $token_type->getToken();
					if($token)
					{
						$this->tokens[] = $token;
					}
					break;
				}
			}
			if(!$success)
			{
				throw new ParseError($reason='unknown character ' . $this->exp[$index], $this->exp='', $index);
			}
			$index++;
		}
	}
}

abstract class TokenType
{
	const USE_REGEX = true;
	const USE_SIMPLE = false;
	
	const MATCH_ONE = true;
	const MATCH_MANY = false;
	
	protected $matches;
	protected $token_class;
	protected $exp;
	protected $start;
	protected $end;
	
	protected $use_regex;
	protected $match_one;
	
	public function __construct($matches=null, $token_class=null, $use_regex=self::USE_SIMPLE, $match_one=self::MATCH_ONE)
	{
		$this->matches = $matches;
		$this->token_class = $token_class;
		$this->use_regex = (bool)$use_regex;
		$this->match_one = (bool)$match_one;
	}
	
	protected function setStart($start)
	{
		$this->start = $this->end = $start;
	}
	
	public function parse($exp, $pos)
	{
		$this->exp = $exp;
		$this->setStart($pos);
		
		if($this->match_one == self::MATCH_ONE)
		{
			$this->match_one();
		}
		else
		{
			$this->match_many();
		}
		return $this->end;
	}
	
	protected function match_one()
	{
		if(!$this->match())
		{
			$this->end = -1;
		}		
	}
	
	protected function match_many()
	{
		while(($this->end < strlen($this->exp)) && $this->match())
		{
			$this->end++;
		}
		if($this->end > $this->start)
		{
			//Roll back one character because we didn't find what we were looking for.
			$this->end--;
		}
		else
		{
			$this->end = -1;
		}
	}
	
	protected function match()
	{
		//Pick a comparison based on a flag
		if($this->use_regex == self::USE_REGEX)
		{
			return $this->regex_match();
		}
		else
		{
			return $this->simple_match();
		}
	}
	
	protected function regex_match()
	{
		return(preg_match($this->make_regex(), $this->exp[$this->end]));
	}
	
	protected function simple_match()
	{
		return (strstr($this->matches, strtolower($this->exp[$this->end])));
	}
	
	public function getToken()
	{
		$token_exp = slice($this->exp, $this->start, $this->end);
		return Token::create($this->token_class, $token_exp, $this->start);
	}
	
	protected function make_regex()
	{
		return '/' . $this->matches . '/ix';
	}
}

class WhitespaceType extends TokenType
{
	public function __construct()
	{
		parent::__construct('\s', '', self::USE_REGEX, self::MATCH_MANY);
	}
	
	public function getToken()
	{
		return false;
	}
}

abstract class MultiType extends TokenType
{
	public function __construct($matches = null, $token_class = null, $use_regex = self::USE_SIMPLE)
	{
		parent::__construct($matches, $token_class, $use_regex, self::MATCH_MANY);
	}
	
	protected function match_many()
	{
		if(!$this->match())
		{
			$this->end = -1;
		}
	}
	
	protected function simple_match()
	{
		$len = strlen($this->matches);
		$test = substr($this->exp, $this->start, $len);
		if(strcmp($this->matches, $test) === 0)
		{
			$this->end += ($len - 1);
			return true;
		}
		return false;
	}
	
	protected function regex_match()
	{
		$matches = array();
		if(preg_match($this->make_regex(), substr($this->exp, $this->start), $matches))
		{
			$this->end += (strlen($matches[0])-1);
			return true;
		}
		return false;
	}
	
	protected function make_regex()
	{
		return '/^' . $this->matches . '/ix';
	}
}

class MeasureType extends MultiType
{
	public function __construct()
	{
		parent::__construct('measure', 'MeasureToken', self::USE_SIMPLE);
	}
}

class QuestionType extends MultiType
{
	public function __construct()
	{
		parent::__construct('q', '\d', 'QuestionToken', self::USE_SIMPLE);
	}
}

class NDType extends TokenType
{
	public function __construct()
	{
		parent::__construct('(num)|(den)', 'NDToken', self::USE_REGEX, self::MATCH_MANY);
	}
	
	protected function match_many()
	{
		$exp = substr($this->exp, $this->end, 3);
		if(preg_match($this->matches, $exp))
		{
			$this->end += 2;
			return;
		}
		$this->end = -1;
	}
}

class ParenthesisType extends TokenType
{
	public function __construct()
	{
		parent::__construct('()', 'ParenthesisToken');
	}
}

class BracketType extends TokenType
{
	public function __construct()
	{
		parent::__construct('{}', 'BracketToken');
	}
}

class NumberType extends TokenType
{
	public function __construct()
	{
		parent::__construct('\d', 'NumberToken', self::USE_REGEX, self::MATCH_MANY);
	}
}

class ComparisonType extends TokenType
{
	public function __construct()
	{
		parent::__construct('!<>=', 'ComparisonToken', self::USE_SIMPLE, self::MATCH_MANY);
	}
}

class LogicalOpType extends TokenType
{
	public function __construct()
	{
		parent::__construct('', 'LogicalOpToken', self::USE_SIMPLE, self::MATCH_MANY);
	}
	
	protected function match_many()
	{
		$pos = $this->end + 1;
		if(strstr('&|', $this->exp[$this->end]))
		{
			if($this->exp[$this->end] == $this->exp[$pos])
			{
				$this->end = $pos;
			}
		}
		$this->end = -1;
	}
}

class ValueType extends TokenType
{
	public function __construct()
	{
		parent::__construct('ynx', 'ValueToken', self::USE_SIMPLE, self::MATCH_ONE);
	}
}

class AllType extends TokenType
{
	public function __construct()
	{
		parent::__construct('all', 'AllToken', self::USE_SIMPLE, self::MATCH_MANY);
	}
	
	protected function match_many()
	{
		if(substr($this->exp, $this->end, 3) == 'all')
		{
			$this->end += 2;
			return;
		}
		$this->end = -1;
	}
}
?>
