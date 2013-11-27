<?php
namespace um;
require_once $_SERVER['DOCUMENT_ROOT'] . '/../core/spamfilter/core/load.php';
Load::helper('random');
/**
 * Immutable random string object which can be treated as a string for purposes of output.
 */
class RandomString
{
	const CUSTOM = 0;	//For use if only the custom choices should be used.
	const LOWER = 1;
	const UPPER = 2;
	const ALPHA = 3;
	const NUM = 4;
	const ALPHANUM = 7;
	const SPECIAL = 8;
	const ALL = 15;

	/** @var int the type of characters allowed in this random string */
	private $flags = self::ALPHA;
	/** @var array the choices of characters */
	private $choices;
	/** @var int */
	private $length;
	/** @var string */
	private $value;

	public function __construct($flags = self::ALPHA, $length = 0, $choices = false)
	{
		$this->flags = $flags;
		if(!$length)
		{
			$this->length = rand(1, 20);
		}
		elseif(is_array($length))
		{
			$this->length = rand($length[0], $length[1]);
		}
		else
		{
			$this->length = round($length);
		}
		$this->setChoices($choices);
	}

	/**
	 * Initializes the choices array if it hasn't already been initialized.
	 */
	protected function setChoices($choices=0)
	{
		if($choices)
		{
			$this->choices = array();
			if(is_string($choices))
			{
				$choices = str_split($choices);
			}
			foreach($choices as $letter)
			{
				if(is_int($letter))
				{
					$this->choices[] = $letter;
				}
				else
				{
					$this->choices[] = ord((string)$letter);
				}
			}
		}
		elseif(!isset($this->choices))
		{
			$this->choices = array();
			if($this->flags & self::ALPHA)
			{
				//Lower case ascii keys
				$this->choices = array_merge($this->choices, range(97,122));
			}
			if($this->flags & self::NUM)
			{
				//Numeric ascii keys
				$this->choices = array_merge($this->choices, range(48, 57));
			}
			if($this->flags & self::SPECIAL)
			{
				//Numeric ascii keys
				$this->choices = array_merge($this->choices, range(33, 47), range(58, 64), range(91, 96), range(123, 126));
			}
		}
	}

	/**
	 * "Lazy-loading" string generation with caching.
	 * @return string
	 */
	public function __toString()
	{
		if(!isset($this->value))
		{
			$this->setChoices();
			$this->value = '';
			for($i = 0; $i < $this->length; $i++)
			{
				$this->value .= $this->upper_lower($this->choose());
			}
		}
		return $this->value;
	}

	public function getValue()
	{
		return $this->__toString();
	}

	/**
	 * Picks a random character from the choices array.
	 * @return string
	 */
	protected function choose()
	{
		return chr($this->choices[rand(0, count($this->choices)-1)]);
	}

	/**
	 * Capitalizes based on the flags set.
	 * If LOWER but not UPPER, the random expression evaluates to rand(0,0) and the if statement is never true.
	 * If UPPER but not LOWER, rand(1,1) so the statement is always true.
	 * If both UPPER and LOWER, rand(0,1), a coin toss.
	 * @param string $char
	 * @return string
	 */
	protected function upper_lower($char)
	{
		if(($this->flags & (self::LOWER | self::UPPER))
				&& rand(($this->flags & self::LOWER ? 0 : 1),($this->flags & self::UPPER ? 1 : 0)))
		{
			$char = strtoupper($char);
		}
		return $char;
	}
}
?>
