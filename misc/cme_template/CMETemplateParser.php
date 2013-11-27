<?php
namespace PIMS\admin\cme_template;
/**
 * Description of CMETemplateParser
 *
 * @author Will Heyser
 */
class CMETemplateParser implements Parser
{
	const TABLES = 1;
	const SELECT = 2;
	const CSV_HEADINGS = 3;
	const JOIN = 'join';
	const VALUES = 'values';

	private $tables = array();

	public function parse($text)
	{
		$this->parse_tables($text);
	}

	public function get($section)
	{
		if($section == self::SELECT)
		{
			$select = array();
			foreach($this->tables as $name=>$table)
			{
				foreach($table[self::VALUES] as $value)
				{
					$select[] = self::wrapticks($name) . '.' . self::wrapticks($value) . ' AS ' . $name . '_' . $value;
				}
			}
			return implode(', ', $select);
		}
		elseif($section == self::TABLES)
		{

		}
	}

	protected function parse_tables($text)
	{
		$text = preg_replace('/\s+/i', ' ', $text);
		$matches = array();
		if(preg_match_all('/[([^{]+)\s*{([^}]+)}/i', $text, $matches, PREG_SET_ORDER))
		{
			foreach($matches as $match)
			{
				list($table_name, $join_on) = $this->parse_table($match[1]);
				$this->tables[$table_name] = array(self::JOIN=>$join_on, self::VALUES=>$this->parse_values($match[2]));
			}
		}
	}

	protected function parse_table($table)
	{
		$matches = array();
		if(preg_match('/([a-z0-9_]+)\s*\(([^)]*)\)$/i', $table, $matches))
		{
			return array($matches[1], $matches[2]);
		}
	}

	protected function parse_values($text)
	{
		$retval = array();
		$items = explode('||', $text);
		foreach($items as $item)
		{
			list($name, $val) = explode('=>', $item);
			$retval[$name] = $val;
		}
		return $retval;
	}

	protected static function wrapticks($name)
	{
		return '`' . $name . '`';
	}
}

?>