<?php
require_once 'mapper/Classs.php';
require_once LIBRARY_PATH.'/FormatDate.php';

class Default_Models_Classs {
	protected $_id;
	protected $_full_name;
	protected $_short_name;
	protected $_subject_id;
	protected $_course_id ;
	protected $_time_start ;
	protected $_time_end ;
	protected $_time_start_register ;
	protected $_time_end_register ;
	protected $_max_user ;
	protected $_hidden ;
	protected $_created_user ;
	protected $_note ;

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

	public function setFull_name($value){
		$this->_full_name = $value;
	}
	public function getFull_name(){
		return $this->_full_name;
	}

	public function setShort_name($value) {
		$this->_short_name = $value;
		return $this;
	}
	public function getShort_name() {
		return $this->_short_name;
	}
	
	public function setSubject_id($value) {
		$this->_subject_id = $value;
		return $this;
	}
	public function getSubject_id() {
		return $this->_subject_id;
	}	
	
	public function setCourse_id($value){
		$this->_course_id = $value;
	}
	public function getCourse_id(){
		return $this->_course_id;
	}

	public function setTime_start($value) {
		$this->_time_start = $value;
		return $this;
	}
	public function getTime_start() {
		return $this->_time_start ;
	}

	public function setTime_end($value) {
		$this->_time_end = $value;
		return $this;
	}
	public function getTime_end() {
		return $this->_time_end;
	}

	public function setTime_start_register($value) {
		$this->_time_start_register = $value;
		return $this;
	}
	public function getTime_start_register() {
		return $this->_time_start_register;
	}

	public function setTime_end_register($value) {
		$this->_time_end_register = $value;
		return $this;
	}
	public function getTime_end_register() {
		return $this->_time_end_register;
	}
	
	public function setMax_user($value) {
		$this->_max_user= $value;
		return $this;
	}
	public function getMax_user() {
		return $this->_max_user;
	}
	
	
	public function setHidden($value) {
		$this->_hidden = $value;
		return $this;
	}
	public function getHidden() {
		return $this->_hidden;
	}
	
	public function setCreated_user($value) {
		$this->_created_user = $value;
		return $this;
	}
	public function getCreated_user() {
		return $this->_created_user;
	}
	
	public function setNote($value) {
		$this->_note = $value;
		return $this;
	}
	public function getNote() {
		return $this->_note;
	}
	
	public function setMapper($mapper) {
		$this->_mapper = $mapper;
		return $this;
	}

	public function getMapper() {
		if (null === $this->_mapper) {
			$this->_mapper = new  Default_Models_Mapper_Classs();
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
		$data['full_name'] 				= $this->getFull_name();
		$data['short_name']	 			= $this->getShort_name();
		$data['subject_id']	 			= $this->getSubject_id();
		$data['course_id']				= $this->getCourse_id() ;
		$data['time_start']				= $this->getTime_start() ;
		$data['time_end']				= $this->getTime_end() ;
		$data['time_start_register']	= $this->getTime_start_register() ;
		$data['time_end_register']		= $this->getTime_end_register();
		$data['max_user']				= $this->getMax_user() ;
		$data['hidden']					= $this->getHidden();
		$data['created_user']			= $this->getCreated_user();
		$data['note']					= $this->getNote() ;

		return $data;
	}
	
	public function toArrayHaveConvertDate(){
		$data = array();
		$data['id']						= $this->getId();
		$data['full_name'] 				= $this->getFull_name();
		$data['short_name']	 			= $this->getShort_name();
		$data['subject_id']	 			= $this->getSubject_id();
		$data['course_id']				= $this->getCourse_id() ;
		$data['time_start']				= Zend_View_Helper_FormatDate::convertYmdToMdyJustDate($this->getTime_start()) ;
		$data['time_end']				= Zend_View_Helper_FormatDate::convertYmdToMdyJustDate($this->getTime_end()) ;
		$data['time_start_register']	= Zend_View_Helper_FormatDate::convertYmdToMdyJustDate($this->getTime_start_register()) ;
		$data['time_end_register']		= Zend_View_Helper_FormatDate::convertYmdToMdyJustDate($this->getTime_end_register());
		$data['max_user']				= $this->getMax_user() ;
		$data['hidden']					= $this->getHidden();
		$data['created_user']			= $this->getCreated_user();
		$data['note']					= $this->getNote() ;

		return $data;			
	}
	
	
}