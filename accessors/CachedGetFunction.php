<?php
namespace um;
/**
 * Subclass to output a cached getter method.
 *
 * @author Will Heyser
 */
class CachedGetFunction extends CachedFunction
{
	/**
	 * Return the function signature 'public function get[FunctionName]()'
	 * @return string
	 */
	protected function getFunctionSignature()
	{
		return "public function get" . $this->getAccessFunctionName() . '()';
	}

	protected function setInitialHeader()
	{
		if($this->template->is_active())
		{
			//template code
		}
		else
		{
			$this->addHeaderLine('$retval = $this->' . $this->getAccessVarName());
		}
	}
}
?>