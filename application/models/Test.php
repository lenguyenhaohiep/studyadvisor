<?php
require_once 'mapper/Test.php';

class Default_Models_Test {
	protected $_id; // id tĂNG tự động
	protected $_subject_id; // Môn học ,
	protected $_title; // 'tiêu đề bài test',
	protected $_content; // nội dung mô tả bài test
	protected $_duration_test;// thời gian làm 1 bài test: tính bằng phút 
	protected $_shuffle_question; // xáo trộn câu hỏi 0, 1
	protected $_shuffle_answer; // xáo trộn câu trả lời 0, 1
	protected $_question_perpage;// số câu hỏi trên 1 trang
	protected $_attempts_allowed; // số lần làm bài. 0: là không giới hạn
	protected $_list_shedule_exam; // danh sach cac lich thi ma bai test duoc them vao
	protected $_list_question; // id của các câu hỏi
	protected $_list_score;
	protected $_grade_method; // cách tính điểm cho bài test 1: lan cao nhat 	2: diem trung binh 	3: thu nghiem lan dau 	4: kiem tra lan cuoi'
	protected $_decimal_digits_in_grades; // chữ số thập phân sau điểm của bài thi
	protected $_review_after_test; // Những option xem lại bài thi sau khi làm xong
	protected $_review_while_test_open;// Những option xem lại bài thi sau khi làm xong và đề thi vẫn mở
	protected $_review_after_test_close;// Những option xem lại bài thi sau khi làm xong khi bài thi bị đóng
	protected $_date_create; // Ngày tạo bài test
	protected $_date_modify; // Ngày chỉnh sửa bài test gần nhất
	protected $_user_create; // Id giảng viên tạo bài test
	protected $_hidden; // Đóng hay mở đề thi
	protected $_auto_update_level; // Tự động cập nhật độ khó và độ phân cách

	protected $_mapper;

        
        public function customSql($sql){
		return $this->getMapper()->customSql($sql);
	}
        
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
	
	public function setSubject_id($value) {
		$this->_subject_id = $value;
		return $this;
	}
	public function getSubject_id() {
		return $this->_subject_id;
	}	
	
	public function setTitle($value){
		$this->_title = $value;
	}
	public function getTitle(){
		return $this->_title;
	}

	public function setContent($value) {
		$this->_content = $value;
		return $this;
	}
	public function getContent() {
		return $this->_content;
	}

	public function setDuration_test($value) {
		$this->_duration_test = $value;
		return $this;
	}
	public function getDuration_test() {
		return $this->_duration_test;
	}

	public function setShuffle_question($value) {
		$this->_shuffle_question = $value;
		return $this;
	}
	public function getShuffle_question() {
		return $this->_shuffle_question;
	}

	public function setShuffle_answer($value) {
		$this->_shuffle_answer = $value;
		return $this;
	}
	public function getShuffle_answer() {
		return $this->_shuffle_answer;
	}	
	
	public function setQuestion_perpage($value) {
		$this->_question_perpage = $value;
		return $this;
	}
	public function getQuestion_perpage() {
		return $this->_question_perpage;
	}

	public function setAttempts_allowed($value) {
		$this->_attempts_allowed = $value;
		return $this;
	}
	public function getAttempts_allowed() {
		return $this->_attempts_allowed;
	}

	public function setList_shedule_exam($value) {
		$this->_list_shedule_exam = $value;
		return $this;
	}
	public function getList_shedule_exam() {
		return $this->_list_shedule_exam;
	}
	
	public function setList_question($value) {
		$this->_list_question = $value;
		return $this;
	}
	public function getList_question() {
		return $this->_list_question;
	}

	public function setList_score($value) {
		$this->_list_score = $value;
		return $this;
	}
	public function getList_score() {
		return $this->_list_score;
	}
	
	public function setGrade_method($value) {
		$this->_grade_method = $value;
		return $this;
	}
	public function getGrade_method() {
		return $this->_grade_method;
	}

	public function setDecimal_digits_in_grades($value) {
		$this->_decimal_digits_in_grades = $value;
		return $this;
	}
	public function getDecimal_digits_in_grades() {
		return $this->_decimal_digits_in_grades;
	}

	public function setReview_after_test($value) {
		$this->_review_after_test = $value;
		return $this;
	}
	public function getReview_after_test() {
		return $this->_review_after_test;
	}

	public function setReview_while_test_open($value) {
		$this->_review_while_test_open = $value;
		return $this;
	}
	public function getReview_while_test_open() {
		return $this->_review_while_test_open;
	}

	public function setReview_after_test_close($value) {
		$this->_review_after_test_close = $value;
		return $this;
	}
	public function getReview_after_test_close() {
		return $this->_review_after_test_close;
	}
	
	public function setDate_create($value) {
		$this->_date_create = $value;
		return $this;
	}
	public function getDate_create() {
		return $this->_date_create;
	}

	public function setDate_modify($value) {
		$this->_date_modify= $value;
		return $this;
	}
	public function getDate_modify() {
		return $this->_date_modify;
	}

	public function setUser_create($value) {
		$this->_user_create= $value;
		return $this;
	}
	public function getUser_create() {
		return $this->_user_create;
	}	
	
	public function setHidden($value) {
		$this->_hidden = $value;
		return $this;
	}
	public function getHidden() {
		return $this->_hidden;
	}	
	
	public function setAuto_update_level($value) {
		$this->_auto_update_level = $value;
		return $this;
	}
	public function getAuto_update_level() {
		return $this->_auto_update_level;
	}	
	
	
	
/*-------------- END GET SET ATRIBUTE OF TABLE ------------*/
	
	public function setMapper($mapper) {
		$this->_mapper = $mapper;
		return $this;
	}
	public function getMapper() {
		if (null === $this->_mapper) {
			$this->_mapper = new  Default_Models_Mapper_Test();
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
		$data['subject_id']					= $this->getSubject_id();
		$data['title'] 						= $this->getTitle();
		$data['content']	 				= $this->getContent();
		$data['duration_test']				= $this->getDuration_test();
		$data['shuffle_question']			= $this->getShuffle_question();
		$data['shuffle_answer']				= $this->getShuffle_answer();
		$data['question_perpage']			= $this->getQuestion_perpage();
		$data['attempts_allowed']			= $this->getAttempts_allowed();
		$data['list_shedule_exam']			= $this->getList_shedule_exam();
		$data['list_question']				= $this->getList_question();
		$data['list_score']					= $this->getList_score();
		$data['grade_method']				= $this->getGrade_method();
		$data['decimal_digits_in_grades']	= $this->getDecimal_digits_in_grades();
		$data['review_after_test']			= $this->getReview_after_test();
		$data['review_while_test_open']		= $this->getReview_while_test_open();
		$data['review_after_test_close']	= $this->getReview_after_test_close();
		$data['date_create']				= $this->getDate_create();
		$data['date_modify']				= $this->getDate_modify();
		$data['user_create']				= $this->getUser_create();
		$data['hidden']						= $this->getHidden();
		$data['auto_update_level']						= $this->getAuto_update_level();

		return $data;
	}
	
}