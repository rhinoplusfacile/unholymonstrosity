<?php
namespace dvinci;

/**
 * Model for query strings to allow multiple values.
 *
 * @author Will Heyser
 */
class QueryString
{
	private $_items = array();
	
	public function __construct($items = array())
	{
		if(!empty($items))
		{
			foreach($items as $key=>$val)
			{
				$this->set($key, $val);
			}
		}
	}
	
	public function get($key)
	{
		return isset($this->_items[$key]) ? $this->_items[$key] : false;
	}
	
	public function set($key, $val)
	{
		$this->_items[$key] = $val;
	}
	
	public function delete($key)
	{
		unset($this->_items[$key]);
	}
	
	public function output($without = array())
	{
		$output_items = array();
		foreach($this->_items as $key=>$val)
		{
			if(empty($without) || !in_array($key, $without))
			{
				if(is_array($val))
				{
					foreach($val as $k=>$v)
					{
						$output_items[] = urlencode($key . '[' . $k . ']') . '=' .  urlencode($v);
					}
				}
				else
				{
					$output_items[] = urlencode($key) . '=' . urlencode($val);
				}
			}
		}
		
		$output = '';
		if(!empty($output_items))
		{
			$output = '?' . implode('&', $output_items);
		}
		return $output;
	}
	
	public function __toString()
	{
		return $this->output();
	}
}

?>
