<?php
namespace um;
class Load
{
	private static function basepath()
	{
		return realpath(pathinfo(__FILE__, PATHINFO_DIRNAME) . '/..');
	}

	public static function package($package, $classname)
	{
		require_once self::basepath() . '/' . $package . '/' . $classname . '.php';
	}

	public static function helper($name)
	{
		require_once self::basepath() . '/helpers/' . $name . '.php';
	}
}
?>
