<?php
require_once 'meta.php';
class Measure implements ExpressionInterface, ParserChunk
{
	public $id;
	public $exp;
	public $n;
	public $d;
	
	public function __construct($exp_string)
	{
		$matches = array();
		preg_match('/m(\d+){n:}/ix', $subject, $matches);
		$this->exp = $exp_string;
		$n_match = array();
		preg_match('/n\(([^d}]*)\)/i', $this->exp, $n_match);
		$this->n = new ND;
		$this->n->evaluate($n_match[1]);
		$d_match = array();
		preg_match('/d\(([^}]*)\)/i', $this->exp, $d_match);
		$this->d = new ND;
		$this->d->evaluate($d_match[1]);
	}
	
	public function evaluate($exp)
	{
		$this->exp = trim(preg_replace('/\s/i', '', $exp));
		$n_match = array();
		preg_match('/n\(([^d}]*)\)/i', $this->exp, $n_match);
		$this->n = new ND;
		$this->n->evaluate($n_match[1]);
		$d_match = array();
		preg_match('/d\(([^}]*)\)/i', $this->exp, $d_match);
		$this->d = new ND;
		$this->d->evaluate($d_match[1]);
	}

	public function getValue()
	{
		
	}
}

class ND implements ExpressionInterface, ParserChunk
{
	public $exp_string;
	/** @var Expression */
	public $exp;
	
	public function evaluate($exp)
	{
		$this->exp_string = trim($exp);
		$this->exp = Expression::tokenize($exp);
	}

	public function getValue()
	{
		return $exp->getValue();
	}
}
?>
