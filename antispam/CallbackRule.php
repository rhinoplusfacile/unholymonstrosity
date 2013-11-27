<?php
namespace um;
require_once $_SERVER['DOCUMENT_ROOT'] . '/core/load.php';
Load::package('antispam', 'RuleBase');
/**
 * Description of CallbackRule
 *
 * @author Will Heyser
 */
class CallbackRule extends RuleBase
{

	public function __construct($points_match, $points_no_match, $callback)
	{
		parent::__construct($points_match, $points_no_match);
		$this->callback = $callback;
	}

	public function run($text)
	{
		$func = $this->callback;
		return $func($text, $this->points_match, $this->points_no_match);
	}
}

?>