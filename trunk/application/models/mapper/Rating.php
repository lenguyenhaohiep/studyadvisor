<?php

require_once APPLICATION_PATH . "/models/DbTable/Rating.php";

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 * 
 */

class Default_Models_Mapper_Rating {

    protected $_dbTable;

    public function setDbTable($dbTable) {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }

    public function getDbTable() {
        if (null === $this->_dbTable) {
            $this->setDbTable('Default_Models_DbTable_Rating');
        }
        return $this->_dbTable;
    }

    public function save(Default_Models_Rating $model) {
        $data = array(
            'user_id' => $model->getUserID(),
            'question_id' => $model->getQuestionID(),
            'level' => $model->getLevel(),
            'result' => $model->getResult(),
            'date' => $model->getDate(),
        );
        if (null === ($id = $model->getId())) {
            unset($data['id']);
            $this->getDbTable()->insert($data);
            $last_id = $this->getDbTable()->getDefaultAdapter()->lastInsertId("quizuit_rating", "id");
            return $last_id;
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }

    public function fetchAll($where = null, $order = null, $count = null, $offset = null) {
        $table = $this->getDbTable();

        $resultSet = $table->fetchAll($where, $order, $count, $offset);
        if (0 == count($resultSet)) {
            return;
        }
        $entries = array();

        foreach ($resultSet as $row) {
            $model = new Default_Models_Rating();

            $model->setId($row->id);
            $model->setDate($row->date);
            $model->setUserID($row->user_id);
            $model->setQuestionID($row->question_id);
            $model->setLevel($row->level);
            $model->setResult($row->result);
            $entries[] = $model;
        }
        return $entries;
    }

    public function find($key, $value, Default_Models_Rating $model) {
        $table = $this->getDbTable();
        $row = $table->fetchRow(
                $table->select()
                        ->where($key . ' = ?', $value)
        );
        if (0 == count($row)) {
            return 0;
        }

        $model->setId($row->id);
        $model->setDate($row->date);
        $model->setUserID($row->user_id);
        $model->setQuestionID($row->question_id);
        $model->setLevel($row->level);
        $model->setResult($row->result);
    }

    public function delete($key, $value) {
        if (is_array($value)) {
            $in = implode(',', $value);
            $where = $this->getDbTable()->getAdapter()->quoteInto($key . ' in (?)', $in);
        } else {
            $where = $this->getDbTable()->getAdapter()->quoteInto($key . '= ?', $value);
        }
        $this->getDbTable()->delete($where);
    }

}

?>
