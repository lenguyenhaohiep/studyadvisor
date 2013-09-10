<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once APPLICATION_PATH . '/models/UserVector.php';
require_once APPLICATION_PATH . '/models/QuestionVector.php';

class Recommend {

    public function dot($a, $b) {
        $length = count($a);
        $result = 0;
        for ($i = 0; $i < $length; $i++) {
            $result += $a[$i] * $b[$i];
        }
        return $result;
    }

    public function getPredictiveRatingQuestion($questionID, $userID) {

        //get Average Rating;
        $avg = 0;


        //Get $userVector
        $model = new Default_Models_UserVector();
        $model->find('user_id', $userID);
        $userVector = Zend_json::decode($models->getVector());
        $biasUser = $model->getBias();

        //get last Context Of User
        $Context = "";

        //get $itemVector
        $modelItem = new Default_Models_QuestionVector();
        $where = "question_id = $questionID and level= '$Context'";
        $modelItem->fetch($where);
        foreach ($modelItem as $key => $value) {
            $i = $value->toArray();
            $itemVector = Zend_json::decode($i->getVector());
            $biasItem = $i->getBias();
            break;
        }

        //get ContextAffect
        $contextAffect = 0;


        $predictiveRating = $biasItem + $biasUser + dot($userVector, $itemVector) + $avg + $contextAffect;
        if ($predictiveRating > 1)
            $predictiveRating = 1;
        elseif ($predictiveRating < 0)
            $predictiveRating = 0;

        return $predictiveRating;
    }

    public function getPredictiveRatingLesson($chapterID, $userID) {
        //get all candidate items belong to $chapter;
        $candidate = array();
        $predictiveRatingList = array();
        foreach ($candidate as $key => $value) {
            $predictiveRatingList[] = $this->getPredictiveRatingQuestion($value, $userID);
        }
        $result = array_sum($predictiveRatingList)/count ($predictiveRatingList);
        return $result;
        
    }

    public function getRecommendation($classID, $userID, $level, $number) {
        return array(1460, 1756);
    }

    public function getRecommendationWithChapters($userID, $chapters, $level, $number) {
        return array(array(1460, 1756));
        $result = array();
        for ($i = 0; $i < count($chapters); $i++) {
            $result[] = $this->getRecommendationWithSingleChapter($userID, $c[$i], $level, $number[$i]);
        }
        return $result;
    }

    public function getRecommendationWithSingleChapter($userID, $chapters, $level, $number) {
        return array(1460, 1756);
    }

    public function getRecommendationLesson($userID, $testID) {
        return array(29, 30, 31, 32, 33);
    }

    public function sort($array, $level, $number) {
        //Từ khó đến dễ
        if ($level == 1) {
            //Sort theo thứ tự giảm dần
        } else {
            //Từ dễ đến khó => sort theo thứ tự tăng dần
        }
        $result = array();
        for ($i = 0; $i < $number; $i++) {
            $result = $array[$i][0];
        }
        return $result;
    }

    public function buildModel() {
        
    }

}

?>
