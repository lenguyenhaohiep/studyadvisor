<?php

require_once APPLICATION_PATH . '/models/DbTable/Datamining.php';

class Default_Models_Mapper_Datamining
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
			$this->setDbTable('Default_Models_DbTable_Datamining');
		}
		return $this->_dbTable;
	}

	public function save(Default_Models_Datamining   $model)
	{
		$data = array(
            'user_id'        				=> $model->getUser_id(),
            'chapter_id'          			=> $model->getChapter_id(),
            'listQuestionIDTrue'   			=> $model->getListQuestionIDTrue(),
			'listQuestionIDFalse'   		=> $model->getListQuestionIDFalse(),
		);
		if (null === ($id = $model->getId())) {
			unset($data['id']);
			$this->getDbTable()->insert($data);
			$last_id =  $this->getDbTable()->getDefaultAdapter()->lastInsertId("quizuit_datamining","id");
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
			$entry = new Default_Models_Datamining();

			$entry->setId($row->id);
			$entry->setUser_id($row->user_id);
			$entry->setChapter_id($row->chapter_id);
			$entry->setListQuestionIDTrue($row->listQuestionIDTrue);
			$entry->setListQuestionIDFalse($row->listQuestionIDFalse);
			$entries[] = $entry;
		}
		return $entries;
	}

	public function find($key, $value, Default_Models_Answer  $model) {
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
		$model->setChapter_id($row->chapter_id);
		$model->setListQuestionIDTrue($row->listQuestionIDTrue);
		$model->setListQuestionIDFalse($row->listQuestionIDFalse);

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