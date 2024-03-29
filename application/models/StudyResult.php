<?php

require_once 'mapper/StudyResult.php';
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Default_Models_StudyResult {

    protected $_id;
    protected $_date;
    protected $_user_id;
    protected $_question_id;
    protected $_is_labelled;
    protected $_result;
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

    public function getID() {
        return $this->_id;
    }

    public function setID($value) {
        $this->_id = $value;
        return $this;
    }

    public function getDate() {
        return $this->_date;
    }

    public function setDate($value) {
        $this->_date = $value;
        return $this;
    }

    public function getUserID() {
        return $this->_user_id;
    }

    public function setUserID($value) {
        $this->_user_id = $value;
        return $this;
    }

    public function getQuestionID() {
        return $this->_question_id;
    }

    public function setQuestionID($value) {
        $this->_question_id = $value;
        return $this;
    }

    public function getIsLabelled() {
        return $this->_is_labelled;
    }

    public function setIsLabelled($value) {
        $this->_is_labelled = $value;
        return $this;
    }

    public function getResult() {
        return $this->_result;
    }

    public function setResult($value) {
        $this->_result = $value;
        return $this;
    }

    public function setMapper($mapper) {
        $this->_mapper = $mapper;
        return $this;
    }

    public function getMapper() {
        if (null === $this->_mapper) {
            $this->_mapper = new Default_Models_Mapper_StudyResult();
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
        $data = array(
            'id' => $this->getID(),
            'date' => $this->getDate(),
            'user_id' => $this->getUserID(),
            'question_id' => $this->getQuestionID(),
            'result' => $this->getResult(),
            'is_labelled' => $this->getIsLabelled()
        );

        return $data;
    }

}

?>
