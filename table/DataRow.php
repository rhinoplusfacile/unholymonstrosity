<?php
namespace dvinci\table;
/**
 * Description of DataRow
 *
 * @author Will Heyser
 */
class DataRow extends \dvinci\CollectionBase
{
	/** @var \dvinci\table\Header */
	private $header;
	
	/**
	* Getter for header.
	* @return \dvinci\table\Header 
	*/
	public function getHeader()
	{
		return $this->header;
	}
	
	/**
	* Setter for header.
	* @param \dvinci\table\Header $new_header 
	*/
	public function setHeader(Header $new_header)
	{
		$this->header = $new_header;
		return $this;
	}
	
	public function addDataCell($header_id=0, $data='')
	{
		if(!$header_id)
		{
			$header_id = count($this)+1;
		}
		$cell = new DataCell($header_id, $data);
		return parent::addItem($cell, $header_id);
	}
	
	public function getDataCell(Header $header)
	{
		try
		{
			return $this->getItem($header->getId());
		}
		catch(\Exception $e)
		{
			return new DataCell($header->getId(), '');
		}
	}
	
	public function fill(Headers $col_headers, $value='')
	{
		foreach($col_headers as /* @var $header \dvinci\table\Header */$header)
		{
			$this->addDataCell($header->getId(), $value);
		}
	}
}

?>