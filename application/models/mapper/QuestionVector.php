<?php

require_once APPLICATION_PATH . '/models/DbTable/QuestionVector.php';

class Default_Models_Mapper_QuestionVector {

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
            $this->setDbTable('Default_Models_DbTable_QuestionVector');
        }
        return $this->_dbTable;
    }

    public function save(Default_Models_QuestionVector $model) {
        $data = array(
            'question_id' => $model->getQuestionID(),
            'vector' => $model->getVector(),
            'level'=> $model->getLevel(),
            'bias'=>$model->getBias()
        );
        if (null === ($id = $model->getId())) {
            unset($data['id']);
            $this->getDbTable()->insert($data);
            $last_id = $this->getDbTable()->getDefaultAdapter()->lastInsertId("quizuit_question_vector", "id");
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
            $entry = new Default_Models_QuestionVector();
            $entry->setID($row->id);
            $entry->setQuestionID($row->question_id);
            $entry->setVector($row->vector);
            $entry->setLevel($row->level);
            $entry->setBias($row->bias);
            $entries[] = $entry;
        }
        return $entries;
    }

    public function find($key, $value, Default_Models_QuestionVector $model) {
        $table = $this->getDbTable();
        $row = $table->fetchRow(
                $table->select()
                        ->where($key . ' = ?', $value)
        );
        if (0 == count($row)) {
            return 0;
        }

            $model->setID($row->id);
            $model->setQuestionID($row->question_id);
            $model->setVector($row->vector);
            $model->setLevel($row->level);
            $model->setBias($row->bias);
       
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
