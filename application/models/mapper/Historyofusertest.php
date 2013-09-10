<?php
require_once APPLICATION_PATH . '/models/DbTable/Historyofusertest.php';

class Default_Models_Mapper_Historyofusertest
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
			$this->setDbTable('Default_Models_DbTable_HistoryOfUserTest');
		}
		return $this->_dbTable;
	}

	public function save(Default_Models_Historyofusertest $model)
	{
		$data = array(
		
            'class_id'          	=> $model->getClass_id(),
			'shedule_exam_id'       => $model->getShedule_exam_id(),
			'user_id'          		=> $model->getUser_id(),
			'test_id'        		=> $model->getTest_id(),
            'time_start'   			=> $model->getTime_start(),
            'time_finished'       	=> $model->getTime_finished(),
			'list_question_id'   	=> $model->getList_question_id(),
			'list_answer_of_user'   => $model->getList_answer_of_user(),
			'list_score_of_question'=> $model->getList_score_of_question(),
			'list_score_table_test' => $model->getList_score_table_test(),
			'total_score'   		=> $model->getTotal_score(),
			'essay_list_question_id'   			=> $model->getEssay_list_question_id(),
			'essay_list_answer_of_user'   		=> $model->getEssay_list_answer_of_user(),
			'essay_list_score_of_question'   	=> $model->getEssay_list_score_of_question(),
			'essay_list_score_table_test'   	=> $model->getEssay_list_score_table_test(),
			'do_test_again'   	=> $model->getDo_test_again(),
			'stop_do_test_now'   	=> $model->getStop_do_test_now(),
			'first_load_done'   	=> $model->getFirst_load_done()
		
		);
		if (null === ($id = $model->getId())) {
			unset($data['id']);
			$this->getDbTable()->insert($data);
			$last_id =  $this->getDbTable()->getDefaultAdapter()->lastInsertId("quizuit_history_of_user_test","id");
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
			$entry = new Default_Models_Historyofusertest();
			
			$entry->setId($row->id);
			$entry->setClass_id($row->class_id);
			$entry->setShedule_exam_id($row->shedule_exam_id);
			$entry->setUser_id($row->user_id);
			$entry->setTest_id($row->test_id);
			$entry->setTime_start($row->time_start);
			$entry->setTime_finished($row->time_finished);
			$entry->setList_question_id($row->list_question_id);
			$entry->setList_answer_of_user($row->list_answer_of_user);
			$entry->setList_score_of_question($row->list_score_of_question);
			$entry->setList_score_table_test($row->list_score_table_test);
			$entry->setTotal_score($row->total_score);
			$entry->setEssay_list_question_id($row->essay_list_question_id);
			$entry->setEssay_list_answer_of_user($row->essay_list_answer_of_user);
			$entry->setEssay_list_score_of_question($row->essay_list_score_of_question);
			$entry->setEssay_list_score_table_test($row->essay_list_score_table_test);
			$entry->setDo_test_again($row->do_test_again);
			$entry->setStop_do_test_now($row->stop_do_test_now);
			$entry->setFirst_load_done($row->first_load_done);
			
			$entries[] = $entry;
		}
		return $entries;
	}

	public function find($key, $value, Default_Models_Historyofusertest $model) {
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
			$model->setShedule_exam_id($row->shedule_exam_id);
			$model->setUser_id($row->user_id);
			$model->setTest_id($row->test_id);
			$model->setTime_start($row->time_start);
			$model->setTime_finished($row->time_finished);
			$model->setList_question_id($row->list_question_id);
			$model->setList_answer_of_user($row->list_answer_of_user);
			$model->setList_score_of_question($row->list_score_of_question);
			$model->setList_score_table_test($row->list_score_table_test);
			$model->setTotal_score($row->total_score);
			$model->setEssay_list_question_id($row->essay_list_question_id);
			$model->setEssay_list_answer_of_user($row->essay_list_answer_of_user);
			$model->setEssay_list_score_of_question($row->essay_list_score_of_question);
			$model->setEssay_list_score_table_test($row->essay_list_score_table_test);
			$model->setDo_test_again($row->do_test_again);
			$model->setStop_do_test_now($row->stop_do_test_now);
			$model->setFirst_load_done($row->first_load_done);
			
		return 1;
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