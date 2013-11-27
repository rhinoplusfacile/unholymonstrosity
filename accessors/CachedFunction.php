<?php
namespace um;

abstract class CachedFunction
{
	/**
	 * The variable to which this function will grant access.
	 * @var string
	 */
	private $access_var_name;
	/**
	 * The function name which will be preceded by get or set, in CamelCase.
	 * @var string
	 */
	private $access_func_name;
	/**
	 * The template function from the rule.
	 * @var \um\accessors\TemplateFunction
	 */
	protected $template;
	/**
	 * The code which will be output before the body, usually template calls or variable assignments.
	 * @var array
	 */
	private $header_lines = array();
	/**
	 * The body of the function;
	 * @var array;
	 */
	private $body_lines = array();
	/**
	 * The code which will be output after the body, usually the return.
	 * @var array
	 */
	private $footer_lines = array();

	/**
	 * Factory method to construct appropriate subclass.
	 * @param string $getset Should be either 'get' or 'set'
	 * @param string $var_name The variable to access
	 * @param \um\accessors\TemplateFunction $template The template function object.
	 * @return \um\accessors\CachedGetFunction|\um\accessors\CachedSetFunction
	 * @throws \Exception If $getset is not a valid string
	 */
	public static function factory($getset, $var_name, TemplateFunction $template)
	{
		if($getset == 'get')
		{
			return new CachedGetFunction($var_name, $template);
		}
		elseif($getset == 'set')
		{
			return new CachedSetFunction($var_name, $template);
		}
		else
		{
			throw new \Exception('Invalid argument ' . $getset . ' passed to CachedFunction::factory().');
		}
	}

	/**
	 * Constructor
	 * @param string $var_name
	 * @param \um\accessors\TemplateFunction $template
	 */
	public function __construct($var_name, TemplateFunction $template)
	{
		$this->setAccessVarName($var_name);
		$this->template = $template;
	}

	/**
	 * Takes a variable name, stores it, and uses it to set the function name in CamelCase where words were separated by underscores.
	 * @param string $var_name
	 */
	public function setAccessVarName($var_name)
	{
		$this->access_var_name = trim(strtolower($var_name));
		$this->access_func_name = explode('_', $this->access_var_name);
		array_walk($this->access_func_name, create_function('&$item, $index', '$item = ucfirst($item);'));
		$this->access_func_name = implode('', $this->access_func_name);
	}

	/**
	 * Adds a line to the header code.
	 * @param string $line
	 */
	public function addHeaderLine($line)
	{
		$this->addLine($this->header_lines, $line);
	}

	/**
	 * Adds a line to the function body code.
	 * @param string $line
	 */
	public function addBodyLine($line)
	{
		$this->addLine($this->body_lines, $line);
	}

	/**
	 * Adds a line to the function footer code.
	 * @param string $line
	 */
	public function addFooterLine($line)
	{
		$this->addLine($this->footer_lines, $line);
	}

	/**
	 * Helper. Adds a line to a collection, so that an existence check can be done once instead of three times.
	 * @param array $collection
	 * @param string $line
	 */
	private function addLine(&$collection, $line)
	{
		if($line)
		{
			$collection[] = $line;
		}
	}

	/**
	 * For subclass access to the function name.
	 * @return string
	 */
	protected function getAccessFunctionName()
	{
		return $this->access_func_name;
	}

	/**
	 * For subclass access to the variable name.
	 * @return string
	 */
	protected function getAccessVarName()
	{
		return $this->access_var_name;
	}

	/**
	 * Returns the function signature in subclasses to allow for getter/setter differentiation.
	 */
	protected abstract function getFunctionSignature();

	/**
	 * Outputs the function code to a resource handle.
	 * @param resource $handle
	 */
	public function output($handle)
	{
		fwrite($handle, "public function " . $this->getFunctionSignature() . PHP_EOL);
		fwrite($handle, '{' . PHP_EOL);
		fwrite($handle, implode(PHP_EOL, $this->header_lines));
		fwrite($handle, implode(PHP_EOL, $this->body_lines));
		fwrite($handle, implode(PHP_EOL, $this->footer_lines));
		fwrite($handle, '}' . PHP_EOL);
	}

	public static function line_it_up($line)
	{
		return $line ? $line . ';' : '';
	}
}
?>
