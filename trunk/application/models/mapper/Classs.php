<?php

require_once APPLICATION_PATH . '/models/DbTable/Classs.php';

class Default_Models_Mapper_Classs
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
			$this->setDbTable('Default_Models_DbTable_Classs');
		}
		return $this->_dbTable;
	}

	public function save(Default_Models_Classs   $model)
	{
		$data = array(
            'full_name'            	 	=> $model->getFull_name(),
            'short_name'   				=> $model->getShort_name(),
			'subject_id'   				=> $model->getSubject_id(),
			'course_id'   				=> $model->getCourse_id(),
			'time_start'   				=> $model->getTime_start(),
			'time_end'   				=> $model->getTime_end(),
			'time_start_register'   	=> $model->getTime_start_register(),
			'time_end_register'   		=> $model->getTime_end_register(),
			'max_user'   				=> $model->getMax_user(),
			'hidden'   					=> $model->getHidden(),
			'created_user'   			=> $model->getCreated_user(),
			'note'   					=> $model->getNote(),
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
		$table = $this->getDbTable(); 
		
		$resultSet = $table->fetchAll($where,$order,$count,$offset);			
		if (0 == count($resultSet)) {
			return;
		}
		$entries   = array();
		 
		foreach ($resultSet as $row) {
			$entry = new Default_Models_Classs(); 

			$entry->setId($row->id);
			$entry->setFull_name($row->full_name);
			$entry->setShort_name($row->short_name);
			$entry->setSubject_id($row->subject_id);
			$entry->setCourse_id($row->course_id);
			$entry->setTime_start($row->time_start);
			$entry->setTime_end($row->time_end);
			$entry->setTime_start_register($row->time_start_register);
			$entry->setTime_end_register($row->time_end_register);
			$entry->setMax_user($row->max_user);
			$entry->setHidden($row->hidden);
			$entry->setCreated_user($row->created_user);
			$entry->setNote($row->note);
			$entries[] = $entry;
		}
		return $entries;
	}

	public function find($key, $value, Default_Models_Classs  $model) {
		$table = $this->getDbTable();
		$row = $table->fetchRow(
		$table->select()
		->where($key .' = ?', $value)
		);
		if (0 == count($row)) {
			return 0;
		}
			$model->setId($row->id);
			$model->setFull_name($row->full_name);
			$model->setShort_name($row->short_name);
			$model->setSubject_id($row->subject_id);
			$model->setCourse_id($row->course_id);
			$model->setTime_start($row->time_start);
			$model->setTime_end($row->time_end);
			$model->setTime_start_register($row->time_start_register);
			$model->setTime_end_register($row->time_end_register);
			$model->setMax_user($row->max_user);
			$model->setHidden($row->hidden);
			$model->setCreated_user($row->created_user);
			$model->setNote($row->note);			
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