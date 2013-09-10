<?php

require_once 'mapper/Recommendation.php';
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Default_Models_Recommendation {
protected $_id;
    protected $_userid;
    protected $_testid;
    protected $_list_recommendations;
    protected $_mapper;

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
    public function getRecommendations() {
        return $this->_list_recommendations;
    }

    public function setRecommendations($value) {
        $this->_list_recommendations = $value;
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

    public function getTestID() {
        return $this->_testid;
    }

    public function setTestID($value) {
        $this->_testid = $value;
        return $this;
    }

    public function setMapper($mapper) {
        $this->_mapper = $mapper;
        return $this;
    }

    public function getMapper() {
        if (null === $this->_mapper) {
            $this->_mapper = new Default_Models_Mapper_Recommendation();
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
        $data['test_id'] = $this->getTestID();
        $data['list_recommendations'] = $this->getRecommendations();
        return $data;
    }

}

?>
