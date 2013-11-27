<?php
namespace um;
interface SetInterface
{
	public function addItems();
	public function removeItems();
	public function contains($subset);
	public function toArray();
	public static function compare($a, $b);
	public function equal();
}
?>
