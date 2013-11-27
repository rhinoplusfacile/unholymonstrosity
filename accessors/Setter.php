<?php
namespace um;
require_once 'Accessor.php';

class Setter extends Accessor
{
	public function set($object, $var_name, $value)
	{
		return $this->run($object, $var_name, $value);
	}

}
?>
