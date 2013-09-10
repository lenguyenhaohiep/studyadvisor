<?php

require_once APPLICATION_PATH . '/models/DbTable/Chaptersubject.php';

class Default_Models_Mapper_ChapterSubject
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
			$this->setDbTable('Default_Models_DbTable_ChapterSubject');
		}
		return $this->_dbTable;
	}

	public function save(Default_Models_ChapterSubject   $model)
	{
		$data = array(
            'name'        			=> $model->getName(),
            'subject_id'            => $model->getSubject_id(),
            'note'   				=> $model->getNote(),
                    'stt'=>$model->getStt(),
                    'path'=>$model->getPath()
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
			$entry = new Default_Models_ChapterSubject();

			$entry->setId($row->id);
			$entry->setName($row->name);
			$entry->setSubject_id($row->subject_id);
			$entry->setNote($row->note);
                        $entry->setStt($row->stt);
                        $entry->setPath($row->path);
			$entries[] = $entry;
		}
		return $entries;
	}
	
	public function find($key, $value, Default_Models_ChapterSubject  $model) {
		$table = $this->getDbTable();
		$row = $table->fetchRow(
		$table->select()
		->where($key .' = ?', $value)
		);
		if (0 == count($row)) {
			return 0;
		}
			/* chỗ này để xử lý trong select subject trả về 1 mảng các chapter
			$data = array();
			foreach ($row as $rowItem)
			{
				$index							= count($data);
				$data[$index]					= new Default_Models_ChapterSubject();
				$data[$index]->toArray($rowItem);
			}
			var_dump($data); die();
			return $data;
			*/
		
			$model->setId($row->id);
			$model->setName($row->name);
			$model->setSubject_id($row->subject_id);
			$model->setNote($row->note);
                        $model->setStt($row->stt);
                        $model->setPath($row->path);
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