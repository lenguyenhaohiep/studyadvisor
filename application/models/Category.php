<?php
require_once 'mapper/Category.php';
require_once LIBRARY_PATH.'/FormatDate.php';

class Default_Models_Category {
	protected $_id;
	protected $_name;
	protected $_parent_id;
	protected $_order;
	protected $_is_main_page;
	protected $_is_focus;
	protected $_hidden ;

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

	public function setParent_id($value) {
		$this->_parent_id = $value;
		return $this;
	}
	public function getParent_id() {
		return $this->_parent_id;
	}
	
	public function setOrder($value) {
		$this->_order = $value;
		return $this;
	}
	public function getOrder() {
		return $this->_order;
	}

	public function setIs_main_page($value) {
		$this->_is_main_page = $value;
		return $this;
	}
	public function getIs_main_page() {
		return $this->_is_main_page;
	}

	public function setIs_focus($value) {
		$this->_is_focus = $value;
		return $this;
	}
	public function getIs_focus() {
		return $this->_is_focus;
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
			$this->_mapper = new  Default_Models_Mapper_Category();
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
		$data['parent_id']	 			= $this->getParent_id();
		$data['order']	 				= $this->getOrder();
		$data['is_main_page']			= $this->getIs_main_page();
		$data['is_focus']				= $this->getIs_main_page();
		$data['hidden']					= $this->getHidden();

		return $data;
	}
	
}