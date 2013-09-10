<?php
require_once 'mapper/Groupdetail.php';

class Default_Models_GroupDetail {
	protected $_id;
	protected $_group_id;
	protected $_controller;
	protected $_action;
	protected $_value;
	protected $_show_menu;

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
	
	public function setGroup_id($value){
		$this->_group_id = $value;
	}
	public function getGroup_id(){
		return $this->_group_id;
	}

	public function setController($value) {
		$this->_controller = $value;
		return $this;
	}
	public function getController() {
		return $this->_controller;
	}

	public function setAction($value) {
		$this->_action = $value;
		return $this;
	}
	public function getAction() {
		return $this->_action;
	}

	public function setValue($value) {
		$this->_value = $value; 
		return $this;
	}
	public function getValue() {
		return $this->_value;
	}
	
	public function setShow_menu($value) {
		$this->_show_menu = $value; 
		return $this;
	}
	public function getShow_menu() {
		return $this->_show_menu;
	}
	
	
	
	public function setMapper($mapper) {
		$this->_mapper = $mapper;
		return $this;
	}

	public function getMapper() {
		if (null === $this->_mapper) {
			$this->_mapper = new  Default_Models_Mapper_GroupDetail();
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
		$data['group_id'] 			= $this->getGroup_id();
		$data['controller']	 		= $this->getController();
		$data['action']				= $this->getAction();
		$data['value']				= $this->getValue();
		$data['show_menu']				= $this->getShow_menu();

		return $data;
	}
	
}