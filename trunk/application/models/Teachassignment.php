<?php
require_once 'mapper/Teachassignment.php';

class Default_Models_Teachassignment {
	protected $_id;
	protected $_course_id;
	protected $_user_id;
	protected $_class_id;
	protected $_subject_id;

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
	
	public function setCourse_id($value){
		$this->_course_id = $value;
	}
	public function getCourse_id(){
		return $this->_course_id;
	}

	public function setUser_id($value) {
		$this->_user_id = $value;
		return $this;
	}
	public function getUser_id() {
		return $this->_user_id;
	}

	public function setClass_id($value) {
		$this->_class_id = $value;
		return $this;
	}
	public function getClass_id() {
		return $this->_class_id;
	}

	public function setSubject_id($value) {
		$this->_subject_id = $value;
		return $this;
	}
	public function getSubject_id() {
		return $this->_subject_id;
	}
	
	
	public function setMapper($mapper) {
		$this->_mapper = $mapper;
		return $this;
	}

	public function getMapper() {
		if (null === $this->_mapper) {
			$this->_mapper = new  Default_Models_Mapper_Teachassignment();
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
		$data['id']					= $this->getId();
		$data['course_id'] 			= $this->getCourse_id();
		$data['user_id']	 		= $this->getUser_id();
		$data['class_id']			= $this->getClass_id();
		$data['subject_id']			= $this->getSubject_id();	

		return $data;
	}
	
}