<?php
namespace um;
require_once $_SERVER['DOCUMENT_ROOT'] . '/core/load.php';
require_once Load::helper('session');
require_once Load::helper('post');
require_once Load::package('antispam', 'AutoFailingSpamFilter');
require_once Load::package('antispam', 'HoneyPotField');
require_once Load::package('antispam', 'TextInputHoneyPotField');
require_once Load::package('antispam', 'TextareaHoneyPotField');
require_once Load::package('antispam', 'RadioHoneyPotField');
require_once Load::package('antispam', 'CheckboxHoneyPotField');
require_once Load::package('antispam', 'SubmitHoneyPotField');
require_once Load::package('antispam', 'SelectHoneyPotField');
require_once Load::package('antispam', 'VerifierField');
require_once Load::package('antispam', 'IDStore');
require_once Load::package('antispam', 'NameStore');
require_once Load::package('crypt', 'TwoWay');
/**
 * Class for dealing with form spam using common techniques
 *
 * @author Will Heyser
 */
class SpamFilter
{
	protected static $key = 't4n0ou~r04R`EINr:35sdn2g\3lkn*NGOldsf+gi098[[]g!#*5FN&(%$45n02n9%';
	protected static $session_id = 'dvincisf';
	protected static $session_iv_id = 'dvinciiv';
	protected static $time_to_live = 2;	//They shouldn't be able to submit this form in fewer than 2s
	protected static $expires_after = '1 day';
	private $timestamp;
	private $honeypot_fields = array();
	private $verifier_fields = array();
	/** @var \um\NameStore */
	private $field_names;
	/** @var \um\IDStore */
	private $ids;
	/** @var \um\TwoWay */
	private $crypt;

	private static function storeSpamFilter(SpamFilter $item)
	{
		session_set(self::$session_id, bin2hex($item->encrypt()));
		session_set(self::$session_iv_id, bin2hex($item->getIV()));
	}

	public static function retrieve()
	{
		$iv = hex2bin(session_get(self::$session_iv_id));
		if($iv)
		{
			$crypt = new TwoWay(self::$key, $iv);
			$decoded_string = $crypt->decrypt(hex2bin(session_get(self::$session_id)));
			$retval = @unserialize($decoded_string);
			if($retval !== false)
			{
				return $retval;
			}
		}
		return new AutoFailingSpamFilter();
	}

	public function store()
	{
		self::storeSpamFilter($this);
	}

	public function __construct($config = array())
	{
		$this->ids = new IDStore();
		$this->timestamp = time();
		$this->crypt = new TwoWay(self::$key);
		$honey_pots = isset($config['honey_pots']) ? round($config['honey_pots']) : 0;
		$verifiers = isset($config['verifiers']) ? round($config['verifiers']) : 0;
		$names = array();
		if(isset($config['field_names']))
		{
			$names = $config['field_names'];
		}
		$names_needed = $honey_pots + $verifiers;
		$this->field_names = new NameStore($names_needed, $names);
		for($i = 0; $i < $honey_pots; $i++)
		{
			$this->honeypot_fields[] = HoneyPotField::factory($this);
		}
		for($i = 0; $i < $verifiers; $i++)
		{
			$v = new VerifierField($this->getFieldName());
			$v->setFilterLink($this);
			$this->verifier_fields[] = $v;
		}
	}

	public function getIV()
	{
		return $this->crypt->getIV();
	}

	public function encrypt()
	{
		return $this->crypt->encrypt(serialize($this));
	}

	public function getFieldName()
	{
		return $this->field_names->getName();
	}

	public function output()
	{
		$of = function($x) { return $x->output(); };
		echo '<div class="sffields">';
		$temp = array_map($of, $this->honeypot_fields);
		echo implode('<br />' . PHP_EOL, $temp);
		$temp = array_map($of, $this->verifier_fields);
		echo implode(PHP_EOL, $temp);
		echo '</div>';
	}

	public function test()
	{
		$ts = time();
		if(($this->timestamp < strtotime('-' . self::$expires_after, $ts))		//Is this older than the expiration date?
			|| ($this->timestamp > ($ts - self::$time_to_live)))			//Was the response too quick?
		{
			return false;
		}
		foreach(array_merge($this->honeypot_fields, $this->verifier_fields) as $field)
		{
			if(!$field->test())
			{
				return false;
			}
		}
		return true;
	}

	public function generateId($name='')
	{
		return $this->ids->getId($name);
	}
}

?>