<?php

require_once APPLICATION_PATH . '/models/DbTable/User.php';

class Default_Models_Mapper_User
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
			$this->setDbTable('Default_Models_DbTable_User');
		}
		return $this->_dbTable;
	}

	public function save(Default_Models_User   $model)
	{
		$data = array(
            'username'   				=> $model->getUsername(),
			'password'   				=> $model->getPassword(),
			'group_id'   				=> $model->getGroup_id(),
			'list_course_join'   		=> $model->getList_course_join(),
			'permission_custom'   		=> $model->getPermission_custom(),
			'user_code'   				=> $model->getUser_code(),
			'firstname'   				=> $model->getFirstname(),
			'lastname'   				=> $model->getLastname(),
			'date_create'   			=> $model->getDate_create(),
			'email'   					=> $model->getEmail(),
			'yahoo'   					=> $model->getYahoo(),
			'skyper'   					=> $model->getSkyper(),
			'phone'   					=> $model->getPhone(),
			'department'   				=> $model->getDepartment(),
			'address'   				=> $model->getAddress(),
			'city'   					=> $model->getCity(),
			'firstlogin'   				=> $model->getFirstlogin(),
			'lastlogin'   				=> $model->getLastlogin(),
			'isblock'   				=> $model->getIsblock(),
			
		);
		if (null === ($id = $model->getId())) {
			unset($data['id']);
			$this->getDbTable()->insert($data);
			$last_id =  $this->getDbTable()->getDefaultAdapter()->lastInsertId("quizuit_user","id");
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
			$entry = new Default_Models_User(); 

			$entry->setId($row->id);
			$entry->setUsername($row->username);
			$entry->setPassword($row->password);
			$entry->setGroup_id($row->group_id);
			$entry->setList_course_join($row->list_course_join);
			$entry->setPermission_custom($row->permission_custom);
			$entry->setUser_code($row->user_code);
			$entry->setFirstname($row->firstname);
			$entry->setLastname($row->lastname);
			$entry->setDate_create($row->date_create);
			$entry->setEmail($row->email);
			$entry->setYahoo($row->yahoo);
			$entry->setSkyper($row->skyper);
			$entry->setPhone($row->phone);
			$entry->setDepartment($row->department);
			$entry->setAddress($row->address);
			$entry->setCity($row->city);
			$entry->setFirstlogin($row->firstlogin);
			$entry->setLastlogin($row->lastlogin);
			$entry->setIsblock($row->isblock);
			
			$entries[] = $entry;
		}
		return $entries;
	}

	public function find($key, $value, Default_Models_User  $model) {
		$table = $this->getDbTable();
		$row = $table->fetchRow(
		$table->select()
		->where($key .' = ?', $value)
		);
		if (0 == count($row)) {
			return 0;
		}

			$model->setId($row->id);
			$model->setUsername($row->username);
			$model->setPassword($row->password);
			$model->setGroup_id($row->group_id);
			$model->setList_course_join($row->list_course_join);
			$model->setPermission_custom($row->permission_custom);
			$model->setUser_code($row->user_code);
			$model->setFirstname($row->firstname);
			$model->setLastname($row->lastname);
			$model->setDate_create($row->date_create);
			$model->setEmail($row->email);
			$model->setYahoo($row->yahoo);
			$model->setSkyper($row->skyper);
			$model->setPhone($row->phone);
			$model->setDepartment($row->department);
			$model->setAddress($row->address);
			$model->setCity($row->city);
			$model->setFirstlogin($row->firstlogin);
			$model->setLastlogin($row->lastlogin);
			$model->setIsblock($row->isblock);
			
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