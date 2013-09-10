<?php

require_once APPLICATION_PATH . '/models/DbTable/Classhasstudent.php';

class Default_Models_Mapper_ClassHasStudent
{
	protected $_dbTable;
	public $_classhasstudent ;
	
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
			$this->setDbTable('Default_Models_DbTable_ClassHasStudent');
		}
		return $this->_dbTable;
	}

	public function save(Default_Models_ClassHasStudent   $model)
	{
		$data = array(
            'class_id'        			=> $model->getClass_id(),
            'user_id'           		=> $model->getUser_id(),
		);
		if (null === ($id = $model->getId())) {
			unset($data['id']);
			$this->getDbTable()->insert($data);
		} else {
			$this->getDbTable()->update($data, array('id = ?' => $id));
		}
	}

	public function fetchAll($where = null, $order = null, $count = null, $offset = null)
	{
		try{
		$table = $this->getDbTable(); 
		
		$resultSet = $table->fetchAll($where,$order,$count,$offset);	
		//var_dump(count($resultSet));		
		if (0 == count($resultSet)) {
			return;
		}
		$entries   = array();
		 
		foreach ($resultSet as $row) {
			$entry = new Default_Models_ClassHasStudent();
			
			$entry->setId($row->id);
			$entry->setClass_id($row->class_id);
			$entry->setUser_id($row->user_id);
			$entries[] = $entry;
		}
		return $entries;
		}catch(Exception $ex){
			echo $ex->getMessage();
			die();
		}
	}
	
	public function groupBySql($where = null, $order = null, $count = null, $offset = null)
	{
		try{
		$table = $this->getDbTable(); 

		$groupBy = " group `class_id`";
		$resultSet = $table->fetchAll($where, $order, $groupBy, $count,$offset);				
		if (0 == count($resultSet)) {
			return;
		}
		$entries   = array();
		 
		foreach ($resultSet as $row) {
			$entry = new Default_Models_ClassHasStudent();
			
			$entry->setId($row->id);
			$entry->setClass_id($row->class_id);
			$entry->setUser_id($row->user_id);
			$entries[] = $entry;
		}
		return $entries;
		}catch(Exception $ex){
			echo $ex->getMessage();
			die();
		}
}	

	public function find($key, $value, Default_Models_ClassHasStudent $model) {
		$table = $this->getDbTable();
		$row = $table->fetchRow(
		$table->select()
		->where($key .' = ?', $value)
		);
		if (0 == count($row)) {
			return 0;
		}
			$model->setId($row->id);
			$model->setClass_id($row->class_id);
			$model->setUser_id($row->user_id);			
	}

	public function customSql($sql){
		try{
			$db = $this->getDbTable()->getDefaultAdapter()->query($sql);
			$result	= $db->fetchAll();
			if (0 == count($result)) {
				return;
			}			
			return $result;	
			
		}catch (Exception $ex){
			echo $ex->getMessage();
			die();
		}
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