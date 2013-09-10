<?php
require_once 'mapper/Course.php';

class Default_Models_Course {
	protected $_id;
	protected $_full_name;
	protected $_short_name;
	protected $_time_start;
	protected $_time_end;
	protected $_summary;
	protected $_hidden;

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
	
	public function setSummary($value) {
		$this->_summary = $value;
		return $this;
	}
	public function getSummary() {
		return $this->_summary;
	}

	public function setHidden($value) {
		$this->_hidden = $value;
		return $this;
	}
	public function getHidden() {
		return $this->_hidden;
	}	
	
	
	public function setMapper($mapper) {
		$this->_mapper = $mapper;
		return $this;
	}

	public function getMapper() {
		if (null === $this->_mapper) {
			$this->_mapper = new  Default_Models_Mapper_Course();
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
		$data['time_start']				= $this->getTime_start() ;
		$data['time_end']				= $this->getTime_end() ;
		$data['summary']				= $this->getSummary();
		$data['hidden']					= $this->getHidden();

		return $data;
	}
	
}