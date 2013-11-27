<?php
namespace um;
require_once 'Accessor.php';

class Getter extends Accessor
{
	public function get($object, $var_name)
	{
		return $this->run($object, $var_name, new NullValue);
	}
}
?>
