<?php
if(!function_exists('ddnd'))
{
	function ddnd()
	{
		$n = func_num_args();
		$args = func_get_args();
		for($i = 0; $i < $n; $i++)
		{
			echo $i+1 . ': ';
			var_dump($args[$i]);
		}
	}
}

if(!function_exists('ddd'))
{
	function ddd()
	{
		call_user_func_array('ddnd', func_get_args());
		die;
	}
}

if(!function_exists('vddd'))
{
	function vddd()
	{
		ini_set('xdebug.var_display_max_data', '-1');
		call_user_func_array('ddd', func_get_args());
	}
}

if(!function_exists('vddnd'))
{
	function vddnd()
	{
		ini_set('xdebug.var_display_max_data', '-1');
		call_user_func_array('ddnd', func_get_args());
	}
}

if(!function_exists('vvddd'))
{
	function vvddd()
	{
		ini_set('xdebug.var_display_max_data', '-1');
		ini_set('xdebug.var_display_max_depth', '-1');
		call_user_func_array('ddd', func_get_args());
	}
}

if(!function_exists('vvddnd'))
{
	function vvddnd()
	{
		ini_set('xdebug.var_display_max_data', '-1');
		ini_set('xdebug.var_display_max_depth', '-1');
		call_user_func_array('ddnd', func_get_args());
	}
}

if(!function_exists('sddd'))
{
	function sddd()
	{
		ini_set('xdebug.var_display_max_depth', '1');
		call_user_func_array('ddd', func_get_args());
	}
}

if(!function_exists('sddnd'))
{
	function sddnd()
	{
		ini_set('xdebug.var_display_max_depth', '1');
		call_user_func_array('ddnd', func_get_args());
	}
}

if(!function_exists('m'))
{
	function m($message='')
	{
		static $static_message = 1;
		$bt = debug_backtrace(false);
		$caller = array_shift($bt);
		echo '<div><strong>' . ($message ? $message : $static_message) . ' - ' . $caller['file'] . ' - ' . $caller['line'] . '</strong></div>';
		$static_message++;
	}
}

if(!function_exists('md'))
{
	function md($message='')
	{
		$bt = debug_backtrace(false);
		$caller = array_shift($bt);
		echo '<div><strong>' . ($message ? $message : 'IN A FIRE') . ' - ' . $caller['file'] . ' - ' . $caller['line'] . '</strong></div>';
		die;
	}
}

if(!function_exists('dd'))
{
	// Debug display information
	function dd($data, $hide=true)
	{
		echo ($hide ? '<!-- ' : '<pre>') . print_r($data, true) . ($hide ? ' -->' : '</pre>');
	}
}
?>
