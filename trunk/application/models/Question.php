<?php
// application/models/Question.php

require_once 'mapper/Question.php';

class Default_Models_Question {
	protected $_id;
	protected $_type_question;
	protected $_content;
	protected $_questiontext;
	protected $_subject_id;
	protected $_chapter_id;
	protected $_level;
	protected $_classification;
	protected $_generalfeedback;
	protected $_question_title;
	protected $_list_test_id;
	protected $_created_user;
	protected $_timecreated;
	protected $_timemodified;
	protected $_modifiedby;
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

	public function setType_question($value) {
		$this->_type_question = $value;
		return $this;
	}
	public function getType_question() {
		return $this->_type_question;
	}
	
	public function setContent($value){
		$this->_content = $value;
	}
	public function getContent(){
		return $this->_content;
	}
	
	public function setQuestiontext($value) {
		$this->_questiontext = $value;
		return $this;
	}
	public function getQuestiontext() {
		return $this->_questiontext;
	}

	public function setSubject_id($value) {
		$this->_subject_id = $value;
		return $this;
	}
	public function getSubject_id() {
		return $this->_subject_id;
	}
	
	public function setLevel($value) {
		$this->_level = $value;
		return $this;
	}
	public function getLevel() {
		return $this->_level;
	}
	
	public function setClassification($value) {
		$this->_classification = $value;
		return $this;
	}
	public function getClassification() {
		return $this->_classification;
	}
	
	public function setGeneralfeedback($value) {
		$this->_generalfeedback = $value;
		return $this;
	}
	public function getGeneralfeedback() {
		return $this->_generalfeedback;
	}

    public function setQuestion_title($value) {
		$this->_question_title = $value;
		return $this;
	}
	public function getQuestion_title() {
		return $this->_question_title;
	}
	
    public function setChapter_id($value) {
		$this->_chapter_id = $value;
		return $this;
	}
	public function getChapter_id() {
		return $this->_chapter_id;
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
	
    public function setTimecreated($value) {
		$this->_timecreated = $value;
		return $this;
	}
	public function getTimecreated() {
		return $this->_timecreated;
	}

    public function setTimemodified($value) {
		$this->_timemodified = $value;
		return $this;
	}
	public function getTimemodified() {
		return $this->_timemodified;
	}

    public function setModifiedby($value) {
		$this->_modifiedby = $value;
		return $this;
	}
	public function getModifiedby() {
		return $this->_modifiedby;
	}
	
    public function setHidden($value) {
		$this->_hidden = $value;
		return $this;
	}
	public function getHidden() {
		return $this->_hidden;
	}
	
	/*-------------- END GET SET ATRIBUTE OF TABLE ------------*/
	
	public function setMapper($mapper) {
		$this->_mapper = $mapper;
		return $this;
	}

	public function getMapper() {
		if (null === $this->_mapper) {
			$this->_mapper = new  Default_Models_Mapper_Question();
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

	public function fetchAllColumn($column1,$column2=null,$column3=null){
		return $this->getMapper()->fetchAllColumn($column1,$column2,$column3);		
	}

	public function fetchAllColumnHasWhere($column1,$where){
		return $this->getMapper()->fetchAllColumnHasWhere($column1,$where);		
	}
	
	
	public function customSql($sql){
		return $this->getMapper()->customSql($sql);
	}
	
	public function toArray() {
		$data = array();
		$data['id']					= $this->getId();
		$data['type_question']		= $this->getType_question();
		$data['content'] 			= $this->getContent();
		$data['questiontext']	 	= $this->getQuestiontext();
		$data['subject_id']			= $this->getSubject_id();
		$data['chapter_id'] 		= $this->getChapter_id();
		$data['level']		 		= $this->getLevel();
		$data['classification'] 	= $this->getClassification();
		$data['generalfeedback'] 	= $this->getGeneralfeedback();
		$data['question_title'] 	= $this->getQuestion_title();
		$data['list_test_id'] 		= $this->getList_test_id();
		$data['created_user'] 		= $this->getCreated_user();
		$data['timecreated'] 		= $this->getTimecreated();
		$data['timemodified'] 		= $this->getTimemodified();
		$data['modifiedby'] 		= $this->getModifiedby();
		$data['hidden'] 			= $this->getHidden();

		return $data;
	}
}