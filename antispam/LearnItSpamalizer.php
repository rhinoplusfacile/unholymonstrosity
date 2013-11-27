<?php
namespace um;
require_once $_SERVER['DOCUMENT_ROOT'] . '/core/load.php';
Load::helper('debug');

/**
 * Description of Spamalizer
 *
 * @author Will Heyser
 */
class LearnItSpamalizer extends Spamalizer
{
	private $log_dir;
	public function __construct()
	{
		$this->log_dir = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . '/antispam/spamlog/';
	}

	private function

	public function spamalize()
	{
		$db_handle = mysqli_connect('localhost', 'root', '', 'g2drivin_c2');
		chdir($this->log_dir);
		$logs = glob('*.txt');
		$logs_parsed = array();
		foreach($logs as $log)
		{
			$contents = file_get_contents($this->log_dir . $log);
			$matches = array();
			preg_match('/POST\s*DATA:\s*Array\s*\(([^\)]+)\)/i', $contents, $matches);
			$contents = $matches[1];
			$matches = array();
			preg_match_all('/\[([^\]]+)\]\s*=>\s*([^\[]+)/i', $contents, $matches, PREG_SET_ORDER);
			foreach($matches as $match)
			{
				if(!is_numeric($match[2]))
				{
					$tokens = explode(' ', trim(preg_replace('/[^a-z1-9\'-]+/', ' ', strtolower(strip_tags($match[2]))), ' '));
					foreach($tokens as $token)
					{
						$token = mysqli_real_escape_string($token);
						if(!is_numeric($token) && strlen($token) > 2 && !preg_match('/[qwrtpsdfghjklzxcvbnm]{4,}|[aeiouy]{4,}/i', $token))
						{
//							$sql = "INSERT INTO spam_filter_kb (token, spamcount) VALUES ('$token', 1) ON DUPLICATE KEY UPDATE spamcount = spamcount + 1";
							$logs_parsed
						}
					}
				}
			}
		}
		$sql = "INSERT INTO spam_filter_kb (token, spamcount) VALUES ('$token', 1) ON DUPLICATE KEY UPDATE spamcount = spamcount + 1";

		/*$logs_parsed = array_filter($logs_parsed, function($val) { return $val > 1; });*/

		/*$number_hash = array();
		foreach($logs_parsed as $word=>$count)
		{
			if(!isset($number_hash[$count]))
			{
				$number_hash[$count] = array();
			}
			$number_hash[$count][] = $word;
		}
		uksort($number_hash, function($a, $b) { return ($a > $b ? -1 : ($a == $b ? 0 : 1)); });

		foreach($number_hash as $number=>$words)
		{
?>
<h1><?= $number ?></h1>
<ul>
<?php
			foreach($words as $word)
			{
?>
	<li><?= $word ?></li>
<?php
			}
?>
</ul>
<?php
		}
		//uasort($logs_parsed, function($a, $b) { return ($a > $b ? -1 : ($a == $b ? 0 : 1)); });
		/*
?>
<ul>
<?php
		foreach($logs_parsed as $count=>$word)
		{
?>
	<li><?= $count ?> - <?= $word ?></li>
<?php
		}
?>
</ul>

<?php*/
	}
}

?>