<?php
namespace dvinci\table\output;
/**
 * Description of HTMLOutputEngine
 *
 * @author Will Heyser
 */
class HTMLOutputEngine extends OutputEngine
{
	protected $table_id = '';
	
	/**
	* Getter for table_id.
	* @return string 
	*/
	public function getTableId()
	{
		return $this->table_id;
	}
	
	/**
	* Setter for table_id.
	* @param string $new_table_id 
	*/
	public function setTableId($new_table_id)
	{
		$this->table_id = $new_table_id;
		return $this;
	}
	
	public function output()
	{
		ob_start();
?>
<table<?= $this->table_id ? (' class="' . $this->table_id . '"') : '' ?>>
<?php
		$this->showHeaders();
?>
	<tbody>
<?php 
		foreach($this->table->getDataRows() as /* @var $data_row \dvinci\table\DataRow */$row)
		{
?>
		<tr>
<?php
			$this->showRow($row);
?>
		</tr>
<?php
		}
?>
	</tbody>
</table>
<?php
		$output = ob_get_clean();
		return parent::output($output);
	}
	
	protected function showHeaders()
	{
		if($this->table->getDisplayHeaders() & \dvinci\table\DataTable::SHOW_COL_HEADERS)
		{
?>
	<thead>
		<tr>
<?php 
			if($this->table->getDisplayHeaders() & \dvinci\table\DataTable::SHOW_ROW_HEADERS)
			{
?>
			<th id="row_headers"></th>
<?php
			}
			foreach($this->table->getHeaders() as /* @var $header \dvinci\table\Header */ $header)
			{
				$this->showHeader($header, 'col');
			}
?>
		</tr>
	</thead>
<?php
		}
	}
	
	protected function showHeader($header, $scope='')
	{
?>
			<th scope="<?= $scope ?>" id="header_<?= $header->getId() ?>"><?= $header->getLabel()?></th>
<?php
	}
	
	protected function showRow(\dvinci\table\DataRow $row)
	{

		if($this->table->getDisplayHeaders() & \dvinci\table\DataTable::SHOW_ROW_HEADERS)
		{
			$this->showHeader($row->getHeader(), 'row');
		}
		if($this->table->getUseHeaders())
		{
			foreach($this->table->getHeaders() as /* @var $header \dvinci\table\Header */ $header)
			{
				$cell = $row->getDataCell($header);
				$this->showCell($cell);
			}
		}
		else
		{
			foreach($row as /* @var $cell \dvinci\table\DataCell */$cell)
			{
				$this->showCell($cell);
			}
		}
	}
	
	protected function showCell(\dvinci\table\DataCell $cell)
	{
?>
			<td><?=$cell->getData() ?></td>
<?php		
	}
}

?>