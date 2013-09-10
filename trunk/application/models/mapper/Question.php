<?php
require_once APPLICATION_PATH . '/models/DbTable/Question.php';
require_once("Zend/Date.php");

class Default_Models_Mapper_Question
{
	protected $_dbTable;
	public  function formatDate($date)
	{
		$dateTime = new DateTime($date); 
		return $dateTime->format("m-d-Y H:i:s");		
	}
	public  function formatDate_long($date){
		if (!empty($date)){
			$dateTime = new DateTime($date); 
			return $dateTime->format("F-d-Y H:i:s");
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
			$this->setDbTable('Default_Models_DbTable_Question');
		}
		return $this->_dbTable;
	}

	public function save(Default_Models_Question $model)
	{
		$data = array(
			'type_question'          => $model->getType_question(),
            'questiontext'          => $model->getQuestiontext(),
			'content'        		=> $model->getContent(),
            'subject_id'   			=> $model->getSubject_id(),
			'level'   				=> $model->getLevel(),
			'classification'   		=> $model->getClassification(),
            'generalfeedback'       => $model->getGeneralfeedback(),
			'question_title'   		=> $model->getQuestion_title(),
			'chapter_id'   			=> $model->getChapter_id(),
			'list_test_id'   		=> $model->getList_test_id(),
			'created_user'   		=> $model->getCreated_user(),
			'timecreated'   		=> $model->getTimecreated(),
			'timemodified'   		=> $model->getTimemodified(),
			'modifiedby'   			=> $model->getModifiedby(),
			'hidden'   				=> $model->getHidden()
		);
		
		if (null === ($id = $model->getId())) {
			unset($data['id']);
			$this->getDbTable()->insert($data);
			$last_id =  $this->getDbTable()->getDefaultAdapter()->lastInsertId("quizuit_questions","id");
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
			$entry = new Default_Models_Question();

			$entry->setId($row->id);
			$entry->setType_question($row->type_question);
			$entry->setContent($row->content);
			$entry->setQuestiontext($row->questiontext);
			$entry->setSubject_id($row->subject_id);
			$entry->setLevel($row->level);
			$entry->setClassification($row->classification);
			$entry->setGeneralfeedback($row->generalfeedback);
			$entry->setQuestion_title($row->question_title);
			$entry->setChapter_id($row->chapter_id);
			$entry->setList_test_id($row->list_test_id);
			$entry->setCreated_user($row->created_user);
			$entry->setTimecreated($row->timecreated);
			$entry->setTimemodified($row->timemodified);
			$entry->setModifiedby($row->modifiedby);
			$entry->setHidden($row->hidden);
			$entries[] = $entry;
		}
		return $entries;
	}

	public function find($key, $value, Default_Models_Question $model) {
		$table = $this->getDbTable();
		$row = $table->fetchRow(
		$table->select()
		->where($key .' = ?', $value)
		);
		if (0 == count($row)) {
			return 0;
		}
			$model->setId($row->id);
			$model->setType_question($row->type_question);
			$model->setContent($row->content);
			$model->setQuestiontext($row->questiontext);
			$model->setSubject_id($row->subject_id);
			$model->setLevel($row->level);
			$model->setClassification($row->classification);
			$model->setGeneralfeedback($row->generalfeedback);
			$model->setQuestion_title($row->question_title);
			$model->setChapter_id($row->chapter_id);
			$model->setList_test_id($row->list_test_id);
			$model->setCreated_user($row->created_user);
			$model->setTimecreated($row->timecreated);
			$model->setTimemodified($row->timemodified);
			$model->setModifiedby($row->modifiedby);
			$model->setHidden($row->hidden);
		
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

	public function fetchAllColumn($column1,$column2=null,$column3=null){
		try{
			$sql = "select `".$column1."` from quizuit_questions where `hidden`='off' ";
			$db = $this->getDbTable()->getDefaultAdapter()->query($sql);
			$question	= $db->fetchAll();
			if (0 == count($question)) {
				return;
			}			
			$data = array();
			foreach ($question as $row) {
				$data[] = $row['id'];
			}
			return $data;	
		}catch (Exception $ex){
			echo $ex->getMessage();
			die();
		}
	}
	
	public function fetchAllColumnHasWhere($column1,$where){
		try{
			$sql = "select `".$column1."` from quizuit_questions where `hidden`='off' AND ".$where;
			$db = $this->getDbTable()->getDefaultAdapter()->query($sql);
			$question	= $db->fetchAll();
			if (0 == count($question)) {
				return;
			}			
			$data = array();
			foreach ($question as $row) {
				$data[] = $row['id'];
			}
			return $data;	
		}catch (Exception $ex){
			echo $ex->getMessage();
			die();
		}
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
}