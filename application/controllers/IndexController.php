<?php

require_once APPLICATION_PATH . '/models/Classhasstudent.php';
require_once APPLICATION_PATH . '/models/Classs.php';
require_once APPLICATION_PATH . '/models/User.php';
require_once APPLICATION_PATH . '/models/Test.php';
require_once APPLICATION_PATH . '/models/Course.php';
require_once APPLICATION_PATH . '/models/Question.php';
require_once APPLICATION_PATH . '/models/DoGrade.php';
require_once APPLICATION_PATH . '/models/Sheduleexam.php';
require_once APPLICATION_PATH . '/models/Exam.php';
require_once LIBRARY_PATH . '/FormatDate.php';
require_once APPLICATION_PATH . '/models/Historyofusertest.php';
require_once APPLICATION_PATH . '/models/Subject.php';

class IndexController extends Zend_Controller_Action {

    public function init() {
        $objSessionNamespace = new Zend_Session_Namespace('Zend_Auth');
        $objSessionNamespace->setExpirationSeconds(86400);
        $controllerName = $this->_request->getControllerName();
        $actionName = $this->_request->getActionName();
        $param = $this->_request->getParams();
        $this->view->controllerName = $controllerName;
        $this->view->actionName = $actionName;
        $this->view->param = $param;
    }

    function preDispatch1() {
        $auth = Zend_Auth::getInstance();

        if (!$auth->hasIdentity()) {
            $this->view->haslogin = false;
            $this->_redirect('auth/login');
        } else {
            $this->view->haslogin = true;
            $this->view->userhaslogin = $auth->getStorage()->read();
        }
    }

    public function indexAction() {
        $this->preDispatch1();
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity())
            $userhaslogin = $auth->getStorage()->read();
        if ($userhaslogin->group_id == 2) { // Sinh viên
            $this->_helper->layout->setLayout('student');
            $this->view->headLink()->appendStylesheet(BASE_URL . '/css/student.css');
            $this->view->headLink()->appendStylesheet(BASE_URL . '/js/jquery-ui-1.8.9.custom/css/custom-theme/jquery-ui-1.8.9.custom.css');
        } elseif ($userhaslogin->group_id == 5 || $userhaslogin->group_id == 3) { // admin
            $this->_helper->layout->setLayout('admin');
            $this->view->headLink()->appendStylesheet(BASE_URL . '/css/teacher.css');
            $this->view->headLink()->appendStylesheet(BASE_URL . '/js/jquery-ui-1.8.9.custom/css/cupertino/jquery-ui-1.8.9.custom.css');
        } else {
            $this->_helper->layout->setLayout('admin');
            $this->view->headLink()->appendStylesheet(BASE_URL . '/css/teacher.css');
            $this->view->headLink()->appendStylesheet(BASE_URL . '/js/jquery-ui-1.8.9.custom/css/cupertino/jquery-ui-1.8.9.custom.css');
        }

        //$this->getHelper('viewRenderer')->setNoRender();
    }

    public function countdownAction() {
        $this->view->jQuery()->addJavascriptFile(BASE_URL . '/js/test.do.js');
        $now = date('U');
        $today = date('Y-m-d');
        $now = date('U');
        $request = $this->getRequest();
        $data = $request->getParams();
        $endtime = ($now + $data["duration"] * 60); // 2 phut		
    }

    public function timeAction() {
        $this->view->time = new DateTime();
        $this->_helper->layout->disableLayout();
    }

    public function introAction() {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            $this->_helper->layout->setLayout('guestviewcomment');
            $this->view->headLink()->appendStylesheet(BASE_URL . '/css/student.css');
        } else {
            $userhaslogin = $auth->getStorage()->read();
            if ($userhaslogin->group_id == 2) { // Sinh viên
                $this->_helper->layout->setLayout('student');
                $this->view->headLink()->appendStylesheet(BASE_URL . '/css/student.css');
                $this->view->headLink()->appendStylesheet(BASE_URL . '/js/jquery-ui-1.8.9.custom/css/custom-theme/jquery-ui-1.8.9.custom.css');
            } else {
                $this->_helper->layout->setLayout('admin');
                $this->view->headLink()->appendStylesheet(BASE_URL . '/css/teacher.css');
                $this->view->headLink()->appendStylesheet(BASE_URL . '/js/jquery-ui-1.8.9.custom/css/cupertino/jquery-ui-1.8.9.custom.css');
            }
        }


        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $data = $request->getParams();
            } elseif ($request->isGet()) {
                
            }
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }

    public function getstudenttimestartAction() {
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                // date("U"); Seconds since the Unix Epoch (January 1 1970 00:00:00 GMT)
                $dataResponse = array("success" => true, "timeStart" => date("Y-m-d G:i:s"), "timeStartCalculate" => date("U"));
                echo Zend_Json::encode($dataResponse);
                die();
            } elseif ($request->isGet()) {
                $dataResponse = array("error" => "Lỗi phương thức");
                echo Zend_Json::encode($dataResponse);
                die();
            }
        } catch (Exception $ex) {
            $dataResponse = array("error" => $ex->getMessage());
            echo Zend_Json::encode($dataResponse);
            die();
        }
    }

}
