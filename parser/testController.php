<?php
use PIMS\EvaluationQuestion, PIMS\UserEvaluationAnswers, PIMS\UserEvaluationComments, PIMS\admin\reports\UserEvals, PIMS\PIM;
require_once('siteBase.php');

class C
{
	
}
class A extends C
{
	
}

class B extends C
{
}
require_once 'lexer.php';
/**
 * Specific improvements controller for Development_Screening
 *
 * @author Will Heyser
 */
class testController extends siteBase
{
	
	public function parser()
	{
		$str = 'erggggabc';
		$matches = array();
		$index = 0;
		if(preg_match('/^er(g+)/ix', substr($str, $index), $matches))
		{
			$index += (strlen($matches[0]) - 1);
		}
		ddd($index, $str[$index]);
		$t = new Lexer('m123{n{(q(1)=yes|| q2=no)&&q3>=2}den{all}}');
		$toookens = $t->getTokens();
		
		foreach($toookens as $tarken)
		{
			ddnd($tarken);
		}
	}
	
	public function __construct()
	{
		parent::__construct(false, true);
		$this->output->enable_profiler(true);
	}
	
	public function index()
	{
		$a = new A;
		$b = new B;
		ddd($a, $b);
	}
	
	public function _not_set($value, $args)
	{
		$arg_ar = array();
		preg_match('/([^|]+?)\|([^|]+?)\|([^(]+?)\(([^)]+?)\)/', $args, $arg_ar);
		$yesno = $arg_ar[1];
		$field_id = $arg_ar[2];
		$field_range = $arg_ar[3];
		$fields = explode(',', $arg_ar[4]);
		$a = array(2=>'y');
		$validation_message = 'If ' . $yesno . ' to ' . $field_id . ', do not answer questions ' . $field_range;
		foreach($fields as $id)
		{
			if(isset($a[$id]))
			{
				echo $validation_message;
				return false;
			}
		}
		return true;
	}
	
	private function containsByField($needle, $haystack)
	{
		if(is_array($needle))
		{
			$temp = array_diff($needle, array_keys($haystack));
			return empty($temp);
		}
		else
		{
			return key_exists($needle, $haystack);
		}
	}
	
	public function test_match()
	{
		ddd($this->test_array('answer[2][\'a\'][]'));
	}
	
	private function test_array($str)
	{
		$post = array(
			'answer' => array(
				1 => array(
					'a' => 'a is the value',
					'b' => 'b is the value'
					),
				2 => array(
					'a' => array('value a 1', 'value a 2'),
					'b' => 'b is the value'
					)
				)
			);
		$var_name = $str;
		$indexes = array();
		$pos = strpos($str, '[');
		if($pos !== false)
		{
			$var_name = substr($str, 0, $pos);
			$array_indexes = substr($str, $pos);
			$match = array();
			preg_match_all('/\[([^\]]+?)\]/i', $array_indexes, $match);
			$indexes = $match[1];
			array_walk($indexes, function(&$val)
			{
				$val = trim($val, '\'"');
				if(is_numeric($val))
				{
					$val = (int)$val;
				}
			});
		}
		$retval = $post[$var_name];
		foreach($indexes as $index)
		{
			$retval = $retval[$index];
		}
		return $retval;
	}
	
	public function testFieldTests()
	{
		$this->output->enable_profiler(TRUE);
		$cycle = new \PIMS\Cycle;
		$cycle->load(54339);
		$forms = new \PIMS\UserForms;
		$forms->loadByCycle($cycle);
		/* @var $f \PIMS\FieldTests */$f = \PIMS\FieldTests::getInstance(\PIMS\FieldTests::ASTHMA);
		$measure = new \PIMS\Measure;
		$measure->load(61);
		foreach($forms as /* @var $form \PIMS\UserForms */$form)
		{
			ddnd($f->test($form, $measure));
		}
	}
}

?>
