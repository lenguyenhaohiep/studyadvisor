<?php
require_once 'mapper/Datamining.php';

class Default_Models_Datamining  {
	protected $_id;
	protected $_user_id;
	protected $_chapter_id;
	protected $_listQuestionIDTrue;
	protected $_listQuestionIDFalse;

	protected $_mapper;

	public function __construct(array $option = null) {
		if(is_array($option)) {
			$this->setOptions($option);
		}
	}

	public function __set($name, $value) {
		$method = 'set' . $name;
		if (('mapper' ==  $method) || !method_exists($this, $method)) {
			throw new Exception('Invalid property');
		}
		$this->$method($value);
	}

	public function __get($name) {
		$method = 'get' . $name;
		if (('mapper' == $method) || !method_exists($this, $method)) {
			throw new Exception('Invalid property');
		}
		return $this->$method();
	}
	public function setOptions(array $options) {
		$methods = get_class_methods($this);
		foreach($options as $key => $value) {
			$method = "set" . ucfirst($key);
			if (in_array($method, $methods)) {
				$this->$method($value);
			}
		}
		return $this;
	}

	/*-------------- GET SET ATRIBUTE OF TABLE ------------*/
	public function setId($value) {
		$this->_id = $value;
		return $this;
	}
	public function getId() {
		return $this->_id;
	}
	
	public function setUser_id($value){
		$this->_user_id = $value;
	}
	public function getUser_id(){
		return $this->_user_id;
	}

	public function setChapter_id($value) {
		$this->_chapter_id = $value;
		return $this;
	}
	public function getChapter_id() {
		return $this->_chapter_id;
	}

	public function setListQuestionIDTrue($value) {
		$this->_listQuestionIDTrue = $value;
		return $this;
	}
	public function getListQuestionIDTrue() {
		return $this->_listQuestionIDTrue;
	}

	public function setListQuestionIDFalse($value) {
		$this->_listQuestionIDFalse = $value;
		return $this;
	}
	public function getListQuestionIDFalse() {
		return $this->_listQuestionIDFalse;
	}

	
	public function setMapper($mapper) {
		$this->_mapper = $mapper;
		return $this;
	}

	public function getMapper() {
		if (null === $this->_mapper) {
			$this->_mapper = new  Default_Models_Mapper_Datamining();
		}
		return $this->_mapper;
	}

	public function save() {
		return $this->getMapper()->save($this);
	}

	public function find($key, $value) {
		$this->getMapper()->find($key, $value, $this);
		return $this;
	}

	public function delete($key, $value) {
		$this->getMapper()->delete($key, $value);
	}

	public function fetchAll($where = null, $order = null, $count = null, $offset = null) {
		return $this->getMapper()->fetchAll($where,$order,$count,$offset);
	}

	public function toArray() {
		$data = array();
		$data['id']							= $this->getId();
		$data['user_id'] 					= $this->getUser_id();
		$data['chapter_id']	 				= $this->getChapter_id();
		$data['listQuestionIDTrue']			= $this->getListQuestionIDTrue();
		$data['listQuestionIDFalse']	 	= $this->getListQuestionIDFalse();

		return $data;
	}
	
}