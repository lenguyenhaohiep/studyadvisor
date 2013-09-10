<?php

require_once APPLICATION_PATH . '/models/DbTable/Category.php';

class Default_Models_Mapper_Category 
{
	protected $_dbTable;

	public function setDbTable($dbTable)
	{
		if (is_string($dbTable)) {
			$dbTable = new $dbTable();
		}
		if (!$dbTable instanceof Zend_Db_Table_Abstract) {
			throw new Exception('Invalid table data gateway provided');
		}
		$this->_dbTable = $dbTable;
		return $this;
	}

	public function getDbTable()
	{
		if (null === $this->_dbTable) {
			$this->setDbTable('Default_Models_DbTable_Category');
		}
		return $this->_dbTable;
	}

	public function save(Default_Models_Category   $model)
	{
		$data = array(
            'name'        			=> $model->getName(),
            'parent_id'          	=> $model->getParent_id(),
            'order'   				=> $model->getOrder(),
			'is_main_page'   		=> $model->getIs_main_page(),
			'is_focus'   			=> $model->getIs_focus(),
			'hidden'   				=> $model->getHidden(),
		);
		if (null === ($id = $model->getId())) {
			unset($data['id']);
			$this->getDbTable()->insert($data);
			$last_id =  $this->getDbTable()->getDefaultAdapter()->lastInsertId("quizuit_category","id");
			return $last_id;
		} else {
			$this->getDbTable()->update($data, array('id = ?' => $id));
		}
	}

	public function fetchAll($where = null, $order = null, $count = null, $offset = null)
	{
		$table = $this->getDbTable(); 
		
		$resultSet = $table->fetchAll($where,$order,$count,$offset);			
		if (0 == count($resultSet)) {
			return;
		}
		$entries   = array();
		 
		foreach ($resultSet as $row) {
			$entry = new Default_Models_Category();

			$entry->setId($row->id);
			$entry->setName($row->name);
			$entry->setParent_id($row->parent_id);
			$entry->setOrder($row->order);
			$entry->setIs_main_page($row->is_main_page);
			$entry->setIs_focus($row->is_focus);
			$entry->setHidden($row->hidden);
			$entries[] = $entry;
		}
		return $entries;
	}

	public function find($key, $value, Default_Models_Category  $model) {
		$table = $this->getDbTable();
		$row = $table->fetchRow(
		$table->select()
		->where($key .' = ?', $value)
		);
		if (0 == count($row)) {
			return 0;
		}
		
		$model->setId($row->id);
		$model->setName($row->name);
		$model->setParent_id($row->parent_id);
		$model->setOrder($row->order);
		$model->setIs_main_page($row->is_main_page);
		$model->setIs_focus($row->is_focus);
		$model->setHidden($row->hidden);
		}

	public function delete($key, $value) {
		if (is_array($value)) {
			$in = implode(',', $value);
			$where = $this->getDbTable()->getAdapter()->quoteInto($key . ' in (?)', $in);
		}
		else {
			$where = $this->getDbTable()->getAdapter()->quoteInto($key . '= ?', $value);
		}
		$this->getDbTable()->delete($where);
	}
}