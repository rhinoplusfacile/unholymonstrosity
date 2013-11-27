<?php
namespace um;
require_once $_SERVER['DOCUMENT_ROOT'] . '/core/load.php';
/**
 * Description of RuleBase
 *
 * @author Will Heyser
 */
abstract class RuleBase
{
	protected $points_match;
	protected $points_no_match;

	public function __construct($points_match, $points_no_match)
	{
		$this->points_match = $points_match;
		$this->points_no_match = $points_no_match;
	}

	abstract function run($text);
}

?>