<?php
require_once 'mapper/Sheduleexam.php';

class Default_Models_SheduleExam {
	protected $_id;
	protected $_name;
	protected $_course_id;
	protected $_exam_id;
	protected $_class_id;
	protected $_time_start;
	protected $_time_end;
	protected $_list_test_id;
	protected $_created_user;
	protected $_hidden;
	protected $_note;
	protected $_count_update_level;

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
	
	public function setName($value){
		$this->_name = $value;
	}
	public function getName(){
		return $this->_name;
	}

	public function setCourse_id($value){
		$this->_course_id = $value;
	}
	public function getCourse_id(){
		return $this->_course_id;
	}
	
	public function setExam_id($value) {
		$this->_exam_id = $value;
		return $this;
	}
	public function getExam_id() {
		return $this->_exam_id;
	}

	public function setClass_id($value) {
		$this->_class_id = $value;
		return $this;
	}
	public function getClass_id() {
		return $this->_class_id;
	}

	public function setTime_start($value) {
		$this->_time_start = $value;
		return $this;
	}
	public function getTime_start() {
		return $this->_time_start;
	}

	public function setTime_end($value) {
		$this->_time_end = $value;
		return $this;
	}
	public function getTime_end() {
		return $this->_time_end;
	}
	
	public function setList_test_id($value) {
		$this->_list_test_id = $value;
		return $this;
	}
	public function getList_test_id() {
		return $this->_list_test_id;
	}

	public function setCreated_user($value) {
		$this->_created_user = $value;
		return $this;
	}
	public function getCreated_user() {
		return $this->_created_user;
	}	
	
	public function setHidden($value) {
		$this->_hidden = $value;
		return $this;
	}
	public function getHidden() {
		return $this->_hidden;
	}	
	
	public function setNote($value) {
		$this->_note = $value;
		return $this;
	}
	public function getNote() {
		return $this->_note;
	}
	
	public function setCount_update_level($value) {
		$this->_count_update_level = $value;
		return $this;
	}
	public function getCount_update_level() {
		return $this->_count_update_level;
	}
	
	
	public function setMapper($mapper) {
		$this->_mapper = $mapper;
		return $this;
	}

	public function getMapper() {
		if (null === $this->_mapper) {
			$this->_mapper = new  Default_Models_Mapper_SheduleExam();
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
		$data['id']						= $this->getId();
		$data['name'] 					= $this->getName();
		$data['course_id'] 				= $this->getCourse_id();
		$data['exam_id']	 			= $this->getExam_id();
		$data['class_id']	 			= $this->getClass_id();
		$data['time_start']				= $this->getTime_start() ;
		$data['time_end']				= $this->getTime_end() ;
		$data['list_test_id']			= $this->getList_test_id();
		$data['created_user']			= $this->getCreated_user();
		$data['hidden']					= $this->getHidden();
		$data['note']					= $this->getNote();
		$data['count_update_level']		= $this->getCount_update_level();

		return $data;
	}
	
}