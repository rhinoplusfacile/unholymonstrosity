<?php
namespace um;
require_once $_SERVER['DOCUMENT_ROOT'] . '/core/load.php';
Load::package('antispam', 'RegexRule');

/**
 * Description of HTMLTagRule
 *
 * @author Will Heyser
 */
class HTMLTagRule extends RegexRule
{
	public function __construct($points_match, $points_no_match, $tag, $one_time = true)
	{
		$regex = '/<' . $tag . '[^>]*>/i';
		parent::__construct($points_match, $points_no_match, $regex, $one_time);
	}
}

?>