<?php
namespace um;
require_once $_SERVER['DOCUMENT_ROOT'] . '/core/load.php';
Load::helper('utility');

/**
 * Picks an arbitrary number of values from an array in random order and returns them.
 * Will return up to the total number of items in the array; if $num > count($ar) no further items will be returned.
 * @param array $ar
 * @param int $num
 * @return mixed
 */
function random_pick(array $ar, $num=1)
{
	$num = min($num, count($ar));
	$vals = array_values($ar);
	if($num <= 1)
	{
		return $vals[mt_rand(0, count($vals)-1)];
	}
	else
	{
		$retval = array();
		for($i=0; $i<$num; $i++)
		{
			$id = mt_rand(0, count($vals)-1);
			$retval[] = $vals[$id];
			unset($vals[$id]);
			$vals = array_values($vals);
		}
		return $retval;
	}
}

function shuffle(array &$ar)
{
	$end = count($ar) - 1;
	for($i=$end; $i>0; $i--)
	{
		$rand = mt_rand(0, $i);
		swap($ar[$rand], $ar[$i]);
	}
}

function rand($min, $max)
{
	return mt_rand($min, $max);
}
?>
