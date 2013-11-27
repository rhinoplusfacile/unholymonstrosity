<?php
namespace um;
require_once $_SERVER['DOCUMENT_ROOT'] . '/core/load.php';
Load::package('antispam', 'RuleBase');

/**
 * Description of LengthRule
 *
 * @author Will Heyser
 */
abstract class LengthRule extends RuleBase
{
	protected $length;

	public function __construct($points_match, $points_no_match, $length)
	{
		parent::__construct($points_match, $points_no_match);
		$this->length = $length;
	}

	abstract protected function compare($text);

	public function run($text)
	{
		return $this->compare($text) ? $this->points_match : $this->points_no_match;
	}
}

?>