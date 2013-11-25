<?php

require_once APPLICATION_PATH . '/models/DbTable/Test.php';

class Default_Models_Mapper_Test
{
	protected $_dbTable;

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
			$this->setDbTable('Default_Models_DbTable_Test');
		}
		return $this->_dbTable;
	}

	public function save(Default_Models_Test   $model)
	{
		$data = array(
			'subject_id'        					=> $model->getSubject_id(),
            'title'        					=> $model->getTitle(),
            'content'            			=> $model->getContent(),
			'duration_test'            		=> $model->getDuration_test(),
			'shuffle_question'            	=> $model->getShuffle_question(),
			'shuffle_answer'            	=> $model->getShuffle_answer(),
			'question_perpage'            	=> $model->getQuestion_perpage(),
			'attempts_allowed'            	=> $model->getAttempts_allowed(),
			'list_shedule_exam'             => $model->getList_shedule_exam(),
			'list_question'            		=> $model->getList_question(),
			'list_score'            		=> $model->getList_score(),
			'grade_method'            		=> $model->getGrade_method(),
			'decimal_digits_in_grades'      => $model->getDecimal_digits_in_grades(),
			'review_after_test'             => $model->getReview_after_test(),
            'review_while_test_open'   		=> $model->getReview_while_test_open(),
			'review_after_test_close'   	=> $model->getReview_after_test_close(),
			'date_create'            		=> $model->getDate_create(),
			'date_modify'            		=> $model->getDate_modify(),
			'user_create'            		=> $model->getUser_create(),
			'hidden' 		           		=> $model->getHidden(),
			'auto_update_level' 		           		=> $model->getAuto_update_level(),
		
		);
		if (null === ($id = $model->getId())) {
			unset($data['id']);
			$this->getDbTable()->insert($data);
			$last_id =  $this->getDbTable()->getDefaultAdapter()->lastInsertId("quizuit_test","id");
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
			$entry = new Default_Models_Test();

			$entry->setId($row->id);
			$entry->setSubject_id($row->subject_id);
			$entry->setTitle($row->title);
			$entry->setContent($row->content);
			$entry->setDuration_test($row->duration_test);
			$entry->setShuffle_question($row->shuffle_question);
			$entry->setShuffle_answer($row->shuffle_answer);
			$entry->setQuestion_perpage($row->question_perpage);
			$entry->setAttempts_allowed($row->attempts_allowed);
			$entry->setList_shedule_exam($row->list_shedule_exam);
			$entry->setList_question($row->list_question);
			$entry->setList_score($row->list_score);
			$entry->setGrade_method($row->grade_method);
			$entry->setDecimal_digits_in_grades($row->decimal_digits_in_grades);
			$entry->setReview_after_test($row->review_after_test);
			$entry->setReview_while_test_open($row->review_while_test_open);
			$entry->setReview_after_test_close($row->review_after_test_close);
			$entry->setDate_create($row->date_create);
			$entry->setDate_modify($row->date_modify);
			$entry->setUser_create($row->user_create);
			$entry->setHidden($row->hidden);
			$entry->setAuto_update_level($row->auto_update_level);
			
			$entries[] = $entry;
		}
		return $entries;
	}

	public function find($key, $value, Default_Models_Test  $model) {
		$table = $this->getDbTable();
		$row = $table->fetchRow(
		$table->select()
		->where($key .' = ?', $value)
		);
		if (0 == count($row)) {
			return 0;
		}
		
			$model->setId($row->id);
			$model->setSubject_id($row->subject_id);
			$model->setTitle($row->title);
			$model->setContent($row->content);
			$model->setDuration_test($row->duration_test);
			$model->setShuffle_question($row->shuffle_question);
			$model->setShuffle_answer($row->shuffle_answer);
			$model->setQuestion_perpage($row->question_perpage);
			$model->setAttempts_allowed($row->attempts_allowed);
			$model->setList_shedule_exam($row->list_shedule_exam);
			$model->setList_question($row->list_question);
			$model->setList_score($row->list_score);
			$model->setGrade_method($row->grade_method);
			$model->setDecimal_digits_in_grades($row->decimal_digits_in_grades);
			$model->setReview_after_test($row->review_after_test);
			$model->setReview_while_test_open($row->review_while_test_open);
			$model->setReview_after_test_close($row->review_after_test_close);
			$model->setDate_create($row->date_create);
			$model->setDate_modify($row->date_modify);
			$model->setUser_create($row->user_create);
			$model->setHidden($row->hidden);
			$model->setAuto_update_level($row->auto_update_level);
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