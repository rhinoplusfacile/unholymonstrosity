<?php
namespace um;
require_once $_SERVER['DOCUMENT_ROOT'] . '/core/load.php';
require_once Load::package('antispam', 'HoneyPotField');
require_once Load::helper('random');
require_once Load::package('random_string', 'RandomString');
/**
 * Description of MultiOptionHoneyPotField
 *
 * @author Will Heyser
 */
abstract class MultiOptionHoneyPotField extends HoneyPotField
{
	private static $options;
	private static $min;
	private static $max;
	protected $number;

	public static function setOptionsArray()
	{
		if(func_num_args())
		{
			self::$options = array_map(function($val) { return '' . $val; }, func_get_args());
		}
	}

	private static function getOptionsFromArray($num)
	{
		if(is_array(self::$options) && !empty(self::$options) && $num <= count(self::$options))
		{
			return random_pick(self::$options, $num);
		}
		else
		{
			$retval = array();
			for($i=0; $i<$num; $i++)
			{
				$option = new RandomString(RandomString::ALPHANUM, 30);
				$retval[] = $option->getValue();
			}
			return $retval;
		}
	}

	protected function getOptions()
	{
		return self::getOptionsFromArray($this->number);
	}

	public static function setRange($min=0, $max=0)
	{
		if(!isset(self::$min) || !isset(self::$max))
		{
			$min = max($min, 2);
			$max = min($max, 2);
			if($min > $max)
			{
				$min = 2;
				$max = 5;
			}
			self::$min = $min;
			self::$max = $max;
		}
	}

	public function __construct($name)
	{
		parent::__construct($name);
		self::setRange();
		$this->number = rand(self::$min, self::$max);
	}
}

?>