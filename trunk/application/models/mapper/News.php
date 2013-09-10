<?php

require_once APPLICATION_PATH . '/models/DbTable/News.php';

class Default_Models_Mapper_News 
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
			$this->setDbTable('Default_Models_DbTable_News');
		}
		return $this->_dbTable;
	}

	public function save(Default_Models_News   $model)
	{
		$data = array(
            'title'        				=> $model->getTitle(),
            'description'          		=> $model->getDescription(),
            'content'   				=> $model->getContent(),
			'path_image'   				=> $model->getPath_image(),
			'category_id'   			=> $model->getCategory_id(),
			'created'   				=> $model->getCreated(),
			'modified'   				=> $model->getModified(),
			'user_create'   			=> $model->getUser_create(),
			'publish'   				=> $model->getPublish(),
		);
		if (null === ($id = $model->getId())) {
			unset($data['id']);
			$this->getDbTable()->insert($data);
			$last_id =  $this->getDbTable()->getDefaultAdapter()->lastInsertId("quizuit_news","id");
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
			$entry = new Default_Models_News();

			$entry->setId($row->id);
			$entry->setTitle($row->title);
			$entry->setDescription($row->description);
			$entry->setContent($row->content);
			$entry->setPath_image($row->path_image);
			$entry->setCategory_id($row->category_id);
			$entry->setCreated($row->created);
			$entry->setModified($row->modified);
			$entry->setUser_create($row->user_create);
			$entry->setPublish($row->publish);
			
			$entries[] = $entry;
		}
		return $entries;
	}

	public function find($key, $value, Default_Models_News  $model) {
		$table = $this->getDbTable();
		$row = $table->fetchRow(
		$table->select()
		->where($key .' = ?', $value)
		);
		if (0 == count($row)) {
			return 0;
		}
		
			$model->setId($row->id);
			$model->setTitle($row->title);
			$model->setDescription($row->description);
			$model->setContent($row->content);
			$model->setPath_image($row->path_image);
			$model->setCategory_id($row->category_id);
			$model->setCreated($row->created);
			$model->setModified($row->modified);
			$model->setUser_create($row->user_create);
			$model->setPublish($row->publish);
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