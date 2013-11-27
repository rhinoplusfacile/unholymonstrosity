<?php
namespace dvinci\table;
/**
 * Description of Headers
 *
 * @author Will Heyser
 */
class Headers extends \dvinci\CollectionBase
{
	public function addHeader($id=0, $label='', $sort_order = 0)
	{
		if(!$id)
		{
			$id = count($this)+1;
		}
		if(!$sort_order)
		{
			$sort_order = count($this)+1;
		}
		$header = new Header($id, $label, $sort_order);
		parent::addItem($header, $id);
		uasort($this->_items, function($a, $b)
			{ 
				$asort = $a->getSortOrder();
				$bsort = $b->getSortOrder();
				return $asort == $bsort ? 0 : ($asort > $bsort ? 1 : -1);
			});
	}
	
	public function fill()
	{
		
	}
}

?>