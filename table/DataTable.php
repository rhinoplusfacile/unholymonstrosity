<?php
namespace dvinci\table;
/**
 * Class to represent tabular data.  Output is controlled by handles to various output types, so output can be made several times from the same table in different formats if necessary.
 *
 * @author Will Heyser
 */
class DataTable
{
	const SHOW_NO_HEADERS = 0;
	const SHOW_ROW_HEADERS = 1;
	const SHOW_COL_HEADERS = 2;
	const SHOW_ALL_HEADERS = 3;
	/** @var \dvinci\table\DataRows */
	protected $data;
	/** @var \dvinci\table\Headers */
	protected $headers;
	
	/** @var \dvinci\table\DataRow */
	protected $current_row;
	
	/** @var int */
	private $display_headers = self::SHOW_ALL_HEADERS;
	/** @var bool */
	private $use_headers = true;	
	
	public function __construct()
	{
		$this->data = new DataRows;
		$this->headers = new Headers;
	}
	
	public function addDataRow($header_id=0, $header_label='')
	{
		$this->current_row = $this->data->addDataRow($header_id, $header_label);
		return $this->current_row;
	}
	
	public function getCurrentRow()
	{
		return $this->current_row;
	}
	
	public function addDataCell($row_header_id = null, $col_header_id=0, $data='')
	{
		/* @var $row \dvinci\table\DataRow */
		if(!isset($row_header_id))
		{
			$row = $this->current_row;
		}
		else
		{
			try
			{
				$row = $this->data->getItem($row_header_id);
			}
			catch(\Exception $e)
			{
				throw new Exception('Row "' . $row_header_id . '" does not exist.', 0, $e);
			}
		}
		return $row->addDataCell($col_header_id, $data);
	}
	
	public function addHeader($header_id=0, $header_label='', $sort_order = 0)
	{
		$this->headers->addHeader($header_id, $header_label, $sort_order);
	}
	
	public function getHeaders()
	{
		return $this->headers;
	}
	
	public function getDataRows()
	{
		return $this->data;
	}
	
	public function setDisplayHeaders($display_headers)
	{
		$this->display_headers = $display_headers;
	}
	
	public function getDisplayHeaders()
	{
		return $this->display_headers;
	}
	
	public function setUseHeaders($use_headers)
	{
		$this->use_headers = $use_headers;
	}
	
	public function getUseHeaders()
	{
		return $this->use_headers;
	}
}
?>