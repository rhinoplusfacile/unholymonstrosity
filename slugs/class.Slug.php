<?php
class Slug
{
	const LENGTH = 50;
	const TABLE_PREFIX = 'g2_';
	private static $words;
	
	private $id;
	private $type;
	private $slug;
	
	private $title;
	private $url;
	private $full_slug;
	private $short_slug;
	private $num;
	
	private $update = false;
	private $remove_words = true;
	
	private $is_loaded = false;
	
	public function __construct($id = null, $type = null, $slug = null)
	{
		$this->id = $id;
		$this->type = $type;
		$this->slug = $slug;
	}
	
	/**
	* Getter for id.
	* @return int 
	*/
	public function getId()
	{
		if(!isset($this->id))
		{
			$this->load();
		}
		return $this->id;
	}
	
	/**
	* Setter for id.
	* @param int $new_id 
	*/
	public function setId($new_id)
	{
		$this->id = (int)$new_id;
		return $this;
	}
	
	/**
	* Getter for type.
	* @return string 
	*/
	public function getType()
	{
		if(!isset($this->type))
		{
			$this->load();
		}
		return $this->type;
	}
	
	/**
	* Setter for type.
	* @param string $new_type 
	*/
	public function setType($new_type)
	{
		$this->type = $new_type;
		return $this;
	}
	
	/**
	* Getter for slug.
	* @return string 
	*/
	public function getSlug()
	{
		if(!isset($this->slug))
		{
			$this->load();
		}
		return $this->slug;
	}
	
	/**
	* Setter for slug.
	* @param string $new_slug 
	*/
	public function setSlug($new_slug)
	{
		$this->slug = $new_slug;
		return $this;
	}
	
	public function setUpdate($new_update)
	{
		$this->update = (bool)$new_update;
		return $this;
	}
	
	public function setRemoveWords($new_remove_words)
	{
		$this->remove_words = (bool)$new_remove_words;
		return $this;
	}
	
	public function setTitle($slug, $title)
	{
		$slug = trim($slug);
		$title = trim($title);
		if(empty($slug))
		{
			$this->setRemoveWords(true);
			$this->title = $title;
		}
		else
		{
			$this->setRemoveWords(false);
			$this->title = $slug;
		}
		return $this;
	}
	
	private function load()
	{
		//No reason to load it if we already have all the data, so we'll xor so both can't be true
		if(!$this->is_loaded && ((isset($this->id) && isset($this->type)) xor isset($this->slug)))
		{
			$query = 'SELECT * FROM slugs WHERE ';
			$parts = array();
			if(isset($this->id))
			{
				$parts[] = "id = {$this->id}";
			}
			if(isset($this->type))
			{
				$type = quote_smart($this->type);
				$parts[] = "table_name = '" . self::TABLE_PREFIX . $type . "'";
			}
			if(isset($this->slug))
			{
				$slug = quote_smart($this->slug);
				$parts[] = "slug = '$slug'";
			}
			$query .= implode(' AND ', $parts) . ' LIMIT 1';
			$result = one_record($query);
			if(!empty($result))
			{
				$this->setId($result['id']);
				$this->setType(preg_filter('/' . self::TABLE_PREFIX . '(.*?)/', '$1', $result['table_name']));
				$this->setSlug($result['slug']);
				$this->is_loaded = true;
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			//either it's already true or it has all the information needed
			$this->is_loaded = true;
		}
		return true;
	}
	
	public function save()
	{
		if($this->getURL() != $this->getSlug())
		{
			$slug = quote_smart($this->getURL());
			$table = self::TABLE_PREFIX . $this->getType();
			$id = $this->getId();
			if($this->load() && $this->update)
			{
				$query = "UPDATE slugs SET slug = '$slug' WHERE id=$id AND table_name='$table'";
				return exe($query);
			}
			else
			{
				$add = array(
					'slug'=>$slug,
					'id'=>$id,
					'table_name'=>$table
				);
				return db_add_record('slugs', $add);
			}
			return false;
		}
		return true;
	}
	
	protected function getFullSlug()
	{
		if(!isset($this->full_slug))
		{
			$this->full_slug = self::slugify($this->title, $this->remove_words);
		}
		return $this->full_slug;
	}
	
	protected function getShortSlug()
	{
		if(!isset($this->short_slug))
		{
			$this->short_slug = $this->getFullSlug();
			if(strlen($this->short_slug) > self::LENGTH && !$this->update)
			{
				//String indexing starts at 0, but substr takes a length which starts at 1. Oy.
				$end = strpos($this->short_slug, '-', self::LENGTH);
				while($this->short_slug[$end] != '-' && $end >= self::LENGTH-1)
				{
					$end--;
				}
				$this->short_slug = trim(substr($this->short_slug, 0, $end), '-');
			}
		}
		return $this->short_slug;
	}
	
	public function getURL()
	{
		if(!isset($this->url))
		{
			if($this->title)
			{
				$this->url = $this->getShortSlug() . $this->getNum();
			}
			else
			{
				$this->url = $this->getSlug();
			}
		}
		return $this->url;
	}
		
	public function getNum()
	{
		if(!isset($this->num))
		{
			$this->num = '';
			while(($add = self::getDuplicateNum(($this->getShortSlug() . $this->num), $this->update)) > 1)
			{
				$this->num = '-' . $add;
			}
		}
		return $this->num;
	}
	
	private static function slugify($text, $remove_words = true)
	{
		$return = strtolower($text);
		$return = preg_replace('/[^-a-zA-Z0-9\s-]/', '', $return);
		if($remove_words)
		{
			$return = preg_replace(self::getWords(), ' ', $return);
		}
		$return = preg_replace('/\s+/', '-', trim($return));
		$return = preg_replace('/-+/', '-', $return);
		return $return;
	}
	
	private static function getWords()
	{
		if(!isset(self::$words))
		{
			self::$words = array('a', 'an', 'the', 'in', 'is', 'on', 'and', 'or');
			array_walk(self::$words, function(&$val) {
				$val = '/\b' . $val . '\b/';
			});
		}
		return self::$words;
	}
	
	private static function getDuplicateNum($search, $update = false)
	{
		$query = "SELECT * FROM slugs WHERE slug LIKE '$search%'";
		$result = exe($query);
		return $result['found_rows'] + ($update ? 0 : 1);
	}
}
?>