<?php
require_once 'mapper/User.php';

class Default_Models_User {
	protected $_id;
	protected $_username;
	protected $_password;
	protected $_group_id; // có 3 nhóm user với các quyền mặc định: admin, teacher, student
	protected $_list_course_join;
	protected $_permission_custom; // cờ kiểm tra user có được modify quyền ko, nếu 1 thì có 0 thì là default
	protected $_user_code;
	protected $_firstname;
	protected $_lastname;
	protected $_date_create;
	protected $_email;
	protected $_yahoo;
	protected $_skyper;
	protected $_phone;
	protected $_department;
	protected $_address;
	protected $_city;
	protected $_firstlogin;
	protected $_lastlogin;
	protected $_isblock;

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

	public function setUsername($value){
		$this->_username = $value;
	}
	public function getUsername(){
		return $this->_username;
	}

	public function setPassword($value) {
		$this->_password = $value;
		return $this;
	}
	public function getPassword() {
		return $this->_password;
	}
	
	public function setGroup_id($value){
		$this->_group_id = $value;
	}
	public function getGroup_id(){
		return $this->_group_id;
	}

	public function setList_course_join($value){
		$this->_list_course_join = $value;
	}
	public function getList_course_join(){
		return $this->_list_course_join;
	}
	
	public function setPermission_custom($value) {
		$this->_permission_custom = $value;
		return $this;
	}
	public function getPermission_custom() {
		return $this->_permission_custom;
	}

	public function setUser_code($value) {
		$this->_user_code = $value;
		return $this;
	}
	public function getUser_code() {
		return $this->_user_code;
	}

	public function setFirstname($value) {
		$this->_firstname = $value;
		return $this;
	}
	public function getFirstname() {
		return $this->_firstname;
	}

	public function setLastname($value) {
		$this->_lastname = $value;
		return $this;
	}
	public function getLastname() {
		return $this->_lastname;
	}
	
	public function setDate_create($value) {
		$this->_date_create = $value;
		return $this;
	}
	public function getDate_create() {
		return $this->_date_create;
	}
	
	public function setEmail($value) {
		$this->_email = $value;
		return $this;
	}
	public function getEmail() {
		return $this->_email;
	}
	
	public function setYahoo($value) {
		$this->_yahoo = $value;
		return $this;
	}
	public function getYahoo() {
		return $this->_yahoo;
	}
	
	public function setSkyper($value) {
		$this->_skyper = $value;
		return $this;
	}
	public function getSkyper() {
		return $this->_skyper;
	}
	
	public function setPhone($value) {
		$this->_phone = $value;
		return $this;
	}
	public function getPhone() {
		return $this->_phone;
	}
	
	public function setDepartment($value) {
		$this->_department = $value;
		return $this;
	}
	public function getDepartment() {
		return $this->_department;
	}
	
	public function setAddress($value) {
		$this->_address = $value;
		return $this;
	}
	public function getAddress() {
		return $this->_address;
	}
	
	public function setCity($value) {
		$this->_city = $value;
		return $this;
	}
	public function getCity() {
		return $this->_city;
	}
	
	public function setFirstlogin($value) {
		$this->_firstlogin = $value;
		return $this;
	}
	public function getFirstlogin() {
		return $this->_firstlogin;
	}
	
	public function setLastlogin($value) {
		$this->_lastlogin = $value;
		return $this;
	}
	public function getLastlogin() {
		return $this->_lastlogin;
	}
	
	public function setIsblock($value) {
		$this->_isblock = $value;
		return $this;
	}
	public function getIsblock() {
		return $this->_isblock;
	}
	
	
	public function setMapper($mapper) {
		$this->_mapper = $mapper;
		return $this;
	}

	public function getMapper() {
		if (null === $this->_mapper) {
			$this->_mapper = new  Default_Models_Mapper_User();
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
		$data['username']					= $this->getUsername();
		$data['password']					= $this->getPassword();
		$data['group_id']					= $this->getGroup_id();
		$data['list_course_join']			= $this->getList_course_join();
		$data['permission_custom']			= $this->getPermission_custom();
		$data['user_code']					= $this->getUser_code();
		$data['firstname']					= $this->getFirstname() ;
		$data['lastname']					= $this->getLastname() ;
		$data['date_create']				= $this->getDate_create() ;
		$data['email']						= $this->getEmail();
		$data['yahoo']						= $this->getYahoo() ;
		$data['skyper']						= $this->getSkyper() ;
		$data['phone']						= $this->getPhone() ;
		$data['department']					= $this->getDepartment() ;
		$data['address']					= $this->getAddress() ;
		$data['city']						= $this->getCity() ;
		$data['firstlogin']					= $this->getFirstlogin() ;
		$data['lastlogin']					= $this->getLastlogin() ;
		$data['isblock']					= $this->getIsblock() ;

		return $data;
	}
	
}
