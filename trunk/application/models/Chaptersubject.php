<?php
require_once 'mapper/Chaptersubject.php';

class Default_Models_ChapterSubject {
	protected $_id;
	protected $_name;
	protected $_subject_id;
	protected $_note;
        protected $_stt;
        protected $_path;

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

	public function setSubject_id($value) {
		$this->_subject_id = $value;
		return $this;
	}
	public function getSubject_id() {
		return $this->_subject_id;
	}

	public function setNote($value) {
		$this->_note = $value;
		return $this;
	}
	public function getNote() {
		return $this->_note;
	}
        
        public function setStt($value){
            $this->_stt = $value;
            return $this;
        }


        public function getStt(){
                return $this->_stt;
        }
        
        public function setPath($value){
            $this->_path=$value;
            return $this;
        }

        public function getPath(){
            return $this->_path;
        }


        public function setMapper($mapper) {
		$this->_mapper = $mapper;
		return $this;
	}

	public function getMapper() {
		if (null === $this->_mapper) {
			$this->_mapper = new  Default_Models_Mapper_ChapterSubject();
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
		$data['name'] 				= $this->getName();
		$data['subject_id']	 		= $this->getSubject_id();
		$data['note']				= $this->getNote() ;
                $data['stt']= $this->getStt();
                $data['path'] = $this->getPath();

		return $data;
	}
	
}