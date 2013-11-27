<?php
namespace um;

require_once $_SERVER['DOCUMENT_ROOT'] . '/core/load.php';
require_once Load::helper('utility');
require_once Load::helper('random');
require_once Load::package('antispam', 'Field');

/**
 * Creates a "honey-pot" field for spam detection.
 *
 * @author Will Heyser
 */
abstract class HoneyPotField extends Field
{
	const INPUT_TEXT = 1;
	const TEXTAREA = 2;
	const RADIO = 3;
	const CHECKBOX = 4;
	const SUBMIT = 5;
	const SELECT = 6;

	public static function factory(SpamFilter $filter_link, $name=null, $type=null)
	{
		if(!isset($name))
		{
			$name = $filter_link->getFieldName();
		}
		$type = self::type_it_up($type);
		switch($type)
		{
		case self::INPUT_TEXT:
			$retval = new TextInputHoneyPotField($name);
			break;
		case self::TEXTAREA:
			$retval = new TextareaHoneyPotField($name);
			break;
		case self::RADIO:
			$retval = new RadioHoneyPotField($name);
			break;
		case self::CHECKBOX:
			$retval = new CheckboxHoneyPotField($name);
			break;
		case self::SUBMIT:
			$retval = new SubmitHoneyPotField($name);
			break;
		case self::SELECT:
			$retval = new SelectHoneyPotField($name);
			break;
		}
		$retval->setFilterLink($filter_link);
		return $retval;
	}

	public function __construct($name)
	{
		parent::__construct($name);
	}

	public function test()
	{
		return (post_get($this->name)) ? false : true;
	}

	protected static function type_it_up($type)
	{
		if(is_array($type))
		{
			$type = self::type_it_up(array_rand($type));
		}
		elseif(!is_numeric($type))
		{
			$type = rand(1,6);
		}
		$type = round($type);
		return minmax($type, 1, 6);
	}

	protected static function getLabel($id, $message='This field is not supposed to be filled out by humans. If you can see this field, please do not fill it out.')
	{
		return "<label for=\"$id\">$message</label>";
	}

	protected function generateId($name='')
	{
		if(!$name)
		{
			$name = $this->name;
		}
		if(!isset($this->filter_link))
		{
			throw new Exception('Link to SpamFilter containing this field was not set; generateId cannot be called until setFilterLink is called.');
		}
		return 'hp' . $this->filter_link->generateId($name);
	}
}
?>