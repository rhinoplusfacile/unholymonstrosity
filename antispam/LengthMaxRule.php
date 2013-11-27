<?php
namespace um;
require_once $_SERVER['DOCUMENT_ROOT'] . '/core/load.php';
Load::package('antispam', 'LengthRule');
/**
 * Description of LengthMaxRule
 *
 * @author Will Heyser
 */
class LengthMaxRule extends LengthRule
{
	protected function compare($text)
	{
		return (strlen($text) <= $this->length);
	}
}

?>