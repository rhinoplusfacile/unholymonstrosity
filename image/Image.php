<?php
namespace dvincisite;
/**
 * Description of PortfolioImage
 *
 * @author Will Heyser
 */
class Image extends \dvinci\entities\EntityBase
{
	private $filename;
	private $description;
	private $sort_order;
	private $created_at;
		
	/**
	 * Constructor
	 * @param int $id
	 */
	public function __construct($id=0)
	{
		parent::__construct($id);
		$this->table = 'image';
	}
	
	/**
	* Getter for filename.
	* @return string 
	*/
	public function getFilename()
	{
		$this->checkLoaded();
		return $this->filename;
	}
	
	/**
	* Setter for filename.
	* @param string $new_filename 
	*/
	public function setFilename($new_filename)
	{
		$this->checkLoaded();
		$this->setPropertyValue($this->filename, $new_filename);
		return $this;
	}
	
	/**
	* Getter for description.
	* @return string 
	*/
	public function getDescription()
	{
		$this->checkLoaded();
		return $this->description;
	}
	
	/**
	* Setter for description.
	* @param string $new_description 
	*/
	public function setDescription($new_description)
	{
		$this->checkLoaded();
		$this->setPropertyValue($this->description, $new_description);
		return $this;
	}
	
	/**
	* Getter for sort_order.
	* @return float 
	*/
	public function getSortOrder()
	{
		$this->checkLoaded();
		return $this->sort_order;
	}
	
	/**
	* Setter for sort_order.
	* @param float $new_sort_order 
	*/
	public function setSortOrder($new_sort_order)
	{
		$this->checkLoaded();
		$this->setPropertyValue($this->sort_order, $new_sort_order);
		return $this;
	}
	
	/**
	* Getter for created_at.
	* @return string 
	*/
	public function getCreatedAt()
	{
		$this->checkLoaded();
		return $this->created_at;
	}
	
	/**
	 * Load by ID from DB.
	 * @param int $id
	 * @return boolean load successful?
	 */
	public function load($id)
	{
		return $this->simpleSingleLoad($this->getTable(), $id);
	}
	
	/**
	 * Save to DB.
	 * @return boolean save successful?
	 */
	public function save()
	{
		$fields = array();
		$fields['filename'] = $this->filename;
		$fields['description'] = $this->description;
		$fields['sort_order'] = $this->sort_order;
		
		return $this->simpleSave($this->getTable(), $fields);
	}
	
	/**
	 * Validator
	 * @return boolean valid?
	 */
	public function validate()
	{
		$rules = array(
			array('field' => 'filename',
				'value' => $this->filename,
				'message' => 'Missing filename.',
				'rules' => 'set'),
			array('field' => 'description',
				'value' => $this->description,
				'message' => 'Missing description.',
				'rules' => 'set'),
			array('field' => 'sort_order',
				'value' => $this->sort_order,
				'message' => 'Missing sort_order.',
				'rules' => 'set')
		);
		
		return $this->validation_errors->validate($rules);
	}
	
	/**
	 * Populate from DB result.
	 * @param \stdClass $result DB result
	 */
	public function loadFromDbResult(\stdClass $result)
	{
		$this->filename = $result->filename;
		$this->description = $result->description;
		$this->sort_order = $result->sort_order;
		$this->created_at = $result->created_at;
		
		$this->loadCompleted($result->id);
	}
}
?>
