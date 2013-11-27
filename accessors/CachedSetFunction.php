<?php
namespace um;
/**
 * Subclass to output a cached setter method.
 *
 * @author Will Heyser
 */
class CachedSetFunction extends CachedFunction
{
	/**
	 * Return the function signature 'public function get[FunctionName]($new_[var_name])'
	 * @return string
	 */
	protected function getFunctionSignature()
	{
		return "public function set" . $this->getAccessFunctionName() . '($new_' . $this->getAccessVarName() .')';
	}
}
?>