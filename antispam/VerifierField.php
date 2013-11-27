<?php
namespace um;
require_once $_SERVER['DOCUMENT_ROOT'] . '/core/load.php';
require_once Load::package('random_string', 'RandomString');
/**
 * Description of VerifierField
 *
 * @author Will Heyser
 */
class VerifierField extends Field
{
	protected $value;

	public function __construct()
	{
		$name = new RandomString(RandomString::ALPHA, array(5, 20));
		parent::__construct($name->getValue());
		$value = new RandomString(RandomString::ALPHANUM, array(20, 50));
		$this->value = $value->getValue();
	}

	public function output()
	{
		return '<input type="hidden" name="' . $this->name . '" value="' . $this->value . '" />';
	}

	public function test()
	{
		return (post_get($this->name) == $this->value);
	}
}

?>