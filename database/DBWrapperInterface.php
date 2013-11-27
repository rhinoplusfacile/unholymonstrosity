<?php
namespace um;
interface DBWrapperInterface
{
	public function query($sql, $args=false);
}
?>
