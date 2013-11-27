<?php
namespace um;
require_once $_SERVER['DOCUMENT_ROOT'] . '/core/load.php';
Load::helper('debug');

/**
 * Description of DBWrapper
 *
 * @author Will Heyser
 */
class DBWrapper implements DBWrapperInterface
{
	/** @var \mysqli */
	private $db_handle;
	private $param_handler;

	public function __construct()
	{
		$this->db_handle = mysqli_connect('localhost', 'root', '', 'g2drivin_c2');
		$this->param_handler = new ParamHandler;
	}

	public function query($sql, $args=false)
	{
		/* @var $stmnt \mysqli_stmt */ $stmnt = $this->db_handle->stmt_init();
		if
		$stmnt->
	}

	public function bindParam($type, &$value)
	{
		$this->param_handler->addParam($type, $val);
	}
}

?>