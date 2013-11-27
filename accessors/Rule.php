<?php
namespace um;

abstract class Rule
{
	private $action;
	private $var_name;

	/** @var \accessors\TemplateFunction */
	protected $template;

	protected $object;
	/** @var \ReflectionClass */
	protected $robject;
	/** @var \ReflectionProperty */
	protected $rproperty;

	public function __construct($var_name, $action, TemplateFunction $template)
	{
		$this->var_name = $var_name;
		$this->action = $action;
		$this->template = $template;
	}

	protected function getValue()
	{
		return $this->template->getResult($this->object, $this->rproperty, new NullValue);
	}

	protected function getGetValueCall($inital_value_string)
	{
		if($inital_value_string)
		{
			$inital_value_string = '$' . $inital_value_string;
		}
		if($this->template->is_active())
		{
			return '$this->' . $this->template->getFuncCall() . '(' . $initial_value_string . ')';
		}
		else
		{
			return $inital_value_string;
		}
	}

	protected function setValue($value)
	{
		return $this->template->getResult($this->object, $this->rproperty, $value);
	}

	/**
	 * @param string $rule
	 * @param string $var_name
	 * @param string $action
	 * @return Rule
	 */
	public static function createRule($rule, $var_name, $action, $template)
	{
		$ruleclass = ($rule ? ucfirst($rule) : 'Callback');
		$ruleclass = preg_replace('/(.+)(Rule)/', '$1' . $ruleclass . '$2', get_class());
		return new $ruleclass($var_name, $action, $template);
	}

	protected function getAction()
	{
		return $this->action;
	}

	protected function getVarName()
	{
		return $this->var_name;
	}

	public function run($object, $value)
	{
		$this->robject = new \ReflectionClass($object);
		try
		{
			$this->rproperty = $this->robject->getProperty($this->getVarName());
			$this->rproperty->setAccessible(true);
		}
		catch(\ReflectionException $e)
		{
			if(!($this instanceof CallbackRule))
			{
				throw new PropertyNotFoundException($this->robject->getName(), $this->getVarName(), '', '', $e);
			}
		}
		$this->object = $object;
		return $this->getReturn($value);
	}

	abstract protected function getReturn($value);

	public function generateFunctionBody($getset, $class_name)
	{
		$func = CachedFunction::factory($getset, $this->getVarName(), $this->template);

//		$initial_value_string = (self::is_get($getset) ? self::generateVariableName($getset, $this->getVarName()) : '$ret_val');
//		if(self::is_get($getset))
//		{
			$func->addHeaderLine(self::line_it_up('$reval = ' . $this->getGetValueCall($initial_value_string)));
//			$func->addFooterLine(self::line_it_up('return $retval'));
//		}
//		else
//		{
//			$template_function_call = $this->getSetValueCall($initial_value_string);
//			if($template_function_call)
//			{
//				$line = $template_function_call;
//			}
//			else
//			{
//				$line = '';
//			}
//			$func->addFooterLine(self::line_it_up($line));
//			$func->addFooterLine(self::line_it_up('return $this'));
//		}
		return $func;
	}

	protected static function generateVariableName($getset, $var_name)
	{
		return (self::is_get($getset) ? '$this->' . $var_name : '$new_' . $var_name);
	}

	protected static function is_get($getset)
	{
		return ($getset == 'get');
	}

	protected static function line_it_up($line)
	{
		return $line ? $line . ';' : '';
	}
}
?>
