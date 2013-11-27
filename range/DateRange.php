<?php
namespace dvinci;
/**
 * Class to express date ranges.
 *
 * @author Will Heyser
 */
require_once 'Range.php';

abstract class DateRangeInterval
{
	const DAYS = 1;
	const WEEKS = 2;
	const MONTHS = 3;
	const YEARS = 4;
}

class DateRange extends Range
{
	private $format;
	
	public function __construct($start, $end, $format='Y-m-d')
	{
		$this->setStart($start);
		$this->setEnd($end);
		$this->format = $format;
	}
	
	public function getStart($format = '')
	{
		return $this->format(parent::getStart(), $format);
	}

	public function setStart($new_start)
	{
		parent::setStart($this->date_convert($new_start));
	}
	
	public function getEnd($format = '')
	{
		return $this->format(parent::getEnd(), $format);
	}

	public function setEnd($new_end)
	{
		parent::setEnd($this->date_convert($new_end));
	}
	
	public function contains($date)
	{
		return parent::contains($this->date_convert($date));
	}
	
	public function getIntervals($interval = 0, $by_type = true)
	{
				
		if($by_type)
		{
			$step = '+1 day';
			switch($interval)
			{
			case DateRangeInterval::DAYS:
				$step = '+1 day';
				break;
			case DateRangeInterval::WEEKS:
				$step = '+1 week';
				break;
			case DateRangeInterval::MONTHS:
				$step = '+1 month';
				break;
			case DateRangeInterval::YEARS:
				$step = '+1 year';
				break;
			default:
				$start_date = $this->getStartDate();
				$end_date = $this->getEndDate();
				/* @var $diff \DateInterval */$diff = $start_date->diff($end_date, true);
				if($diff->y > 1)
				{
					$step = '+1 year';
				}
				else
				{
					if($diff->y > 0 || $diff->m > 1)
					{
						$step = '+1 month';
					}
					else
					{
						if($diff->m > 0 || ($diff->d/7) > 1)
						{
							$step = '+1 week';
						}
					}
				}
			}			
		}
		else
		{
			$interval = ($interval ? $interval : 6);
			$start_date = $this->getStartDate();
			$end_date = $this->getEndDate();
			/* @var $diff \DateInterval */$diff = $start_date->diff($end_date, true);
			$days = $diff->days;
			$step = '+' . floor($days / $interval) . 'days';
		}
		return $this->getTimedIntervals($step);
	}
	
	private function getStartDate()
	{
		$start_date = new \DateTime;
		$start_date->setTimestamp(parent::getStart());
		return $start_date;
	}
	
	private function getEndDate()
	{
		$end_date = new \DateTime;
		$end_date->setTimestamp((!parent::getEnd()) ? now() : parent::getEnd());
		return $end_date;
	}
	
	private function getTimedIntervals($step)
	{
		$dates = array();
		$current = parent::getStart();
		$last = parent::getEnd();
		while($current < $last)
		{
			$dates[] = $current;
			$current = strtotime($step, $current);
		}
		$dates[] = $last;

		return $dates;
	}
	
	private function format($date, $format)
	{
		if(empty($format))
		{
			$format = $this->format;
		}
		return date($format, $date);
	}
	
	private function date_convert($date)
	{
		if(is_string($date))
		{
			$date = strtotime($date);
		}
		return $date;
	}
}

?>
