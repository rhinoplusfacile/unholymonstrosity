<?php
namespace um;

/**
 * Enforces both a minimum value and a maximum value, inclusive.
 * If $min < $val < $max, $val will be returned.
 * If $val > $max, $max will be returned.
 * If $val < $min, $min will be returned.
 * @param int|float $val
 * @param int|float $min
 * @param int|float $max
 * @return int|float
 * @throws \InvalidArgumentException
 */
function minmax($val, $min, $max)
{
	if(!validate_list('is_numeric', $val, $min, $max) || $min > $max)
	{
		throw new \InvalidArgumentException('Arguments to minmax must be numeric and $min <= $max.');
	}
	return max($min, min($val, $max));
}

/**
 * Validates a list of arguments based on a function.
 * @param callable $fn Function taking one argument and returning a boolean value.
 * @return bool
 */
function validate_list($fn)
{
	if(!is_callable($fn))
	{
		throw new \InvalidArgumentException('validate_list expects its first argument to be a callable object.');
	}
	$args = array_slice(func_get_args(), 1);
	$retval = true;
	foreach($args as $val)
	{
		$retval = ($retval && $fn($val));
	}
	return $retval;
}

/**
 * Swaps the values of $a and $b by reference.
 * @param mixed $a
 * @param mixed $b
 */
function swap(&$a, &$b)
{
	$temp = $a;
	$a = $b;
	$b = $temp;
}
?>
