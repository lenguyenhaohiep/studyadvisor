<?php

require_once APPLICATION_PATH . '/models/DbTable/Exam.php';

class Default_Models_Mapper_Exam
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
			$this->setDbTable('Default_Models_DbTable_Exam');
		}
		return $this->_dbTable;
	}

	public function save(Default_Models_Exam  $model)
	{
		$data = array(
			'course_id'            	 	=> $model->getCourse_id(),
            'full_name'            	 	=> $model->getFull_name(),
            'short_name'   				=> $model->getShort_name(),
			'summary'   				=> $model->getSummary(),
			'hidden'   					=> $model->getHidden(),
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
			$entry = new Default_Models_Exam(); 

			$entry->setId($row->id);
			$entry->setCourse_id($row->course_id);
			$entry->setFull_name($row->full_name);
			$entry->setShort_name($row->short_name);
			$entry->setSummary($row->summary);
			$entry->setHidden($row->hidden);
			$entry->setNote($row->note);
			$entries[] = $entry;
		}
		return $entries;
	}

	public function find($key, $value, Default_Models_Exam $model) {
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
			$model->setFull_name($row->full_name);
			$model->setShort_name($row->short_name);
			$model->setSummary($row->summary);
			$model->setHidden($row->hidden);
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