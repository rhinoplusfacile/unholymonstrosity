<?php
namespace dvinci\table;
/**
 * Description of DataRows
 *
 * @author Will Heyser
 */
class DataRows extends \dvinci\CollectionBase
{
	public function addDataRow($header_id = 0, $header_label = '')
	{
		if(!$header_id)
		{
			$header_id = count($this)+1;
		}
		$header = new Header($header_id, $header_label);
		$row = new DataRow;
		$row->setHeader($header);
		return $this->addItem($row, $header_id);
	}
	
	public function getDataRow(Header $header)
	{
		try
		{
			return $this->getItem($header->getId());
		}
		catch(\Exception $e)
		{
			return new DataRow;
		}
	}
}

?>