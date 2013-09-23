<?php

require_once 'mapper/UserVector.php';
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Default_Models_UserVector {
protected $_id;
    protected $_userid;
    protected $_vector;
    protected $_bias;
    protected $_mapper;
    protected $_last_context_id;
    protected $_last_imp_vector;

    public function __construct(array $option = null) {
        if (is_array($option)) {
            $this->setOptions($option);
        }
    }

    public function __set($name, $value) {
        $method = 'set' . $name;
        if (('mapper' == $method) || !method_exists($this, $method)) {
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
        foreach ($options as $key => $value) {
            $method = "set" . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }

    /* -------------- GET SET ATRIBUTE OF TABLE ------------ */
    public function getBias() {
        return $this->_bias;
    }

    public function setBias($value) {
        $this->_bias = $value;
        return $this;
    }
    public function getID() {
        return $this->_id;
    }

    public function setID($value) {
        $this->_id = $value;
        return $this;
    }
    public function getUserID() {
        return $this->_userid;
    }

    public function setUserID($value) {
        $this->_userid = $value;
        return $this;
    }

    public function getVector() {
        return $this->_vector;
    }

    public function setVector($value) {
        $this->_vector = $value;
        return $this;
    }
    
    public function getLastContextID(){
        return $this->_last_context_id;
    }
    
    public function setLastContextID($value){
        $this->_last_context_id = $value;
        return $this;
    }
    
    public function getLastImpVector(){
        return $this->_last_imp_vector;
    }
    
    public function setLastImpVector($value){
        $this->_last_imp_vector=$value;
        return $this;
    }

    
    public function setMapper($mapper) {
        $this->_mapper = $mapper;
        return $this;
    }

    public function getMapper() {
        if (null === $this->_mapper) {
            $this->_mapper = new Default_Models_Mapper_UserVector();
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
        return $this->getMapper()->fetchAll($where, $order, $count, $offset);
    }

    public function toArray() {
        $data = array();
        $data['id']=$this->getID();
        $data['user_id'] = $this->getUserID();
        $data['vector'] = $this->getVector();
        $data['bias'] = $this->getBias();
        $data['last_context_id']=$this->getLastContextID();
        $data['last_imp_vector']=$this->getLastImpVector();
        return $data;
    }

}

?>
