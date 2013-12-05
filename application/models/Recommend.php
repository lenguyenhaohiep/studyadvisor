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

    public $para;

    public function getParameters($userID) {
        $link = mysql_connect('localhost', 'root', '');
        if (!$link) {
            die("No Connect");
        } else {
            mysql_select_db("quizuitis01");
        }

        $sql = "select (s/c) as avgrating from ( SELECT count(*) as c,sum(result) as s FROM `quizuit_study_result`) t";
        $result = mysql_query($sql);
        
        $res = mysql_fetch_array($result);
        $this->para['avg'] = $res["avgrating"];

        //Get $userVector
        $sql = "select * from model_user_vector where user_id=$userID";
        $result = mysql_query($sql);
        $res = mysql_fetch_array($result);
        if (mysql_num_rows($result) > 0) {
            $this->para['biasUser'] = $res['bias'];
            $this->para['Context'] = $res['last_context_id'];
            $this->para['userVector'] = $res['vector'];
        } else {
            $this->para['biasUser'] = 0;
            $this->para['Context'] = 0;
            $this->para['userVector'] = "[0.01,0.019,0.028,0.037,0.046,0.055,0.064,0.073,0.082,0.091]";
        }

        $sql = "select (s/c) as avgrating from ( SELECT count(*) as c,sum(result) as s FROM `quizuit_study_result` where user_id=$userID) t";
        $result = mysql_query($sql);
        $res = mysql_fetch_array($result);
        $avg = $res["avgrating"];

        $sql = "select (s/c) as avgrating from ( SELECT count(*) as c,sum(result) as s FROM `quizuit_study_result` where user_id=$userID and is_labelled=" . $this->para['Context'] . ") t";
        $result = mysql_query($sql);
        $res = mysql_fetch_array($result);
        $avgu = $res["avgrating"];

        $this->para['contextAffect'] = $avgu - $avg;
    }

    public function dot($_a, $_b) {
        $a = Zend_Json::decode($_a);
        $b = Zend_Json::decode($_b);
        $length = count($a);
        $result = 0;
        for ($i = 0; $i < $length; $i++) {
            $result += $a[$i] * $b[$i];
        }
        return $result;
    }

    public function getPredictiveRatingQuestion($questionID, $userID, $level) {
        $context = $this->para['Context'];

        $sql = "select * from model_question_vector where question_id=$questionID and context_id='$context'";
        $result = mysql_query($sql);
        $res = mysql_fetch_array($result);
        if (mysql_num_rows($result) > 0) {
            $biasItem = $res['bias'];
            $itemVector = $res['vector'];
        } else {
            $biasItem = 0;
            $itemVector = "[0.01,0.019,0.028,0.037,0.046,0.055,0.064,0.073,0.082,0.091]";
        }

        $predictiveRating = $biasItem + $this->para['biasUser'] + $this->dot($this->para['userVector'], $itemVector) + $this->para['avg'] + $this->para['contextAffect'];
        if ($predictiveRating > 1)
            $predictiveRating = 1;
        elseif ($predictiveRating < 0)
            $predictiveRating = 0;

        return $predictiveRating;
    }

    //Khuyến nghị bài học
    public function getPredictiveRatingLesson($chapterID, $userID, $level) {
        if (!isset($this->para))
            $this->getParameters($userID);
        //return rand(1, 5);
        //get all candidate items belong to $chapter;

        $candidate = $this->getCandidateItems($chapterID, $userID);
        $predictiveRatingList = array();
        echo count($candidate);
        foreach ($candidate as $key => $val) {
            $predictiveRatingList[] = $this->getPredictiveRatingQuestion($val->getId(), $userID, 0);
        }
        $result = array_sum($predictiveRatingList) / count($predictiveRatingList);
        return $result;
    }

    //Khuyến nghị tổng hợp
    public function getRecommendation($classID, $userID, $level, $number) {
        if (!isset($this->para))
            $this->getParameters($userID);
        $modelQuestion = new Default_Models_Question();
        $result = $modelQuestion->fetchAll();
        $rec = array();

        foreach ($result as $key => $itemQuestion) {
            $questionID = $itemQuestion->getId();
            $rec[$questionID] = $this->getPredictiveRatingQuestion($questionID, $userID, $level);
        }

        return $this->sort($rec, $level, $number);
    }

    //pesonalized recommendation - fixed
    public function getRecommendationWithChapters($userID, $chapters, $level, $number) {
        if (!isset($this->para))
            $this->getParameters($userID);
        $result = array();
        for ($i = 0; $i < count($chapters); $i++) {
            if ($i == 0)
                $result[0] = $this->getRecommendationWithSingleChapter($userID, $chapters[$i], $level, $number[$i]);
            else {
                $result[0] = array_merge($result[0], $this->getRecommendationWithSingleChapter($userID, $chapters[$i], $level, $number[$i]));
            }
        }

        return $result;
    }

    // Đã xong// Khuyến nghị bài tập trong bài học - fixed
    public function getRecommendationWithSingleChapter($userID, $chapterID, $level, $number) {
        if (!isset($this->para))
            $this->getParameters($userID);

        $result = $this->getCandidateItems($chapterID, $userID);
        $rec = array();

        foreach ($result as $key => $itemQuestion) {
            $questionID = $itemQuestion->getId();
            $rec[$questionID] = $this->getPredictiveRatingQuestion($questionID, $userID, $level);
        }

        return $this->sort($rec, $level, $number);
    }

    //Get List of recommended lessons - fixed
    public function getRecommendationLesson($userID, $testID, $level) {
        if (!isset($this->para))
            $this->getParameters($userID);
        $modelTest = new Default_Models_Test();
        $result = $modelTest->find("id", $testID);
        //Get Questions in The test
        $QuestionsSet = "(" . $result->getList_question() . ")";

        //get all related-chapters
        $sql = "SELECT distinct(`chapter_id`) FROM `quizuit_questions` WHERE `id` IN " . $QuestionsSet;
        $listChapters = $modelTest->customSql($sql);
        foreach ($listChapters as $key => $val) {
            $chapterID = $val['chapter_id'];
            //Chapter=0 là dữ liệu câu hỏi chưa thuộc 1 chủ đề nào
            if ($chapterID != 0) {
                $rec[$chapterID] = $this->getPredictiveRatingLesson($chapterID, $userID, $level);
            }
        }

        return $this->sortBasedThresold($rec, 0.75);
    }

    //Sort list of lessons - fixed
    public function sortBasedThresold($array, $thresold) {
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
        $i = 0;
        foreach ($array as $key => $val) {
            $list[] = $key;
            $i++;
            if ($i == $number)
                break;
        }
        return $list;
    }

    //Un-fixed
    public function getCandidateItems($chapterID, $userID) {
        $modelQuestion = new Default_Models_Question();
        $result = $modelQuestion->fetchAll("chapter_id = $chapterID");


        return $result;
    }

}

?>
