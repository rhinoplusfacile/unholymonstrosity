<?php
namespace um;
require_once $_SERVER['DOCUMENT_ROOT'] . '/core/load.php';
Load::package('antispam', 'LengthRule');
/**
 * Description of LengthMinRule
 *
 * @author Will Heyser
 */
class LengthMinRule extends LengthRule
{
	protected function compare($text)
	{
		return (strlen($text) >= $this->length);
	}
}

?>