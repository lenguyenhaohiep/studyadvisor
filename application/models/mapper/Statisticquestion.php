<?php

require_once APPLICATION_PATH . '/models/DbTable/Statisticquestion.php';

class Default_Models_Mapper_StatisticQuestion
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
			$this->setDbTable('Default_Models_DbTable_StatisticQuestion');
		}
		return $this->_dbTable;
	}

	public function save(Default_Models_StatisticQuestion $model)
	{
		$data = array(
			'question_id'            	 		=> $model->getQuestion_id(),
			'amountTrue'            	 		=> $model->getAmountTrue(),
			'total_done'            	 		=> $model->getTotal_done()
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
			$entry = new Default_Models_StatisticQuestion(); 

			$entry->setId($row->id);
			$entry->setQuestion_id($row->question_id);
			$entry->setAmountTrue($row->amountTrue);
			$entry->setTotal_done($row->total_done);
			
			$entries[] = $entry;
		}
		return $entries;
	}

	public function find($key, $value, Default_Models_StatisticQuestion  $model) {
		
		$table = $this->getDbTable();
		$row = $table->fetchRow(
		$table->select()
		->where($key .' = ?', $value)
		);
		
		if (0 == count($row)) {
			return 0;
		}
			$model->setId($row->id);
			$model->setQuestion_id($row->question_id);
			$model->setAmountTrue($row->amountTrue);
			$model->setTotal_done($row->total_done);
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