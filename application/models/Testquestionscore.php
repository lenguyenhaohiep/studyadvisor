<?php
require_once 'mapper/Testquestionscore.php';

class Default_Models_Test_Question_Score {
	protected $_id;
	protected $_test_id;
	protected $_question_id;
	protected $_score;

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
	
	public function setTest_id($value){
		$this->_test_id = $value;
	}
	public function getTest_id(){
		return $this->_test_id;
	}

	public function setQuestion_id($value) {
		$this->_question_id = $value;
		return $this;
	}
	public function getQuestion_id() {
		return $this->_question_id;
	}

	public function setScore($value) {
		$this->_score = $value;
		return $this;
	}
	public function getScore() {
		return $this->_score;
	}

	public function setMapper($mapper) {
		$this->_mapper = $mapper;
		return $this;
	}

	public function getMapper() {
		if (null === $this->_mapper) {
			$this->_mapper = new  Default_Models_Mapper_Test_Question_Score();
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
		$data['test_id'] 			= $this->getTest_id();
		$data['question_id']	 	= $this->getQuestion_id();
		$data['score']				= $this->getScore();

		return $data;
	}
	
}