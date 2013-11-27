<?php
namespace dvinci;
/**
 * Simple range object.
 *
 * @author Will Heyser
 */
class Range
{
	private $start;
	private $end;
	
	public function __construct($start, $end)
	{
		$this->setStart($start);
		$this->setEnd($end);
	}
	
	public function getStart()
	{
		return $this->start;
	}

	public function setStart($new_start)
	{
		$this->start = $new_start;
	}
	
	public function getEnd()
	{
		return $this->end;
	}

	public function setEnd($new_end)
	{
		$this->end = $new_end;
	}
	
	public function contains($item)
	{
		return ((!$this->start || $item > $this->start) && (!$this->end || $item < $this->end));
	}
}

?>
