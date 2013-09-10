<?php

require_once APPLICATION_PATH . '/models/DbTable/Teachassignment.php';

class Default_Models_Mapper_Teachassignment
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
			$this->setDbTable('Default_Models_DbTable_Teachassignment');
		}
		return $this->_dbTable;
	}

	public function save(Default_Models_Teachassignment   $model)
	{
		$data = array(
            'course_id'        		=> $model->getCourse_id(),
            'user_id'            	=> $model->getUser_id(),
            'class_id'   			=> $model->getClass_id(),
			'subject_id'   			=> $model->getSubject_id(),
		);
		if (null === ($id = $model->getId())) {
			unset($data['id']);
			$this->getDbTable()->insert($data);
			$last_id =  $this->getDbTable()->getDefaultAdapter()->lastInsertId("quizuit_teach_assignment","id");
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
			$entry = new Default_Models_Teachassignment();

			$entry->setId($row->id);
			$entry->setCourse_id($row->course_id);
			$entry->setUser_id($row->user_id);
			$entry->setClass_id($row->class_id);
			$entry->setSubject_id($row->subject_id);
			$entries[] = $entry;
		}
		return $entries;
	}

	public function find($key, $value, Default_Models_Teachassignment  $model) {
		$table = $this->getDbTable();
		$row = $table->fetchRow(
		$table->select()
		->where($key .' = ?', $value)
		);
		if (0 == count($row)) {
			return 0;
		}
		
		$model->setId($row->id);
		$model->setCourse_id($row->course_id);
		$model->setUser_id($row->user_id);
		$model->setClass_id($row->class_id);
		$model->setSubject_id($row->subject_id);
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