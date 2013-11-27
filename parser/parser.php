<?php
require_once 'meta.php';
require_once 'lexer.php';
require_once 'tokens.php';

class Parser
{
	/** @var Lexer */
	private $lexer;
	/** @var string */
	private $expression_string;
	/** @var ParseTree */
	private $parse_tree;
	/** @var array */
	private $tokens;
	
	public function __construct($expression_string)
	{
		$this->lexer = new Lexer($expression_string);
	}
	
	public function getParseTree()
	{
		if(!isset($this->tokens))
		{
			$this->tokens = $this->lexer->getTokens();
		}
		if(!isset($this->parse_tree))
		{
			$this->parse_tree = new ParseTree($tokens);
		}
		try
		{
			$this->parse_tree->parse();
			return $this->parse_tree;
		}
		catch(ParseError $e)
		{
			throw new ParseError($e->getReason(), $this->expression_string, $e->getPosition());
		}
	}
}

class ParseTree
{
	private $measures = array();
	private $tokens;
	
	public function __construct($tokens)
	{
		$this->tokens = $tokens;
		$this->parse();
	}
	
	public function parse()
	{
		$temp = array();
		$index = 0;
		while($index < count($this->tokens))
		{
			$token = $this->tokens[$index];
			if($token instanceof MeasureToken)
			{
				$temp[] = $token;
				$index++;
				$token = $this->tokens[$index];
				while(!($token instanceof MeasureToken) && $index < count($this->tokens))
				{
					$temp[] = $token;
					$index++;
					$token = $this->tokens[$index];
				}
				$new_measure = new Measure;
				$new_measure->addTokens($temp);
				$this->measures[] = $new_measure;
			}
		}
		if(!count($this->measures))
		{
			throw new ParseError('malformed measure code', '', 0);
		}
	}
}

class Measure
{
	/** @var ND */
	private $n;
	/** @var ND */
	private $d;
	
	/** @var int */
	private $measure;
	
	public function __construct()
	{
		$this->body = new Brackets;
	}
	
	public function addTokens($tokens)
	{
		if($tokens[0] instanceof MeasureToken)
		{
			$this->measure = $tokens[0];
		}
		else
		{
			throw new ParseError('expected \'m\',  found \'' . $tokens[0]->getExpression() . '\'', '', $tokens[0]->getPos());
		}
		if($tokens[1] instanceof BracketToken && $tokens[1]->getDirection() == EnclosureToken::OPEN)
		{
			$this->measure = $tokens[1];
		}
		else
		{
			throw new ParseError('expected \'{\',  found \'' . $tokens[1]->getExpression() . '\'', '', $tokens[0]->getPos());
		}
		$length = count($tokens);
		if($tokens[$length-1] instanceof BracketToken && $tokens[$length-1]->getDirection() == EnclosureToken::CLOSE)
		{
			$this->measure = $tokens[$length-1];
		}
		else
		{
			throw new ParseError('expected \'}\',  found \'' . $tokens[$length-1]->getExpression() . '\'', '', $tokens[0]->getPos());
		}
		$inner_tokens = array_slice($tokens, 2, $length-3);
		$index = 0;
		$temp = array();
		if(($inner_tokens[$index] instanceof NDToken) && ($inner_tokens[$index]->getND() == NDToken::N))
		{
			$this->n = new ND;
			$temp[] = $inner_tokens[$index];
			$index++;
			while(!($inner_tokens[$index] instanceof NDToken) && $index < count($tokens))
			{
				$temp[] = $inner_tokens[$index];
				$index++;
			}
			$this->n->addTokens($temp);
			$temp = array();
		}
		else
		{
			throw new ParseError('expected \'n\', found \'' . $inner_tokens[$index] . '\'', '', $inner_tokens[$index]->getPos());
		}
		if(($inner_tokens[$index] instanceof NDToken) && ($inner_tokens[$index]->getND() == NDToken::D))
		{
			$this->d = new ND;
			$temp[] = $inner_tokens[$index];
			$index++;
			while(!($inner_tokens[$index] instanceof NDToken) && $index < count($tokens))
			{
				$temp[] = $inner_tokens[$index];
				$index++;
			}
			$this->n->addTokens($temp);
			$temp = array();
		}		
	}
}

abstract class Enclosure implements \IteratorAggregate, \Countable, \ArrayAccess
{
	protected $enclosed = array();
	/** @var EnclosureToken */
	protected $start_token;
	/** @var EnclosureToken */
	protected $end_token;
	
	abstract public function parse($tokens);
	
	public function setStart($token)
	{
		if($token instanceof EnclosureToken &&
			$token->getDirection() == EnclosureToken::OPEN)
		{
			$this->setStart($token);
		}
	}
	
	public function count()
	{
		return count($this->enclosed);
	}
	
	public function getIterator()
	{
		return new ArrayIterator($this->enclosed);
	}

	public function offsetExists($offset)
	{
		return isset($this->enclosed[$offset]);
	}

	public function offsetGet($offset)
	{
		return $this->enclosed[$offset];
	}

	public function offsetSet($offset, $value)
	{
		$this->enclosed[$offset] = $value;
	}

	public function offsetUnset($offset)
	{
		unset($this->enclosed[$offset]);
	}		
}


class Brackets extends Enclosure
{
	public function parse($tokens)
	{
		$this->start_token = $tokens[0];
		$this->end_token = $tokens[count($tokens)-1];
		if(!($this->start_token instanceof BracketToken) ||
			$this->start_token->getDirection() != EnclosureToken::OPEN ||
			!($this->end_token instanceof BracketToken) ||
			$this->end_token->getDirection() != EnclosureToken::CLOSE)
		{
			throw new ParseError('expected \'m\',  received \'' . $tokens[0]->getExpression() . '\'', '', $tokens[0]->getPos());
		}
	}
}
?>
