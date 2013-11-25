<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once APPLICATION_PATH . '/models/UserVector.php';
require_once APPLICATION_PATH . '/models/Question.php';
require_once APPLICATION_PATH . '/models/QuestionVector.php';
require_once APPLICATION_PATH . '/models/Test.php';

class Recommend {

    public function dot($a, $b) {
        $length = count($a);
        $result = 0;
        for ($i = 0; $i < $length; $i++) {
            $result += $a[$i] * $b[$i];
        }
        return $result;
    }

    public function getPredictiveRatingQuestion($questionID, $userID, $level) {
        return rand(1,5);
//        //get Average Rating;
//        $avg = 0;
//
//
//        //Get $userVector
//        $model = new Default_Models_UserVector();
//        $model->find('user_id', $userID);
//        $userVector = Zend_json::decode($models->getVector());
//        $biasUser = $model->getBias();
//
//        //get last Context Of User
//        $Context = "";
//
//        //get $itemVector
//        $modelItem = new Default_Models_QuestionVector();
//        $where = "question_id = $questionID and level= '$Context'";
//        $modelItem->fetch($where);
//        foreach ($modelItem as $key => $value) {
//            $i = $value->toArray();
//            $itemVector = Zend_json::decode($i->getVector());
//            $biasItem = $i->getBias();
//            break;
//        }
//
//        //get ContextAffect
//        $contextAffect = 0;
//
//
//        $predictiveRating = $biasItem + $biasUser + dot($userVector, $itemVector) + $avg + $contextAffect;
//        if ($predictiveRating > 1)
//            $predictiveRating = 1;
//        elseif ($predictiveRating < 0)
//            $predictiveRating = 0;
//
//        return $predictiveRating;
    }

    //Khuyến nghị bài học
    public function getPredictiveRatingLesson($chapterID, $userID, $level) {
        //return rand(1, 5);
        //get all candidate items belong to $chapter;
               
        $candidate = $this->getCandidateItems($chapterID, $userID);
        $predictiveRatingList = array();
        echo count($candidate);
        foreach ($candidate as $key=>$val) {
            $predictiveRatingList[] = $this->getPredictiveRatingQuestion($val->getId(), $userID, 0);
        }
        $result = array_sum($predictiveRatingList) / count($predictiveRatingList);
        return $result;
    }

    //Khuyến nghị tổng hợp
    public function getRecommendation($classID, $userID, $level, $number) {
  
        $modelQuestion = new Default_Models_Question();
        $result = $modelQuestion->fetchAll();
        $rec = array();
        
        foreach ($result as $key => $itemQuestion) {
            $questionID=$itemQuestion->getId();
            $rec[$questionID]=$this->getPredictiveRatingQuestion($questionID, $userID, $level);
        }
        
        return $this->sort($rec, $level, $number);
    }
    
    
    //pesonalized recommendation - fixed
    public function getRecommendationWithChapters($userID, $chapters, $level, $number) {
        
        $result = array();
        for ($i = 0; $i < count($chapters); $i++) {
            if ($i==0)
            $result[0] = $this->getRecommendationWithSingleChapter($userID, $chapters[$i], $level, $number[$i]);
            else{
            $result[0] = array_merge($result[0], $this->getRecommendationWithSingleChapter($userID, $chapters[$i], $level, $number[$i]));
            }
        }
        
        return $result;
    }

    // Đã xong// Khuyến nghị bài tập trong bài học - fixed
    public function getRecommendationWithSingleChapter($userID, $chapterID, $level, $number) {
//        return array(1460, 1756);
             
        $result=$this->getCandidateItems($chapterID, $userID);
        $rec = array();
        
        foreach ($result as $key => $itemQuestion) {
            $questionID=$itemQuestion->getId();
            $rec[$questionID]=$this->getPredictiveRatingQuestion($questionID, $userID, $level);
        }
        
        return $this->sort($rec, $level, $number);
    }

    //Get List of recommended lessons - fixed
    public function getRecommendationLesson($userID, $testID, $level) {
        
        $modelTest = new Default_Models_Test();
        $result = $modelTest->find("id", $testID);
        //Get Questions in The test
        $QuestionsSet = "(".$result->getList_question().")";
        
        //get all related-chapters
        $sql = "SELECT distinct(`chapter_id`) FROM `quizuit_questions` WHERE `id` IN ".$QuestionsSet;
        $listChapters=$modelTest->customSql($sql);
        foreach ($listChapters as $key=>$val){
            $chapterID =  $val['chapter_id'];
            //Chapter=0 là dữ liệu câu hỏi chưa thuộc 1 chủ đề nào
            if ($chapterID != 0){
            $rec[$chapterID]=$this->getPredictiveRatingLesson($chapterID, $userID, $level);
            }
        }
      
        return $this->sortBasedThresold($rec, 5);
    }
    
    //Sort list of lessons - fixed
    public function sortBasedThresold($array, $thresold){
        asort($array);
        $list = array();
        foreach ($array as $key => $value) {
            if ($value <= $thresold)
            $list[] = $key;
        }
        return $list;
    }

    //fixed
    public function sort($array, $level, $number) {
        
        //Từ khó đến dễ
        if ($level == 1) {
            asort($array);
        } else {
            //Từ dễ đến khó
            arsort($array);
        }
        $list = array();
        $i=0;
        foreach ($array as $key=>$val) {
            $list[] = $key;
            $i++;
            if ($i == $number)
                break;
        }
        return $list;
    }
    
    //Un-fixed
    public function getCandidateItems($chapterID, $userID){
        $modelQuestion = new Default_Models_Question();
        $result = $modelQuestion->fetchAll("chapter_id = $chapterID");
        
        
        return $result;
    }
}

?>
