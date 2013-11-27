<?php
class Pagination
{
	private $total;
	private $per_page = 100;
	private $current_page = 1;
	
	/** @var string */
	private $base_url;
	/** @var string */
	private $base_url_query_string;
	
	private $range_size = 3;
	
	/** @var string */
	private $variable = 'p';
	
	private $automate = true;
	
	private $prev_range;
	private $next_range;
	
	public function __construct($config = array())
	{
		foreach($config as $key=>$val)
		{
			$this->setValue($key, $val);
		}
	}
	
	protected function useGet()
	{
		$page = $this->getAutoPageValue();
		if($page)
		{
			$this->setCurrentPage($page);
		}
	}
	
	/**
	* Setter for use_get.
	* @param bool $new_use_get 
	*/
	public function setAutomate($new_automate)
	{
		$this->automate = (bool)$new_automate;
		return $this;
	}
	
	private function setValue($key, $val)
	{
		$key = explode('_', $key);
		array_walk($key, create_function('&$item, $index', '$item = ucfirst($item);'));
		$setter = 'set' . implode('', $key);
		if(is_callable(array($this, $setter)))
		{
			$this->$setter($val);
		}
	}
	
	public function setVariable($variable)
	{
		$this->variable = $variable;
	}
	
	/**
	* Getter for range_size.
	* @return int 
	*/
	public function getRangeSize()
	{
		return $this->range_size;
	}
	
	/**
	* Setter for range_size.
	* @param int $new_range_size 
	*/
	public function setRangeSize($new_range_size)
	{
		$this->range_size = ($new_range_size ? $new_range_size : $this->range_size);
		return $this;
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
	
	public function getAutoPageValue()
	{
		if($this->automate)
		{
			if(isset($_GET[$this->variable]))
			{
				return (int)$_GET[$this->variable];
			}
			return 1;
		}
		return null;
	}
	
	/**
	* Setter for current_page.
	* @param int $new_current_page 
	*/
	public function setCurrentPage($new_current_page)
	{
		//Can't have a current page below 1 or above the maximum page count)
		$this->current_page = $this->normalize_page((int)$new_current_page);
		$this->prev_range = $this->next_range = null;
		return $this;
	}
	
	public function validate($page = 0)
	{
		$this->useGet();
		if(!$page)
		{
			$page = $this->getAutoPageValue();
		}
		if($this->getCurrentPage() != $page && $this->total)
		{
			if($this->automate)
			{
				redirect($this->getCurrentLink());
			}
			else
			{
				return false;
			}
		}
		return true;
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
		if(!$this->per_page)
		{
			return 0;
		}
		return ceil($this->total/$this->per_page);
	}
	
	protected function getPageArray()
	{
		if($this->getPageCount())
		{
			return range(1, $this->getPageCount());
		}
		return array();
	}
	
	public function getPreviousRange($size=0)
	{
		if(!$this->getRangeSize())
		{
			$first_page = 1;
			$size = 0;
		}
		else
		{
			$first_page = max($this->getCurrentPage() - ($this->getRangeSize() + $size), 1);
		}
		$prev_page = $this->getCurrentPage() - 1;
		if((!isset($this->prev_range) || $size) && $prev_page > 0)
		{
			$this->prev_range = range($first_page, $prev_page);
		}
		if(!isset($this->prev_range))
		{
			$this->prev_range = array();
		}
		return $this->prev_range;
	}
	
	public function getNextRange($size=0)
	{
		if(!$this->getRangeSize())
		{
			$last_page = $this->getPageCount();
			$size = 0;
		}
		else
		{
			$last_page = min(($this->getCurrentPage() + $this->getRangeSize() + ($size)), $this->getPageCount());
		}
		$next_page = $this->getCurrentPage() + 1;
		if((!isset($this->next_range) || $size) && $next_page <= $this->getPageCount())
		{
			$this->next_range = range($next_page, $last_page);
		}
		if(!isset($this->next_range))
		{
			$this->next_range = array();
		}
		return $this->next_range;
	}
	
	public function getPreviousLink()
	{
		return $this->query_string_assemble($this->getCurrentPage() - 1);
	}
	
	public function getNextLink()
	{
		return $this->query_string_assemble($this->getCurrentPage() + 1);
	}
	
	public function getCurrentLink()
	{
		return $this->query_string_assemble($this->getCurrentPage());
	}
	
	public function getLinks()
	{
		$prev_range = $this->getPreviousRange();
		$prev_size = ($prev_range ? ($this->getRangeSize() - count($prev_range)) : $this->getRangeSize());
		$next_range = $this->getNextRange();
		$next_size = ($next_range ? ($this->getRangeSize() - count($next_range)) : $this->getRangeSize());
		if($prev_size)
		{
			$next_range = $this->getNextRange($prev_size);
		}
		if($next_size)
		{
			$prev_range = $this->getPreviousRange($next_size);
		}
		$links = array();
		$pages = array_merge($prev_range, $next_range);
		foreach($pages as $page_num)
		{
			$links[$page_num] = $this->query_string_assemble($page_num);
		}
		$links[$this->getCurrentPage()] = '';
		ksort($links);
		return $links;
	}
	
	private static function getQueryString($url)
	{
		$pos = strpos($url, '?');
		if($pos !== false)
		{
			return trim(substr($url, $pos+1));
		}
		return '';
	}
	
	private static function getURLString($url)
	{
		$pos = strpos($url, '?');
		if($pos !== false)
		{
			return trim(substr($url, 0, $pos));
		}
		return $url;
	}
	
	private static function putTogetherUrlString($url, $qs)
	{
		$retval = (string)$url;
		$qs = ltrim($qs, '&');
		if($qs)
		{
			$retval .= '?' . $qs;
		}
		return $retval;
	}
	
	private function query_string_assemble($page)
	{
		if($page != $this->normalize_page($page))
		{
			return null;
		}
		elseif($page > 1)
		{
			$qs = $this->base_url_query_string . '&' . $this->variable . '=' . $page;
		}
		else
		{
			$qs = $this->base_url_query_string;
		}
		return self::putTogetherUrlString($this->base_url, $qs);
	}
	
	private function normalize_page($page)
	{
		$page = max($page, 1);
		$page = min($page, $this->getPageCount());
		return $page;
	}
}

class OutputPages
{
	protected $pagination;
	
	public function __construct(Pagination $pagination)
	{
		$this->pagination = $pagination;
	}
	
	public function output()
	{
		if($this->pagination->getPageCount() > 1)
		{
			$prev_link = $this->pagination->getPreviousLink();
			if($prev_link)
			{
			?>
			<a href="<?= $prev_link ?>">Prev</a>
			<?php
			}
			else
			{
			?>
			<span>Prev</span>
			<?php
			}
			$links = $this->pagination->getLinks();
			if(count($links))
			{
				foreach($links as $page => $link)
				{
					if($link)
					{
			?>
			<a href="<?= $link ?>"><?= $page ?></a>
			<?php
					}
					else
					{
			?>
			<span><?= $page ?></span>
			<?php
					}
				}
			}
			$next_link = $this->pagination->getNextLink();
			if($next_link)
			{
			?>
			<a href="<?= $next_link ?>">Next</a>
			<?php
			}
			else
			{
			?>
			<span>Next</span>
			<?php
			}
		}
	}
}
?>