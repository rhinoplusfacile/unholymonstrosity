<?php
namespace um;
function session_start()
{
	if(!isset($_SESSION))
	{
		\session_start();
	}
}

function session_get($key)
{
	session_start();
	return (isset($_SESSION[$key])) ? $_SESSION[$key] : false;
}

function session_set($key, $val)
{
	session_start();
	$_SESSION[$key] = $val;
}
?>
