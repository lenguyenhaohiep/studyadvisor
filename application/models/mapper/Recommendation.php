<?php

require_once APPLICATION_PATH . '/models/DbTable/Recommendation.php';

class Default_Models_Mapper_Recommendation {

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
            $this->setDbTable('Default_Models_DbTable_Recommendation');
        }
        return $this->_dbTable;
    }

    public function save(Default_Models_Recommendation $model) {
        $data = array(
            'user_id' => $model->getUserID(),
            'test_id' => $model->getTestID(),
            'list_recommendations'=>$model->getRecommendations()
        );
        if (null === ($id = $model->getId())) {
            unset($data['id']);
            $this->getDbTable()->insert($data);
            $last_id = $this->getDbTable()->getDefaultAdapter()->lastInsertId("quizuit_recommendation", "id");
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
            $entry = new Default_Models_Recommendation();
            
            $entry->setID($row->id);
            $entry->setUserID($row->user_id);
            $entry->setTestID($row->test_id);
            $entry->setRecommendations($row->list_recommendations);
            
            $entries[] = $entry;
        }
        return $entries;
    }

    public function find($key, $value, Default_Models_Recommendation $model) {
        $table = $this->getDbTable();
        $row = $table->fetchRow(
                $table->select()
                        ->where($key . ' = ?', $value)
        );
        if (0 == count($row)) {
            return 0;
        }

        $model->setID($row->id);
        $model->setUserID($row->user_id);
        $model->setTestID($row->test_id);
        $model->setRecommendations($row->list_recommendations);
       
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
