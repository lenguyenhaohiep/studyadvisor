<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once APPLICATION_PATH . '/models/MatrixFactorization.php';
require_once APPLICATION_PATH . '/models/STI.php';
require_once APPLICATION_PATH . '/models/User.php';
require_once APPLICATION_PATH . '/models/Rating.php';
require_once APPLICATION_PATH . '/models/Question.php';


class RecommendController extends Zend_Controller_Action{
    public  function buildmodelAction(){        
        $modelUser = new Default_Models_User();
        $result = $modelUser->fetchAll("group_id = 2");
        
        $users=array();
        $usersIndex=array();
        $i=0;
        foreach ($result as $key => $value) {
            $u = $value->toArray();
            $users[] = $u['id'];
            $usersIndex[$u['id']]=$i;
            $i++;
        }
        
        $modelQuestion = new Default_Models_Question();
        $result = $modelQuestion->fetchAll();
        $items = array();
        $itemIndex = array();
        $ii=0;
        foreach ($result as $key => $value) {
            $i = $value->toArray();
            $items[] = $i['id'];
            $itemIndex[$i['id']] = $ii;
            $ii++;
        }
        
        $context = array("beginner","intermediate","advanced");
        $contextIndex = array("beginner"=>0,"intermediate"=>1,"advanced"=>2);
        
        $modelRating = new Default_Models_Rating();
        $result = $modelRating->fetchAll();
        $ratings = array();
        foreach ($result as $key => $value) {
            $rating = $value->toArray();
            $index = $usersIndex[$rating['user_id']];
            $index .= "-".$itemIndex[$rating['question_id']];
            $index .= "-".$contextIndex[$rating['level']];
            $ratings[$index]=$rating['result'];
        }
        
        
        echo "Build data finished";
        //Train Model
        $stiModel = new STI();
        $stiModel->ratings=$rating;
        $stiModel->numUsers= count($users);
        $stiModel->numContexts= count ($context);
        $stiModel->numItems= count ($items);
        
        $stiModel->run();
        die;
    }
 }
?>
