<?php
namespace dvinci\table;
/**
 * Header for data tables
 *
 * @author Will Heyser
 */
class Header
{
	/** @var string */
	private $id;
	/** @var string */
	private $label;
	/** @var int */
	private $sort_order;
	
	public function __construct($id, $label, $sort_order=0)
	{
		$this->id = $id;
		$this->label = $label;
		$this->sort_order = $sort_order;
	}
	
	/**
	* Getter for id.
	* @return string 
	*/
	public function getId()
	{
		return $this->id;
	}
	
	/**
	* Setter for id.
	* @param string $new_id 
	*/
	public function setId($new_id)
	{
		$this->id = $new_id;
		return $this;
	}
	
	/**
	* Getter for label.
	* @return string 
	*/
	public function getLabel()
	{
		return $this->label;
	}
	
	/**
	* Setter for label.
	* @param string $new_label 
	*/
	public function setLabel($new_label)
	{
		$this->label = $new_label;
		return $this;
	}
	
	/**
	* Getter for sort_order.
	* @return int 
	*/
	public function getSortOrder()
	{
		return $this->sort_order;
	}
	
	/**
	* Setter for sort_order.
	* @param int $new_sort_order 
	*/
	public function setSortOrder($new_sort_order)
	{
		$this->sort_order = $new_sort_order;
		return $this;
	}
}

?>