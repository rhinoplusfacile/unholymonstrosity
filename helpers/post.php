<?php
namespace um;

function post_get($key)
{
	if(isset($_POST) && isset($_POST[$key]))
	{
		return $_POST[$key];
	}
}
?>
