<?php
require_once 'mapper/Statisticadvisory.php';

class Default_Models_StatisticAdvisory {
	protected $_id;
	protected $_user_id;
	protected $_subject_id;
	protected $_chapter_id;
	protected $_question_id;
	protected $_amountTrue;
	protected $_amountFalse;
	protected $_true_false;

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

	public function setSubject_id($value) {
		$this->_subject_id = $value;
		return $this;
	}
	public function getSubject_id() {
		return $this->_subject_id;
	}

	public function setChapter_id($value) {
		$this->_chapter_id = $value;
		return $this;
	}
	public function getChapter_id() {
		return $this->_chapter_id;
	}

	public function setQuestion_id($value) {
		$this->_question_id = $value;
		return $this;
	}
	public function getQuestion_id() {
		return $this->_question_id;
	}
	
	public function setAmountTrue($value) {
		$this->_amountTrue = $value;
		return $this;
	}
	public function getAmountTrue() {
		return $this->_amountTrue;
	}

	public function setAmountFalse($value) {
		$this->_amountFalse = $value;
		return $this;
	}
	public function getAmountFalse() {
		return $this->_amountFalse;
	}
	
	public function setTrue_false($value) {
		$this->_true_false = $value;
		return $this;
	}
	public function getTrue_false() {
		return $this->_true_false;
	}
	
	
	public function setMapper($mapper) {
		$this->_mapper = $mapper;
		return $this;
	}

	public function getMapper() {
		if (null === $this->_mapper) {
			$this->_mapper = new  Default_Models_Mapper_StatisticAdvisory();
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
		$data['user_id'] 				= $this->getUser_id();
		$data['subject_id']	 			= $this->getSubject_id();
		$data['chapter_id']				= $this->getChapter_id() ;
		$data['question_id']			= $this->getQuestion_id();
		$data['amountTrue']				= $this->getAmountTrue();
		$data['amountFalse']			= $this->getAmountFalse();
		$data['true_false']				= $this->getTrue_false();
				
		
		return $data;
	}
	
}