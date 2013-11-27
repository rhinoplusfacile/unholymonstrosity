<?php
namespace um;

/**
 * Returned when there's no stored anti-spam measures and thus the form should fail because the session is invalid or a bot has not actually visited the page to get the session.
 * Simply implements a null constructor and overrides the SpamFilter::test() method to always return false;
 *
 * @author Will Heyser
 */
class AutoFailingSpamFilter extends SpamFilter
{
	public function __construct()
	{
	}

	public function test()
	{
		return false;
	}
}

?>