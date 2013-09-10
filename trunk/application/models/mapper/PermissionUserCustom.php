<?php

require_once APPLICATION_PATH . '/models/DbTable/PermissionUserCustom.php';

class Default_Models_Mapper_PermissionUserCustom
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
			$this->setDbTable('Default_Models_DbTable_PermissionUserCustom');
		}
		return $this->_dbTable;
	}

	public function save(Default_Models_PermissionUserCustom   $model)
	{
		$data = array(
            'user_id'            	 	=> $model->getUser_id(),
            'question_per'   			=> $model->getQuestion_per(),
			'test_per'   				=> $model->getTest_per(),
			'user_per'   				=> $model->getUser_per(),
			'course_per'   				=> $model->getCourse_per(),
			'class_per'   				=> $model->getClass_per(),
			'subject_per'   			=> $model->getSubject_per(),
			'shedule_exam_per'   		=> $model->getShedule_exam_per(),
			'exam_per'   				=> $model->getExam_per(),
			'report_per'   				=> $model->getReport_per(),
			'list_subject_per'   		=> $model->getList_subject_per(),
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
			$entry = new Default_Models_PermissionUserCustom(); 

			$entry->setId($row->id);
			$entry->setUser_id($row->user_id);
			$entry->setQuestion_per($row->question_per);
			$entry->setTest_per($row->test_per);
			$entry->setUser_per($row->user_per);
			$entry->setCourse_per($row->course_per);
			$entry->setClass_per($row->class_per);
			$entry->setSubject_per($row->subject_per);
			$entry->setShedule_exam_per($row->shedule_exam_per);
			$entry->setExam_per($row->exam_per);
			$entry->setReport_per($row->report_per);
			$entry->setList_subject_per($row->list_subject_per);
			$entries[] = $entry;
		}
		return $entries;
	}

	public function find($key, $value, Default_Models_PermissionUserCustom  $model) {
		$table = $this->getDbTable();
		$row = $table->fetchRow(
		$table->select()
		->where($key .' = ?', $value)
		);
		if (0 == count($row)) {
			return 0;
		}
			$model->setId($row->id);
			$model->setUser_id($row->user_id);
			$model->setQuestion_per($row->question_per);
			$model->setTest_per($row->test_per);
			$model->setUser_per($row->user_per);
			$model->setCourse_per($row->course_per);
			$model->setClass_per($row->class_per);
			$model->setSubject_per($row->subject_per);
			$model->setShedule_exam_per($row->shedule_exam_per);
			$model->setExam_per($row->exam_per);
			$model->setReport_per($row->report_per);
			$model->setList_subject_per($row->list_subject_per);
			
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