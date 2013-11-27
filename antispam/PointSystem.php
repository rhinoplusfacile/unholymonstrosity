<?php
namespace um;
require_once $_SERVER['DOCUMENT_ROOT'] . '/core/load.php';
Load::package('antispam', 'LengthMinRule');
Load::package('antispam', 'HTMLTagRule');
Load::package('antispam', 'ForbiddenWordsRule');
class PointSystem
{
	private $score;
	private $rules;

	public function __construct()
	{
		$this->rules = array();
		$this->rules[] = new LengthMinRule(0, -5, 20);
		$this->rules[] = new HTMLTagRule(-1, 2, 'a', false);
		$this->rules[] = new ForbiddenWordsRule(-1, 1, array('shit', 'hell', 'cock', 'bitch', 'fuck', 'suck', 'ass', 'damn', 'lolita', 'xxx', 'porn'));
	}

	public function judge($text)
	{
		foreach($this->rules as $rule)
		{
			$this->score += $rule->run($text);
		}
	}

	public function getScore()
	{
		return $this->score;
	}
}
?>