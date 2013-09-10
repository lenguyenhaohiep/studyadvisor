<?php
require_once 'mapper/Historyofusertest.php';

class Default_Models_Historyofusertest {
	protected $_id;
	protected $_shedule_exam_id;
	protected $_class_id;
	protected $_user_id;
	protected $_test_id;
	protected $_time_start;
	protected $_time_finished;
	protected $_list_question_id;
	protected $_list_answer_of_user;
	protected $_list_score_of_question;
	protected $_list_score_table_test;
	protected $_total_score;
	protected $_essay_list_question_id;
	protected $_essay_list_answer_of_user;
	
	protected $_essay_list_score_of_question;
	protected $_essay_list_score_table_test;
	protected $_do_test_again;
	protected $_stop_do_test_now;
	protected $_first_load_done;
	
	
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
	
	public function setShedule_exam_id($value) {
		$this->_shedule_exam_id = $value;
		return $this;
	}
	public function getShedule_exam_id() {
		return $this->_shedule_exam_id;
	}	
	
	public function setClass_id($value) {
		$this->_class_id = $value;
		return $this;
	}
	public function getClass_id() {
		return $this->_class_id;
	}	
	
	public function setUser_id($value){
		$this->_user_id = $value;
	}
	public function getUser_id(){
		return $this->_user_id;
	}
	
	public function setTest_id($value) {
		$this->_test_id = $value;
		return $this;
	}
	public function getTest_id() {
		return $this->_test_id;
	}

	public function setTime_start($value) {
		$this->_time_start = $value;
		return $this;
	}
	public function getTime_start() {
		return $this->_time_start;
	}
	
	public function setTime_finished($value) {
		$this->_time_finished = $value;
		return $this;
	}
	public function getTime_finished() {
		return $this->_time_finished;
	}

    public function setList_question_id($value) {
		$this->_list_question_id = $value;
		return $this;
	}
	public function getList_question_id() {
		return $this->_list_question_id;
	}
	
    public function setList_answer_of_user($value) {
		$this->_list_answer_of_user= $value;
		return $this;
	}
	public function getList_answer_of_user() {
		return $this->_list_answer_of_user;
	}
	
    public function setList_score_of_question($value) {
		$this->_list_score_of_question  = $value;
		return $this;
	}
	public function getList_score_of_question() {
		return $this->_list_score_of_question;
	}
	

    public function setList_score_table_test($value) {
		$this->_list_score_table_test  = $value;
		return $this;
	}
	public function getList_score_table_test() {
		return $this->_list_score_table_test;
	}
	
    public function setTotal_score($value) {
		$this->_total_score = $value;
		return $this;
	}
	public function getTotal_score() {
		return $this->_total_score;
	}

    public function setEssay_list_question_id($value) {
		$this->_essay_list_question_id = $value;
		return $this;
	}
	public function getEssay_list_question_id() {
		return $this->_essay_list_question_id;
	}

    public function setEssay_list_answer_of_user($value) {
		$this->_essay_list_answer_of_user = $value;
		return $this;
	}
	public function getEssay_list_answer_of_user() {
		return $this->_essay_list_answer_of_user;
	}

    public function setEssay_list_score_of_question($value) {
		$this->_essay_list_score_of_question = $value;
		return $this;
	}
	public function getEssay_list_score_of_question() {
		return $this->_essay_list_score_of_question;
	}
	
	
    public function setEssay_list_score_table_test($value) {
		$this->_essay_list_score_table_test = $value;
		return $this;
	}
	public function getEssay_list_score_table_test() {
		return $this->_essay_list_score_table_test;
	}
	
    public function setDo_test_again($value) {
		$this->_do_test_again = $value;
		return $this;
	}
	public function getDo_test_again() {
		return $this->_do_test_again;
	}
	
    public function setStop_do_test_now($value) {
		$this->_stop_do_test_now = $value;
		return $this;
	}
	public function getStop_do_test_now() {
		return $this->_stop_do_test_now;
	}
	
    public function setFirst_load_done($value) {
		$this->_first_load_done = $value;
		return $this;
	}
	public function getFirst_load_done() {
		return $this->_first_load_done;
	}	
	
	
	
	/*-------------- END GET SET ATRIBUTE OF TABLE ------------*/
	
	public function setMapper($mapper) {
		$this->_mapper = $mapper;
		return $this;
	}

	public function getMapper() {
		if (null === $this->_mapper) {
			$this->_mapper = new  Default_Models_Mapper_Historyofusertest();
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
		$data['shedule_exam_id']		= $this->getShedule_exam_id();
		$data['class_id']				= $this->getClass_id();
		$data['user_id'] 				= $this->getUser_id();
		$data['test_id']	 			= $this->getTest_id();
		$data['time_start']				= $this->getTime_start();
		$data['time_finished'] 			= $this->getTime_finished();
		$data['list_question_id'] 		= $this->getList_question_id();
		$data['list_answer_of_user'] 	= $this->getList_answer_of_user();
		$data['list_score_of_question'] = $this->getList_score_of_question();
		$data['list_score_table_test'] = $this->getList_score_table_test();
		$data['total_score'] 			= $this->getTotal_score();
		$data['essay_list_question_id'] 			= $this->getEssay_list_question_id();
		$data['essay_list_answer_of_user'] 			= $this->getEssay_list_answer_of_user();
		$data['essay_list_score_of_question'] 		= $this->getEssay_list_score_of_question();
		$data['essay_list_score_table_test'] 		= $this->getEssay_list_score_table_test();
		$data['do_test_again'] 						= $this->getDo_test_again();
		$data['stop_do_test_now'] 						= $this->getStop_do_test_now();
		$data['first_load_done'] 						= $this->getFirst_load_done();
		

		return $data;
	}
}