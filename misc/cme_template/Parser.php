<?php
namespace PIMS\admin\cme_template;

interface Parser
{
	public function parse($text);
	public function get($section);
}
?>
