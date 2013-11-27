<?php
namespace um;
require_once $_SERVER['DOCUMENT_ROOT'] . '/core/load.php';
Load::package('antispam', 'RuleBase');

/**
 * Description of ForbiddenWordsRule
 *
 * @author Will Heyser
 */
class ForbiddenWordsRule extends RuleBase
{
	protected $words_list;
	protected $threshold;

	public function __construct($points_match, $points_no_match, $words_list, $threshold = 0.25)
	{
		parent::__construct($points_match, $points_no_match);
		$this->words_list = $words_list;
		$this->threshold = $threshold;
	}

	public function run($text)
	{
		$words_list = $this->words_list;
		$tokens = explode(' ', trim(preg_replace('/[^a-z1-9\'-]+/', ' ', strtolower($text)), ' '));
		$bad_words = array_filter($tokens, function($val) use ($words_list)
		{
			return in_array($val, $words_list);
		});

		$bad_word_count = count($bad_words);
		$total_count = count($tokens);
		if($bad_word_count/$total_count > $this->threshold)
		{
			return $this->points_match;
		}
		return $this->points_no_match;

	}
}

?>