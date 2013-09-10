<?php

require_once APPLICATION_PATH . '/models/DbTable/Statisticadvisory.php';

class Default_Models_Mapper_StatisticAdvisory
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
			$this->setDbTable('Default_Models_DbTable_StatisticAdvisory');
		}
		return $this->_dbTable;
	}

	public function save(Default_Models_StatisticAdvisory   $model)
	{
		$data = array(
            'user_id'            	 		=> $model->getUser_id(),
            'subject_id'   					=> $model->getSubject_id(),
			'chapter_id'   					=> $model->getChapter_id(),
			'question_id'            	 		=> $model->getQuestion_id(),
			'amountTrue'            	 		=> $model->getAmountTrue(),
			'amountFalse'            	 		=> $model->getAmountFalse(),
			'true_false'            	 		=> $model->getTrue_false(),
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
			$entry = new Default_Models_StatisticAdvisory(); 

			$entry->setId($row->id);
			$entry->setUser_id($row->user_id);
			$entry->setSubject_id($row->subject_id);
			$entry->setChapter_id($row->chapter_id);
			$entry->setQuestion_id($row->question_id);
			$entry->setAmountTrue($row->amountTrue);
			$entry->setAmountFalse($row->amountFalse);
			$entry->setTrue_false($row->true_false);
			
			$entries[] = $entry;
		}
		return $entries;
	}

	public function find($key, $value, Default_Models_StatisticAdvisory  $model) {
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
			$model->setSubject_id($row->subject_id);
			$model->setChapter_id($row->chapter_id);
			$model->setQuestion_id($row->question_id);
			$model->setAmountTrue($row->amountTrue);
			$model->setAmountFalse($row->amountFalse);
			$model->setTrue_false($row->true_false);
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