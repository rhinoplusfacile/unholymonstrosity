<?php
$special = '!@#$%^&*()_+-={}[]|\\:;"\'<>,.?/`~';
$ar = array();
foreach(str_split($special) as $char)
{
	$ar[] = ord($char);
}
sort($ar);
$ranges = array();
$temp_range = array();
for($i = 0; $i < count($ar); $i++)
{
	if(!(in_array($ar[$i]+1, $ar) && in_array($ar[$i]-1, $ar)))
	{
		if(empty($temp_range))
		{
			$temp_range[] = $ar[$i];
		}
		else
		{
			$temp_range[] = $ar[$i];
			$ranges[] = $temp_range;
			$temp_range = array();
		}
	}
}
$output = array();
foreach($ranges as $range)
{
	$output[] = 'range(' . $range[0] . ', ' . $range[1] . ')';
}
echo implode(', ', $output);
?>