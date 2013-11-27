<?php
namespace um;
require_once $_SERVER['DOCUMENT_ROOT'] . '/core/load.php';
Load::package('antispam', 'RuleBase');
/**
 * Description of RegexRule
 *
 * @author Will Heyser
 */
class RegexRule extends RuleBase
{
	protected $regex;
	protected $one_time;

	public function __construct($points_match, $points_no_match, $regex, $one_time=true)
	{
		parent::__construct($points_match, $points_no_match);
		$this->regex = $regex;
		$this->one_time = $one_time;
	}

	public function run($text)
	{
		$matches = array();
		$matched = preg_match_all($this->regex, $text, $matches);
		if(!$matched || count($matches[0]) < 1)
		{
			return $this->points_no_match;
		}
		else
		{
			return $this->one_time ? $this->points_match : ($this->points_match * count($matches[0]));
		}
	}
}

?>