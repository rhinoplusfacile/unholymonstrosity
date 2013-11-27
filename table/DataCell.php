<?php
namespace dvinci\table;
/**
 * Description of DataCell
 *
 * @author Will Heyser
 */
class DataCell
{
	private $header_id;
	private $data;
	
	public function __construct($header_id, $data)
	{
		$this->header_id = $header_id;
		$this->data = $data;
	}
	
	/**
	* Getter for header_id.
	* @return string 
	*/
	public function getHeaderId()
	{
		return $this->header_id;
	}
	
	/**
	* Setter for header_id.
	* @param string $new_header_id 
	*/
	public function setHeaderId($new_header_id)
	{
		$this->header_id = $new_header_id;
		return $this;
	}
	
	/**
	* Getter for data.
	* @return string 
	*/
	public function getData()
	{
		return $this->data;
	}
	
	/**
	* Setter for data.
	* @param string $new_data 
	*/
	public function setData($new_data)
	{
		$this->data = $new_data;
		return $this;
	}
}

?>