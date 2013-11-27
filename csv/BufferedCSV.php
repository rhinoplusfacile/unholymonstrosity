<?php
namespace dvinci;
/**
 * Description of BufferedCSV
 *
 * @author Will Heyser
 */
class BufferedCSV extends CSV
{
	private $header_written = false;
	private $csv_headers_written = false; 
	
	public function __construct($filename = 'data', $handle = null)
	{
		parent::__construct($filename, $handle);
	}
	
	public function output()
	{
		if(!$this->csv_headers_written)
		{
			$this->csv_headers_written = true;
			$this->outputCSVHeaders();
			flush();
		}
		if(!$this->header_written)
		{
			$this->header_written = true;
			$this->outputHeader();
			flush();
		}
		$this->outputRows();
		flush();
		$this->resetRows();
	}
}

?>