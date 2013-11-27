<?php
namespace um;
require_once 'Rule.php';

class FilterRule extends Rule
{
	const ALL_ACTIONS = 3;
	const TYPE_ACTIONS = 1;
	const NON_TYPE_ACTIONS = 2;
	private static $types = array('bool', 'boolean', 'int', 'integer', 'float', 'string', 'array', 'resource', 'object');

	public function __construct($var_name, $actions, $template)
	{
		$actions = explode('|', $actions);
		$type_filters = array_filter($actions, 'self::valid_type');
		$non_type_filters = array_diff($actions, $type_filters);
		$actions = array('non_type'=>$non_type_filters, 'type'=>$type_filters);
		parent::__construct($var_name, $actions, $template);
	}

	protected function getReturn($value)
	{
		if($value instanceof NullValue)
		{
			$retval = $this->actions($this->getValue());
			return $retval;
		}
		else
		{
			$value = $this->actions($value);
			return $this->setValue($value);
		}
	}

	protected function getAction($which = self::ALL_ACTIONS)
	{
		$actions = parent::getAction();
		$retval = array();
		if($which & self::TYPE_ACTIONS)
		{
			$retval = array_merge($actions['type'], $retval);
		}
		if($which & self::NON_TYPE_ACTIONS)
		{
			$retval = array_merge($actions['non_type'], $retval);
		}
		return $retval;
	}

	private function actions($value)
	{
		$retval = $value;
		$type_actions = $this->getAction(self::TYPE_ACTIONS);
		$filter_match = false;
		if(count($type_actions))
		{
			$retval = $this->filter($value, $type_actions);
			$filter_match = true;
		}
		foreach($this->getAction(self::NON_TYPE_ACTIONS) as $action)
		{
			$matches = array();
			preg_match('/([[:alnum:]]+):([[:alnum:]]+)/', $action, $matches);
			if(!empty($matches))						//Specify class and method to override hierarchy
			{
				if(method_exists($matches[1], $matches[2]))
				{
					$retval = call_user_func(array($matches[1], $matches[2]), $value);
					$filter_match = true;
				}
			}
			elseif(method_exists($this->object, $action))		//Check object functions next
			{
				$retval = $this->object->$action($retval);
				$filter_match = true;
			}
			elseif(function_exists($action))			//Check global functions
			{
				$retval = $action($retval);
				$filter_match = true;
			}
		}
		if($filter_match)
		{
			return $retval;
		}
		throw new \UnexpectedValueException('Value passed to filter was not of an acceptable type (' . implode('|', $this->getAction()) . ').');
	}

	private static function valid_type($type)
	{
		$type = trim($type);
		return class_exists($type) || in_array($type, self::$types);
	}

	private static function get_legit_types($value)
	{
		$actual_type = gettype($value);
		$legit_types = array();
		switch($actual_type)
		{
		case 'boolean':
			$legit_types[] = 'bool';
			$legit_types[] = 'boolean';
			break;
		case 'double':
			$legit_types[] = 'float';
			break;
		case 'integer':
			$legit_types[] = 'integer';
			$legit_types[] = 'int';
			break;
		default:
			$legit_types[] = $actual_type;
		}
		return $legit_types;
	}

	private static function is_type($value, $types)
	{
		$type_check = array_intersect(self::get_legit_types($value), $types);
		return (count($type_check) > 0);
	}

	private static function is_class($value, $types)
	{
		foreach($types as $type)
		{
			if(class_exists($type) && $value instanceof $type)
			{
				return true;
			}
		}
		return false;
	}

	private static function convert_type($value, $types)
	{
		$converted_values = array();
		foreach($types as $type)
		{
			switch($type)
			{
				case 'mixed':
					return $value;
				case 'bool':
				case 'boolean':
					$converted_values[$type] = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
					break;
				case 'int':
				case 'integer':
					$converted_values[$type] = filter_var($value, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
					break;
				case 'float':
					$converted_values[$type] = filter_var($value, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);
					break;
				case 'string':
					$converted_values[$type] = (string)$value;
					break;
				case 'array':
					$converted_values[$type] = (array)$value;
					break;
				case 'object':
					$converted_values[$type] = (object)$value;
					break;
				default:
					break;
			}
		}
		foreach($types as $type)
		{
			if(isset($converted_values[$type]))
			{
				return $converted_values[$type];
			}
		}
		return new NullValue;
	}

	private static function filter($value, $types)
	{
		if(self::is_type($value, $types) || self::is_class($value, $types))
		{
			return $value;
		}
		$converted_type = self::convert_type($value, $types);
		if($converted_type instanceof NullValue)
		{
			throw new \UnexpectedValueException('Value passed to filter was not of an acceptable type (' . implode('|', $types) . ').');
		}
		return $converted_type;
	}

	public function generateFunctionBody($getset, $class_name)
	{
		$func = parent::generateFunctionBody($getset, $class_name);
		
		$internal_var = '$this->' . $this->getVarName();
		if($getset == 'get')
		{
			$arg = '';
			$value = $internal_var;
		}
		else
		{
			$arg = '$new_' . $this->getVarName();
			$value = $args;
		}
		$var_name = $this->getVarName();
		$var_name = explode('_', $var_name);
		array_walk($var_name, create_function('&$item, $index', '$item = ucfirst($item);'));
		$var_name = implode('', $var_name);
		$lines = array();
		$lines[] = "public function {$getset}{$var_name}({$arg})";
		$lines[] = '{';
		if($this->template->is_active())
		{
			$lines[] = '';
		}
		$lines[] = "	\$retval = $value;";
		$type_actions = $this->getAction(self::TYPE_ACTIONS);
		if(count($type_actions))
		{
			foreach($type_actions as $action)
			{
				$lines[] = '';
			}
			$retval = self::filter($value, $type_actions);
			$filter_match = true;
		}
		foreach($this->getAction(self::NON_TYPE_ACTIONS) as $action)
		{
			$matches = array();
			preg_match('/([[:alnum:]]+):([[:alnum:]]+)/', $action, $matches);
			if(!empty($matches))						//Specify class and method to override hierarchy
			{
				if(method_exists($matches[1], $matches[2]))
				{
					$retval = call_user_func(array($matches[1], $matches[2]), $value);
					$filter_match = true;
				}
			}
			elseif(method_exists($this->object, $action))		//Check object functions next
			{
				$retval = $this->object->$action($retval);
				$filter_match = true;
			}
			elseif(function_exists($action))			//Check global functions
			{
				$retval = $action($retval);
				$filter_match = true;
			}
		}
		if($filter_match)
		{
			return $retval;
		}
		throw new \UnexpectedValueException('Value passed to filter was not of an acceptable type (' . implode('|', $this->getAction()) . ').');
	}
}
?>
