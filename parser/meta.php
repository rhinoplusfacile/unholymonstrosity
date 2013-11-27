<?php
require_once('ParseException.php');
interface ParserChunk
{
	public function evaluate();
}

interface ExpressionInterface
{
	public function getValue();
}
?>
