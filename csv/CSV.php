<?php
namespace dvinci;
/**
 * Description of CSV
 *
 * @author Will Heyser
 */
class CSV
{
	private $headers;
	private $rows;
	
	private $filename;
	
	private $handle;
	private $write_inline = true;
	
	public function __construct($filename = 'data', $handle = null)
	{
		if(isset($handle))
		{
			$this->handle = $handle;
			$this->write_inline = false;
		}
		else
		{
			$this->handle = fopen('php://output', 'w');
		}
		
		$this->filename = $filename;
		$this->headers = array();
		$this->rows = array();
		
	}
	
	public function __destruct()
	{
		fclose($this->handle);
	}
	
	public function addHeader($text, $id)
	{
		$this->headers[$id] = $text;
		foreach($this->rows as $row)
		{
			if(!isset($row[$id]))
			{
				$row[$id] = '';
			}
		}
	}
	
	public function addRow($row)
	{
		$temp = array();
		foreach($this->headers as $id=>$h)
		{
			$temp[$id] = '';
		}
		$this->rows[] = $row + $temp;
	}
	
	protected function resetRows()
	{
		unset($this->rows);
		$this->rows = array();
	}
	
	public function output()
	{
		$this->outputCSVHeaders();
		$this->outputHeader();
		$this->outputRows();
	}
	
	protected function outputCSVHeaders()
	{
		if($this->write_inline)
		{
			header('Content-type: text/csv');
			header('Cache-Control: no-store, no-cache');
			header('Content-Disposition: attachment; filename="' . $this->filename . '.csv"');
		}
	}
	
	protected function outputHeader()
	{
		$this->writeCSVLine($this->headers, ',', '"');
	}
	
	protected function outputRows()
	{
		foreach($this->rows as $row)
		{
			$this->outputRow($row);
		}
	}
	
	protected function outputRow(array $row)
	{
			$temp = array();
			foreach($this->headers as $id=>$h)
			{
				$temp[] = $row[$id];
			}
			$this->writeCSVLine($temp);
	}
	
	private function writeCSVLine($fields, $delimiter = ',', $enclosure = '"')
	{
		// Sanity Check
		if (!is_resource($this->handle))
		{
			user_error('fputcsv() expects parameter 1 to be resource, ' . gettype($this->handle) . ' given', E_USER_WARNING);
			return false;
		}
		
		$str = '';
		foreach ($fields as $cell)
		{
			$cell = str_replace($enclosure, $enclosure . $enclosure, $cell);
			$str .= $enclosure . $cell . $enclosure . $delimiter;
		}
		fputs($this->handle, substr($str, 0, -1) . PHP_EOL);
		
		return strlen($str);
	}
}
?>
