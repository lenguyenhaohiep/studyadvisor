<?php

require_once APPLICATION_PATH . '/models/DbTable/Groupdetail.php';

class Default_Models_Mapper_GroupDetail
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
			$this->setDbTable('Default_Models_DbTable_GroupDetail');
		}
		return $this->_dbTable;
	}

	public function save(Default_Models_GroupDetail    $model)
	{
		$data = array(
            'group_id'        		=> $model->getGroup_id(),
            'controller'            => $model->getController(),
            'action'   				=> $model->getAction(),
			'value'   				=> $model->getValue(),
		'show_menu'   				=> $model->getShow_menu(),
		
		);
		if (null === ($id = $model->getId())) {
			unset($data['id']);
			$this->getDbTable()->insert($data);
			$last_id =  $this->getDbTable()->getDefaultAdapter()->lastInsertId("quizuit_group_detail","id");
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
			$entry = new Default_Models_GroupDetail();

			$entry->setId($row->id);
			$entry->setGroup_id($row->group_id);
			$entry->setController($row->controller);
			$entry->setAction($row->action);
			$entry->setValue($row->value);
			$entry->setShow_menu($row->show_menu);
			
			$entries[] = $entry;
		}
		return $entries;
	}

	public function find($key, $value, Default_Models_GroupDetail  $model) {
		$table = $this->getDbTable();
		$row = $table->fetchRow(
		$table->select()
		->where($key .' = ?', $value)
		);
		if (0 == count($row)) {
			return 0;
		}
		
			$model->setId($row->id);
			$model->setGroup_id($row->group_id);
			$model->setController($row->controller);
			$model->setAction($row->action);
			$model->setValue($row->value);		
			$model->setShow_menu($row->show_menu);
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