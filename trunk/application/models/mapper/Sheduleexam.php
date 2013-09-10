<?php

require_once APPLICATION_PATH . '/models/DbTable/Sheduleexam.php';

class Default_Models_Mapper_SheduleExam
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
			$this->setDbTable('Default_Models_DbTable_SheduleExam');
		}
		return $this->_dbTable;
	}

	public function save(Default_Models_SheduleExam   $model)
	{
		$data = array(
            'name'            	 		=> $model->getName(),
			'course_id'            	 	=> $model->getCourse_id(),
            'exam_id'   				=> $model->getExam_id(),
			'class_id'   				=> $model->getClass_id(),
			'time_start'   				=> $model->getTime_start(),
			'time_end'   				=> $model->getTime_end(),
			'list_test_id'   			=> $model->getList_test_id(),
			'created_user'   			=> $model->getCreated_user(),
			'hidden'   					=> $model->getHidden(),
			'note'   					=> $model->getNote(),
			'count_update_level'   		=> $model->getCount_update_level(),
		
		);		
		if (null === ($id = $model->getId())) {
			unset($data['id']);
			$this->getDbTable()->insert($data);
			$last_id =  $this->getDbTable()->getDefaultAdapter()->lastInsertId("quizuit_shedule_exam","id");
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
			$entry = new Default_Models_SheduleExam(); 

			$entry->setId($row->id);
			$entry->setName($row->name);
			$entry->setCourse_id($row->course_id);
			$entry->setExam_id($row->exam_id);
			$entry->setClass_id($row->class_id);
			$entry->setTime_start($row->time_start);
			$entry->setTime_end($row->time_end);
			$entry->setList_test_id($row->list_test_id);
			$entry->setCreated_user($row->created_user);
			$entry->setHidden($row->hidden);
			$entry->setNote($row->note);
			$entry->setCount_update_level($row->count_update_level);
			
			$entries[] = $entry;
		}
		return $entries;
	}

	public function find($key, $value, Default_Models_SheduleExam  $model) {
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
			$model->setCourse_id($row->course_id);
			$model->setExam_id($row->exam_id);
			$model->setClass_id($row->class_id);
			$model->setTime_start($row->time_start);
			$model->setTime_end($row->time_end);
			$model->setList_test_id($row->list_test_id);
			$model->setCreated_user($row->created_user);
			$model->setHidden($row->hidden);
			$model->setNote($row->note);
			$model->setCount_update_level($row->count_update_level);
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