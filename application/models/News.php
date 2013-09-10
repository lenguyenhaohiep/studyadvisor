<?php
require_once 'mapper/News.php';
require_once LIBRARY_PATH.'/FormatDate.php';

class Default_Models_News {
	protected $_id;
	protected $_title;
	protected $_description;
	protected $_content;
	protected $_path_image;
	protected $_category_id;
	protected $_created;
	protected $_modified;
	protected $_user_create;
	protected $_publish;

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

	public function setTitle($value){
		$this->_title = $value;
	}
	public function getTitle(){
		return $this->_title;
	}

	public function setDescription($value) {
		$this->_description = $value;
		return $this;
	}
	public function getDescription() {
		return $this->_description;
	}
	
	public function setContent($value) {
		$this->_content = $value;
		return $this;
	}
	public function getContent() {
		return $this->_content;
	}

	public function setPath_image($value) {
		$this->_path_image = $value;
		return $this;
	}
	public function getPath_image() {
		return $this->_path_image;
	}

	public function setCategory_id($value) {
		$this->_category_id = $value;
		return $this;
	}
	public function getCategory_id() {
		return $this->_category_id;
	}
	
	public function setCreated($value) {
		$this->_created = $value;
		return $this;
	}
	public function getCreated() {
		return $this->_created;
	}

	public function setModified($value) {
		$this->_modified = $value;
		return $this;
	}
	public function getModified() {
		return $this->_modified;
	}

	public function setUser_create($value) {
		$this->_user_create = $value;
		return $this;
	}
	public function getUser_create() {
		return $this->_user_create;
	}
	
	public function setPublish($value) {
		$this->_publish = $value;
		return $this;
	}
	public function getPublish() {
		return $this->_publish;
	}
	
	public function setMapper($mapper) {
		$this->_mapper = $mapper;
		return $this;
	}

	public function getMapper() {
		if (null === $this->_mapper) {
			$this->_mapper = new  Default_Models_Mapper_News();
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
		$data['title'] 						= $this->getTitle();
		$data['description']	 			= $this->getDescription();
		$data['content']	 				= $this->getContent();
		$data['path_image']					= $this->getPath_image();
		$data['category_id']				= $this->getCategory_id();
		$data['created']					= $this->getCreated();
		$data['modified']					= $this->getModified();
		$data['user_create']				= $this->getUser_create();
		$data['publish']					= $this->getPublish();

		return $data;
	}
	
}