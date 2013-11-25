<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once APPLICATION_PATH . '/models/UserVector.php';
require_once APPLICATION_PATH . '/models/QuestionVector.php';
require_once APPLICATION_PATH . '/models/Recommend.php';
class RecommendController extends Zend_Controller_Action{
    
    public function testAction(){

        $userid=263;
        $testid=138;
        $modelRecommend = new Recommend();
        $modelRecommend->getRecommendationLesson($userid, $testid, 1);
        die;
    }

        private function DBConnect() {
        $link = mysql_connect('localhost', 'root', '');
        if (!$link) {
            die('Could not connect: ' . mysql_error());
        } else {
            mysql_select_db("quizuitis01");
        }
    }
    private function RetrieveData() {
        //Lay danh sach User
        $sql = "select * from quizuit_user where group_id = 2";
        $result = mysql_query($sql);
        while ($u = mysql_fetch_array($result)) {
            $users[] = intval($u['id']);
        }

        //Lay Danh sach Cau hoi
        $itemsIndex = array();
        $sql = "select * from quizuit_questions";
        $result = mysql_query($sql);
        while ($ii = mysql_fetch_array($result)) {
            $items[] = intval($ii['id']);
        }

        $contexts = array(0, 1, 2, 3, 4, 5 ,6);

        //LayMatran Danh gia ==> Temp;
        $sql = "SELECT * , corrects / times AS rating
                FROM (
                        SELECT  r1.User_id AS user_id, 
                                r1.question_id AS question_id, 
                                (
                                SELECT COUNT( * ) 
                                FROM  `quizuit_study_result` r2
                                WHERE r2.user_id = r1.user_id
                                AND r2.question_id = r1.question_id
                                AND Is_Labelled IS NULL
                                ) AS times, 
                                (
                                SELECT COUNT( * ) 
                                FROM  `quizuit_study_result` r2
                                WHERE r2.user_id = r1.user_id
                                AND r2.question_id = r1.question_id
                                AND r2.result =1
                                AND Is_Labelled IS NULL
                                ) AS corrects
                        FROM  `quizuit_study_result` r1
                        WHERE Is_Labelled IS NULL 
                        GROUP BY User_id, question_id
                    )   TABLE1";
        $result = mysql_query($sql);
        
        $ratingMatrix = array();
        while ($rating= mysql_fetch_array($result)){
            $r= array(
            "userid" => intval($rating['user_id']),
            "itemid" => intval($rating['question_id']),
            "contextid"=> intval("0"),
            "rating" => floatval($rating['rating'])
            );
            $ratingMatrix[]=$r;
            
        }
        
        $data = array(
            'ratings'=>$ratingMatrix,
            'numContexts'=>count($contexts),
            'numItems'=>count($items),
            'numUsers'=>count($users),
            "users"=>$users,
            "items"=>$items,
            "contexts"=>$contexts
        );
        echo Zend_Json::encode($data);
    }
    
    public  function obtaindataAction(){  
        $this->DBConnect();
        $this->RetrieveData();
        die;
    
    }      
    
    public  function sendparametersAction(){  
        $request = $this->getRequest();
        $data = $request->getParams();
        $parameters = Zend_Json::decode($data['parameters']);
        $users = $parameters['list_Users'];
        foreach ($users as $key => $u) {
            $modelUserVector = new Default_Models_UserVector();
            $modelUserVector->setUserID($u['user_id']);
            $modelUserVector->setBias($u['bias']);
            $modelUserVector->setVector(Zend_Json::encode($u['vector']));
            $modelUserVector->save();
        }
        
        $items = $parameters['list_Items'];
        foreach ($items as $key => $i) {
            $modelQuestionVector = new Default_Models_QuestionVector();
            $modelQuestionVector->setBias($i['bias']);
            $modelQuestionVector->setVector(Zend_Json::encode($i['vector']));
            $modelQuestionVector->setQuestionID($i['question_id']);
            $modelQuestionVector->setContextID($i['context_id']);
            $modelQuestionVector->save();
        }
        die;
    }      

 }
?>
