<?php
require_once APPLICATION_PATH . '/models/Classhasstudent.php';
require_once APPLICATION_PATH . '/models/Classs.php';
require_once APPLICATION_PATH . '/models/Course.php';
require_once APPLICATION_PATH . '/models/Comment.php';
require_once APPLICATION_PATH . '/models/User.php';
require_once LIBRARY_PATH . '/FormatDate.php';
require_once 'Zend/Filter/StripTags.php';

class CommentController extends Zend_Controller_Action {

    private $_comment;
    private $_arrError;
    private $_itemFirstPage = 15;

    public function init() {
        $objSessionNamespace = new Zend_Session_Namespace('Zend_Auth');
        $objSessionNamespace->setExpirationSeconds(86400);
                $this->_comment = new Default_Models_Comment();

        // Phục vụ việc giữ lại Cái menu đang active ở bên dưới - focus menu
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

    public function deleteAction() {
        $this->preDispatch1();
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $data = $request->getParams();
                $id = $data['id'];
                if (empty($id))
                    throw new Exception("ID không tồn tại.");
                $this->_comment->find("id", $id);
                if ($this->_comment->getId())
                    $this->_comment->delete('id', $id);
                else
                    throw new Exception("ID = " . $id . " không tồn tại.");
                $dataResponse = array("success" => true);
                echo Zend_Json::encode($dataResponse);
                die();
            }elseif ($request->isGet()) {
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

    public function addAction() {
        $this->preDispatch1();
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                // get all atrribute and validate
                $data = $request->getParams();
                if (empty($data['content']))
                    throw new Exception("Chưa nhập nội dung.");
                // validate data nếu có lỗi thì thông báo trả lại form 
                if (strlen($data['content']) > 6000)
                    throw new Exception("Chỉ được nhập tối đa 6000 ký tự.Bạn đã nhập hơn.");


                $this->_comment = new Default_Models_Comment();
                $auth = Zend_Auth::getInstance();
                if ($auth->hasIdentity()) {
                    $userhaslogin = $auth->getStorage()->read();
                    $data['user_id'] = $userhaslogin->id;
                }
                // Check ko cho user nhập nhiều SPAM
                $this->_comment = new Default_Models_Comment();
                $resultCm = $this->_comment->fetchAll("`user_id`=" . $userhaslogin->id, " time_create desc");
                if (count($resultCm) > 0)
                    foreach ($resultCm as $resultCmItem) {
                        $timePost = $resultCmItem->getTime_create();
                        $interval = date("U") - $timePost;
                        if ($interval < 15)
                            throw new Exception("Sau 15 giây bạn mới viết bình luận tiếp.");
                        break;
                    }

                $data = $this->_filter($data);
                $data['time_create'] = date("U");
                $this->_comment->setOptions($data);
                if (empty($data['id']))
                    $this->_comment->setId(null);
                $last_id_insert = $this->_comment->save();
                $modelComment = new Default_Models_Comment();
                $modelComment->find("id", $last_id_insert);
                $modelUser = new Default_Models_User();
                $modelUser->find("id", $data['user_id']);
                $time = Zend_View_Helper_FormatDate::timeBetweenNotGetSecond($modelComment->getTime_create(), date("U"));

                $dataResponse = array("success" => true, "data" => $modelComment->toArray(), "user" => $modelUser->toArray(), "time" => $time);
                echo Zend_Json::encode($dataResponse);
                die();
            }elseif ($request->isGet()) {
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

    public function getmorecommentAction() {

        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                // get all atrribute and validate
                $data = $request->getParams();
                $arrComment = array();
                $itemPerPage = 10;

                $page = trim($data['page']);

                if (!is_numeric($page))
                    $page = -1;
                $page = intval($page);
                // Kiểm tra nếu có chỉnh sửa số trang <0 thì trả xuống view luôn
                if ($page < 0) {
                    $page++;
                    $dataResponse = array("success" => true, "data" => $arrComment, "page" => $page, "notGetMore" => $notGetMore);
                } else {
                    $count = ($this->_itemFirstPage + ($page * $itemPerPage));
                    $this->_comment = new Default_Models_Comment();
                    //$result = $this->_comment->fetchAll();
                    //if(count($result)<=$count)
                    //$count = count($result)-1; 
                    $result = $this->_comment->fetchAll(null, "time_create desc", $itemPerPage, $count);

                    if (count($result) > 0)
                        foreach ($result as $key => $resultItem) {
                            $modelUser = new Default_Models_User();
                            $modelUser->find("id", $resultItem->getUser_id());
                            $arrComment[$key] = $resultItem->toArray();
                            $arrComment[$key]['time_create'] = Zend_View_Helper_FormatDate::timeBetweenNotGetSecond($resultItem->getTime_create(), date("U"));
                            if ($modelUser->getId())
                                $arrComment[$key]['user_id'] = $modelUser->getFirstname() . " " . $modelUser->getLastname();
                            $auth = Zend_Auth::getInstance();
                            if ($auth->hasIdentity())
                                $userhaslogin = $auth->getStorage()->read();
                            if ($userhaslogin->id == $resultItem->getUser_id() || $userhaslogin->group_id == 5)
                                $arrComment[$key]['hasDelete'] = 1;
                        }
                    $notGetMore = 0;
                    if (count($arrComment) < $itemPerPage)
                        $notGetMore = 1;
                    $page++;
                    $dataResponse = array("success" => true, "data" => $arrComment, "page" => $page, "notGetMore" => $notGetMore);
                }
                echo Zend_Json::encode($dataResponse);
                die();
            }elseif ($request->isGet()) {
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

    public function indexAction() {
        $this->view->jQuery()->addJavascriptFile(BASE_URL . '/js/cms.js');
        $this->view->headLink()->appendStylesheet(BASE_URL . '/css/cms.css');

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
                
            } elseif ($request->isGet()) {
                $modelComment = new Default_Models_Comment();
                $result = $modelComment->fetchAll(null, " time_create desc", $this->_itemFirstPage, 0);
                $data = array();
                if (count($result) > 0)
                    foreach ($result as $resultItem)
                        $data[] = $resultItem->toArray();
                $this->view->data = $data;
                $this->view->itemFirstPage = $this->_itemFirstPage;
            }
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }

    public function _validate($data, $update = false) {
        $this->_arrError = Array();
        // CHECK MỘT SỐ TRƯỜNG NHẬP TRỐNG 
        //if(strlen($data['content']) > 5000)		$this->_arrError[] = "Chỉ được nhập tối đa 5000 ký tự.Bạn đã nhập hơn.";
    }

    private function _filter($data) {
        $data['content'] = Zend_Filter::filterStatic($data['content'], 'StringTrim');
        $data['content'] = str_replace('\"', '"', $data['content']);
        $data['content'] = str_replace("\'", '"', $data['content']);
        // filter cac script
        $filter = new Zend_Filter_StripTags();
        $data['content'] = $filter->filter($data['content']);

        return $data;
    }

}

