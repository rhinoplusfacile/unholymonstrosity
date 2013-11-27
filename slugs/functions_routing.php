<?php

require_once('class.Slug.php');
class Slugmap
{
	private static $slugs;

	private function __construct()
	{
		throw new Exception('Constructor should not be called.');
	}

	private static function init()
	{
		self::$slugs = array();
		$query = "SELECT * FROM `slugs`";
		$result = exe($query);
		if($result['found_rows'])
		{
			while($item = mysql_fetch_array($result['result']))
			{
				$cur_type = $item['table_name'];
				if(!isset(self::$slugs[$cur_type]))
				{
					self::$slugs[$cur_type] = array();
				}
				self::$slugs[$cur_type][$item['id']] = $item['slug'];
			}
		}
	}

	public static function getSlug($cur_type, $id)
	{
		if(!isset(self::$slugs))
		{
			self::init();
		}
		return self::$slugs[$cur_type][$id];
	}
}

function slug_to_id($slug)
{
	$slug = new Slug(null, null, $slug);
	$slug->getId();
	return $slug->getId();
}

function id_to_slug($id, $table)
{
	return Slugmap::getSlug($table, $id);
}

function slugs_addOne($slug, $id, $table)
{
	$slug = new Slug($id, $table, $slug);
	return $slug->save();
}

function slugs_updateOne($slug, $id, $table)
{
	$slug = new Slug($id, $table, $slug);
	$slug->setUpdate(true);
	return $slug->save();
}

function slugs_getOneSlug($id, $table)
{
	$slug = slugs_getOne($id, $table);
	return $slug->getURL();
}

function slugs_getOne($slugorid, $table)
{
	$slug = null;
	$id = null;
	if(is_numeric($slugorid))
	{
		$id = $slugorid;
	}
	else
	{
		$slug = $slugorid;
	}
	return new Slug($id, $table, $slug);
}

function slugs_sluggify($slug, $name, $update=false)
{
	$new_slug = new Slug();
	if(empty($slug))
	{
		$new_slug->setRemoveWords(true);
		$new_slug->setTitle($name);
	}
	else
	{
		$new_slug->setRemoveWords(false);
		$new_slug->setTitle($slug);
	}
	$new_slug->setUpdate($update);
	return $new_slug->getURL();
}

function slugs_createSlug($id, $table, $slug, $name)
{
	$current_slug = slugs_getOneSlug($id, $table);
	$update = ($current_slug ? true : false);
	$new_slug = slugs_sluggify($slug, $name, $update);
	if($current_slug != $new_slug)
	{
		return $new_slug;
	}
	return false;
}

function slugs_form_url($base, $user_type_id, $category_id, $item_id)
{
	global $system;
	$user_type_map = array_flip($system['user.types']);
	$category_map = array_flip($system['curriculum.types']);
	$user_type = $user_type_map[$user_type_id];
	$category = $category_map[$category_id];
	return '/' . $base . '/' . $user_type . '/' . $category . '/' . id_to_slug($item_id, 'g2_' . $category);
}

function search_map_reroute()
{
	global $system;
	$qstring = explode('/', $_GET['url']);
	if(count($qstring) == 3)
	{
		$_GET['h'] = $qstring[0];
		$_GET['usertype'] = $system['user.types'][$qstring[1]];
		$_GET['is_topic'] = $qstring[2];
	}
}

function search_detail_reroute()
{
	global $system;
	
	$qstring = explode('/', $_GET['url']);

	if(count($qstring) == 3)
	{
		$c_map = array_flip($system['curriculum.types']);
		$_GET['usertype'] = $system['user.types'][$qstring[0]];
		$_GET['c'] = $system['curriculum.types'][$qstring[1]];
		$_GET['id'] = slug_to_id($qstring[2]);
	}
}
?>
