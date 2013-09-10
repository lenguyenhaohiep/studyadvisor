<?php
require_once 'mapper/PermissionUserCustom.php';

class Default_Models_PermissionUserCustom {
	protected $_id;
	protected $_user_id;
	protected $_question_per;
	protected $_test_per;
	protected $_user_per;
	protected $_course_per;
	protected $_class_per;
	protected $_subject_per;
	protected $_shedule_exam_per;
	protected $_exam_per;
	protected $_report_per;
	protected $_list_subject_per;

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

	public function setQuestion_per($value) {
		$this->_question_per = $value;
		return $this;
	}
	public function getQuestion_per() {
		return $this->_question_per;
	}

	public function setTest_per($value) {
		$this->_test_per = $value;
		return $this;
	}
	public function getTest_per() {
		return $this->_test_per;
	}

	public function setUser_per($value) {
		$this->_user_per = $value;
		return $this;
	}
	public function getUser_per() {
		return $this->_user_per;
	}
	
	public function setCourse_per($value) {
		$this->_course_per = $value;
		return $this;
	}
	public function getCourse_per() {
		return $this->_course_per;
	}

	public function setClass_per($value) {
		$this->_class_per= $value;
		return $this;
	}
	public function getClass_per() {
		return $this->_class_per;
	}	
	
	public function setSubject_per($value) {
		$this->_subject_per= $value;
		return $this;
	}
	public function getSubject_per() {
		return $this->_subject_per;
	}	
	
	public function setShedule_exam_per($value) {
		$this->_shedule_exam_per= $value;
		return $this;
	}
	public function getShedule_exam_per() {
		return $this->_shedule_exam_per;
	}	
	
	public function setExam_per($value) {
		$this->_exam_per= $value;
		return $this;
	}
	public function getExam_per() {
		return $this->_exam_per;
	}	
	
	public function setReport_per($value) {
		$this->_report_per= $value;
		return $this;
	}
	public function getReport_per() {
		return $this->_report_per;
	}	
	
	public function setList_subject_per($value) {
		$this->_list_subject_per= $value;
		return $this;
	}
	public function getList_subject_per() {
		return $this->_list_subject_per;
	}	
	
	
	public function setMapper($mapper) {
		$this->_mapper = $mapper;
		return $this;
	}

	public function getMapper() {
		if (null === $this->_mapper) {
			$this->_mapper = new  Default_Models_Mapper_PermissionUserCustom();
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
		$data['question_per']	 		= $this->getQuestion_per();
		$data['test_per']				= $this->getTest_per() ;
		$data['user_per']				= $this->getUser_per() ;
		$data['course_per']				= $this->getCourse_per();
		$data['class_per']				= $this->getClass_per();
		$data['subject_per']			= $this->getSubject_per();
		$data['shedule_exam_per']		= $this->getShedule_exam_per();
		$data['exam_per']				= $this->getExam_per();
		$data['report_per']				= $this->getReport_per();
		$data['list_subject_per']		= $this->getList_subject_per();

		return $data;
	}
	
}