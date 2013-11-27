<?php
require_once 'accessors.php';
class Pagination implements \accessors\Accessible
{
	/** @var \accessors\Accessors */
	protected static $accessors;
	
	private static function init()
	{
		if(!isset(self::$accessors))
		{
			self::$accessors = new \accessors\Accessors(get_class());
		}
	}
	
	/**
	 * @var int
	 * @get access
	 * @set filter int
	 */
	private $total;
	/**
	 * @var int
	 * @get access
	 * @set	filter int
	 */
	private $per_page = 100;
	/**
	 * @var int
	 * @get access
	 * @set filter int
	 */
	private $current_page = 1;
	
	/** @var string */
	private $base_url;
	/** @var string */
	private $base_url_query_string;
	
	/**
	 * @var string 
	 */
	private $variable = 'p';
	
	public function __construct($config)
	{
		self::init();
		foreach($config as $key=>$val)
		{
			$this->setValue($key, $val);
		}
	}
	
	public function __call($name, $args)
	{
		self::$accessors->processFunctionCall($this, $name, $args);
	}
	
	
	/**
	* Getter for base_url.
	* @return string 
	*/
	public function getBaseUrl()
	{
		return self::putTogetherUrlString($this->base_url, $this->base_url_query_string);
	}
	
	/**
	* Setter for base_url.
	* @param string $new_base_url 
	*/
	public function setBaseUrl($new_base_url)
	{
		$this->base_url = trim(self::getURLString($new_base_url));
		$this->base_url_query_string = trim(self::getQueryString($new_base_url));
		return $this;
	}
	
	/**
	* Getter for total.
	* @return int 
	*/
	public function getTotal()
	{
		return $this->total;
	}
	
	/**
	* Setter for total.
	* @param int $new_total 
	*/
	public function setTotal($new_total)
	{
		$this->total = (int)$new_total;
		return $this;
	}
	
	/**
	* Getter for current_page.
	* @return int 
	*/
	public function getCurrentPage()
	{
		return $this->current_page;
	}
	
	/**
	* Setter for current_page.
	* @param int $new_current_page 
	*/
	public function setCurrentPage($new_current_page)
	{
		//Can't have a current page below 1 or above the maximum page count)
		$this->current_page = max(min((int)$new_current_page, $this->getPageCount()), 1);
		return $this;
	}
	
	/**
	* Getter for per_page.
	* @return int 
	*/
	public function getPerPage()
	{
		return $this->per_page;
	}
	
	/**
	* Setter for per_page.
	* @param int $new_per_page 
	*/
	public function setPerPage($new_per_page)
	{
		$this->per_page = $new_per_page;
		return $this;
	}
	
	public function getPageCount()
	{
		return ceil($this->total/$this->per_page);
	}
	
	public function getPageArray()
	{
		return range(1, $this->getPageCount());
	}
	
	public function getPreviousLink()
	{
		$prev = min(max(($this->getCurrentPage()-1), 1), $this->getPageCount());
		return $this->query_string_assemble($prev);
	}
	
	public function getNextLink()
	{
		$next = max(min($this->getCurrentPage()+1, $this->getPageCount()), 1);
		return $this->query_string_assemble($next);
	}
	
	private static function getQueryString($url)
	{
		$pos = strstr($url, '?');
		if($pos !== false)
		{
			return substr($url, $pos+1);
		}
		return '';
	}
	
	private static function getURLString($url)
	{
		$pos = strstr($url, '?');
		if($pos !== false)
		{
			return trim(substr($url, 0, $pos-1));
		}
		return $url;
	}
	
	private static function putTogetherUrlString($url, $qs)
	{
		$retval = $url;
		$qs = ltrim($qs, '&');
		if($qs)
		{
			$retval .= '?' . $qs;
		}
		return $retval;
	}
	
	private function query_string_assemble($page)
	{
		if($page > 1)
		{
			$qs = $this->base_url_query_string . '&' . $this->variable . '=' . $page;
		}
		else
		{
			$qs = $this->base_url_query_string;
		}
		return self::putTogetherUrlString($this->base_url, $qs);
	}
}
?>