<?php
namespace um;

abstract class Accessor
{
	private $rules = array();
	
	public function __construct($class)
	{
		$getset = get_class($this);
		$matches = array();
		preg_match('/(get|set)ter/i', $getset, $matches);
		$getset = strtolower($matches[1]);
		
		$rclass = new \ReflectionClass($class);
		$class_comment = $rclass->getDocComment();
		$this->CommentParse($getset, null, $rclass, $class_comment);
//		$matches = array();
//		if(preg_match_all('/@' . $getset . '\s*\$(\w+)\s*(\w+)\s*([\w|:]+)?/i', $class_comment, $matches))
//		{
//			$class_types = self::form_type_map($class_comment);
//			foreach(range(0, count($matches[0])-1) as $id)
//			{
//				$var_name = ltrim($matches[1][$id], '$');
//				if(!$matches[3][$id])
//				{
//					$action = $class_types[$var_name];
//				}
//				else
//				{
//					$action = $matches[3][$id];
//				}
//				$this->setRule($var_name, $matches[2][$id], $action);
//			}
//		}
		
		foreach($rclass->getProperties(\ReflectionProperty::IS_PRIVATE) as /* @var $rproperty \ReflectionProperty */$rproperty)
		{
//			$matches = array();
			$comment = $rproperty->getDocComment();
			$this->CommentParse($getset, $rproperty, $rclass, $comment);
//			if(preg_match('/@' . $getset . '\s*(\w+)\s*(\w|:+)?/i', $comment, $matches))
//			{
//				if(!isset($matches[2]) || !$matches[2])
//				{
//					$action = self::form_type_map($comment, $rproperty->getName());
//				}
//				else 
//				{
//					$action = $matches[2];
//				}
//				$this->setRule($rproperty->getName(), $matches[1], $action);
//			}
		}
	}
	
	private function CommentParse($getset, \ReflectionProperty $property=null, \ReflectionClass $rclass, $comment)
	{
		$func_list = array();
		$matches = array();
		$regex = '/\s*@template\s*' . $getset . '\s*(\((?<vars>(\$\w+\s*)+)\))?\s*(?<func>\w+)\s*/';
		if(preg_match_all($regex, $comment, $matches))
		{
			foreach(range(0, count($matches[0])-1) as $id)
			{
				try
				{
					$rmethod = $rclass->getMethod($matches['func'][$id]);
					if(!isset($property))
					{
						$vars = preg_split('/\s*\$/i', trim($matches['vars'][$id], ' $'));
					}
					else
					{
						$vars = array($property->getName());
					}
					foreach($vars as $var)
					{
						$func_list[$var] = $rmethod;
					}
				}
				catch(\ReflectionException $e)
				{
					throw new MethodMissingException('', $rclass->getName(), $matches['func'][$id]);
				}
			}
		}
		$matches = array();
		$regex = '/@' . $getset . '\s*' . (!isset($property) ? '\$(?<var_name>\w+)\s*' : '') . '(?<type>\w+)\s*(?<action>[\w|:]+)?/';
		if(preg_match_all($regex, $comment, $matches))
		{
			$types = self::form_type_map($comment);
			foreach(range(0, count($matches[0])-1) as $id)
			{
				if(!isset($property))
				{
					$var_name = ltrim($matches['var_name'][$id], '$');
				}
				else
				{
					$var_name = $property->getName();
				}
				if(!$matches['action'][$id])
				{
					$action = $types[$var_name];
				}
				else
				{
					$action = $matches['action'][$id];
				}
				$type = $matches['type'][$id];
				$template = (isset($func_list[$var_name]) ? $func_list[$var_name] : null);
				$this->setRule($var_name, $type, $action, $template);
			}
			
		}
	}
	
	private function form_type_map($comment, $var_name='')
	{
		$retval = array();
		$matches = array();
		$tag = $var_name ? 'var' : 'property';
		$re = '/@' . $tag . '\s*([\w\\|]+)' . (!$var_name ? '\s*$([\w]+)' : '') . '/i';
		if(preg_match_all($re, $comment, $matches))
		{
			foreach(range(0, count($matches[0])-1) as $id)
			{
				if($var_name)
				{
					$retval = $matches[1][$id];
				}
				else
				{
					$retval[ltrim($matches[1][$id], '$')] = $matches[2][$id];
				}
			}
			return $retval;
		}
	}
	
	public function setRule($var_name, $rule, $action='', \ReflectionMethod $template_method=null)
	{
		$template = new TemplateFunction($template_method);
		$rule = Rule::createRule($rule, $var_name, $action, $template);
		$this->rules[$var_name] = $rule;
	}
	
	/**
	 * @param string $var_name
	 * @return Rule
	 */
	protected function getRule($var_name)
	{
		if(isset($this->rules[$var_name]))
		{
			return $this->rules[$var_name];
		}
		return false;
	}
	
	protected function validate($object)
	{
		if(!is_object($object))
		{
			throw new RuleException('', get_class($this) . ' must operate on object.');
		}
	}
	
	protected function run($object, $var_name, $value)
	{
		$this->validate($object);
		$rule = $this->getRule($var_name);
		if($rule)
		{
			return $rule->run($object, $value);
		}
		throw new RuleNotFoundException($var_name);
	}
}
?>