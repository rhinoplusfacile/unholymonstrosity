<?php
namespace PIMS\admin\cme_template;
/**
 * Description of CMETemplate
 *
 * @author Will Heyser
 */
class CMETemplate
{
	/**
	 *
	 * @param string $startdate
	 * @param string $enddate
	 * @param int $pim_id
	 * @param int $cme_id
	 * @param string $template
	 * @param \PIMS\admin\cme_template\Parser $parser
	 * @param mixed $db_handle
	 */
	public function __construct($startdate, $enddate, $pim_id, $cme_id, $template, Parser $parser, $db_handle)
	{
		$mysqlstartdate = date('Y-m-d', strtotime($startdate));
		$mysqlenddate = date('Y-m-d', strtotime($enddate));
		$pim_id = (int)$pim_id;
		$cme_id = (int)$cme_id;

		$parser->parse($template);

		$sql = "SELECT u.ID_PIM, u.cme_credit_at, u.ID_USER, p.name AS pim, u2.ID_ABP, u2.email, u2.firstname, u2.lastname
				FROM abp_user_PIM AS u
				LEFT JOIN abp_users AS u2 ON u2.ID_USER = u.ID_USER
				LEFT JOIN abp_PIMs AS p ON p.ID_PIM = u.ID_PIM";
		if($cme_id)
		{
			$sql .= " LEFT JOIN abp_PIM_CME AS pc on pc.ID_PIM = p.ID_PIM";
		}
		$sql .= " WHERE u.cme_credit_at != '0000-00-00'
				AND u2.ID_ABP > 0
				". ($startdate ? "AND u.cme_credit_at >= '$mysqlstartdate'" : '') ."
				". ($enddate ? "AND u.cme_credit_at <= '$mysqlenddate'" : '') ."
				". ($pim_id ? "AND u.ID_PIM = $pim_id" : '') ."
				". ($cme_id ? "AND pc.ID_CME = $cme_id" : '') ."
				ORDER BY u.cme_credit_at DESC, u2.lastname ASC, u2.firstname ASC";
		$result = $this->db->query($sql)->result();
		foreach($result as $row)
		{
			$d = date('m/d/Y', strtotime($row->cme_credit_at));
			$this->_items[$d][] = $row;
		}
	}
}

?>