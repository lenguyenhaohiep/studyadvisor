<?php

require_once LIBRARY_PATH . '/FormatDate.php';
require_once 'Zend/Filter/StripTags.php';
require_once APPLICATION_PATH . '/models/User.php';

class UserController extends Zend_Controller_Action {

    private $_user;
    private $_arrError;

    public function init() {
        $objSessionNamespace = new Zend_Session_Namespace('Zend_Auth');
        $objSessionNamespace->setExpirationSeconds(86400);
        $this->view->controller = "user";
        $this->_helper->layout->setLayout('admin');
        //$this->_helper->layout->setLayout('teacher');
        $this->view->headLink()->appendStylesheet(BASE_URL . '/css/teacher.css');
        $this->_user = new Default_Models_User();
        //$this->view->jQuery()->addJavascriptFile(BASE_URL . '/js/controller/test/test.js');
        $this->_cols_view = array("id", "username", "group_id", "user_code");
        // các cột hiển thị <tên sẽ hiển thị trong head của table>
        $this->_cols_view_title = array("id", "Tên đăng nhập", "Nhóm", "Mã số");

        // Phục vụ việc giữ lại Cái menu đang active ở bên dưới - focus menu
        $controllerName = $this->_request->getControllerName();
        $actionName = $this->_request->getActionName();
        $param = $this->_request->getParams();
        $this->view->controllerName = $controllerName;
        $this->view->actionName = $actionName;
        $this->view->param = $param;
    }

    function preDispatch() {
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
        $this->view->jQuery()->addJavascriptFile(BASE_URL . '/js/cms.js');
        $this->view->headLink()->appendStylesheet(BASE_URL . '/css/cms.css');

        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $data = $request->getParams();
            } elseif ($request->isGet()) {
                $this->view->cols_view_title = $this->_cols_view_title;
                $this->view->cols_view = $this->_cols_view;
            }
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }

    public function serversideAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getParams();
            if ($request->isPost()) {
                
            } elseif ($request->isGet()) {
                $iColumns = $data['iColumns'];
                $iDisplayStart = $data['iDisplayStart'];
                $iDisplayLength = $data['iDisplayLength'];
                $sEcho = intval($data['sEcho']);
                // Order
                $order = array();
                $hasSort = $data['iSortCol_0'];
                $data['sSortDir_0'] = 'DESC';
                if (isset($hasSort)) {
                    $iSortingCols = $data['iSortingCols'];
                    $listSortColsIndex = array();
                    $listSortColsDir = array();
                    for ($i = 0; $i < $iSortingCols; $i++) {
                        $iSortColIndex = $data["iSortCol_" . $i];
                        $iSortColDir = $data["sSortDir_" . $i];
                        $iSortColName = $this->_cols_view[$iSortColIndex];
                        $iSortColDir = ($iSortColDir == "" ? "ASC" : $iSortColDir);
                        $order[].=$iSortColName . ' ' . $iSortColDir;
                    }
                }
                // filter
                $where = '';
                $sSearch = $data['sSearch'];
                if (!empty($sSearch)) {
                    foreach ($this->_cols_view as $col_viewItem) {
                        if ($col_viewItem != "id")
                            $where.='`' . $col_viewItem . '` LIKE "%' . trim(addslashes($sSearch)) . '%" OR ';
                    }
                    $where.="0 AND ";
                }
                for ($i = 0; $i <= $iColumns - 2; $i++) {
                    $search_col = $data["sSearch_" . $i];
                    if (!empty($search_col))
                        $where.='`' . $this->_cols_view[$i] . '` LIKE "%' . trim(addslashes($search_col)) . '%" AND ';
                }
                $where.="1";


                $dataObj = $this->_user->fetchAll($where, $order, $iDisplayLength, $iDisplayStart);
                $total = count($this->_user->fetchAll($where));
                $json_data = new stdClass();
                $json_data->sEcho = $sEcho;
                $json_data->iTotalRecords = $total;
                $json_data->iTotalDisplayRecords = $total;
                $aaData = array();
                if (count($dataObj)) {
                    foreach ($dataObj as $dataItem) {
                        $dataItemArray = $dataItem->toArray();
                        $id = $dataItemArray["id"];
                        $modelsGroupUser = new Default_Models_GroupUser();
                        if (empty($dataItemArray["group_id"]))
                            $dataItemArray["group_id"] = "";
                        else
                            $modelsGroupUser->find("id", $dataItemArray["group_id"]);
                        if ($modelsGroupUser->getId())
                            $dataItemArray["group_id"] = $modelsGroupUser->getGroup_name();

                        //$dataItemArray["date_create"] = Zend_View_Helper_FormatDate::convertYmdToMdy($dataItemArray["date_create"]);
                        $tmpArr = array();
                        $strAction = '<a class="view-icon"   href="' . BASE_URL . '/' . $data["controller"] . '/view/id/' . $id . '" onclick="viewClick(this.href); return false;"><img class="fugue fugue-eye" alt="" src="' . BASE_URL . '/img/icons/space.gif"/></a>&nbsp;&nbsp;';
                        $strAction .= '<a class="edit-icon"   href="' . BASE_URL . '/' . $data["controller"] . '/edit/id/' . $id . '"><img class="fugue fugue-pencil" alt="Chỉnh sửa" src="' . BASE_URL . '/img/icons/space.gif"/></a>&nbsp;&nbsp;';
                        $strAction .= '<a class="remove-icon" href="' . BASE_URL . '/' . $data["controller"] . '/delete/id/' . $id . '"  onclick="deleteClick(this,this.href); return false;"><img class="fugue fugue-cross"  alt="Xóa"       src="' . BASE_URL . '/img/icons/space.gif"/></a>';
                        $tmpArr[] = $strAction;
                        if (count($this->_cols_view))
                            foreach ($this->_cols_view as $col_viewItem)
                                if ($col_viewItem != 'id')
                                    $tmpArr[] = $dataItemArray[$col_viewItem];
                        // add two collum to action,check all
                        $strCheck = '<input  class="check_row" type="checkbox" id="checkbox_row' . $id . '" onclick="return checkRow(this.id);"/>';
                        $tmpArr[] = $strCheck;
                        $aaData[] = $tmpArr;
                    }
                }
                $json_data->aaData = $aaData;
                $str_json = Zend_Json::encode($json_data);
                echo $str_json;
                die();
            }
        } catch (Exception $ex) {
            echo $ex->getMessage();
            die();
        }
    }

    public function deleteAction() {
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $data = $request->getParams();
                $arr_id = $data['id'];
                if (!is_array($arr_id))
                    $arr_id = array($arr_id);
                if (count($arr_id))
                    foreach ($arr_id as $arr_idItem) {
                        $this->_user->find("id", $arr_idItem);
                        if ($this->_user->getId())
                            $this->_user->delete('id', $arr_idItem);
                        else
                            throw new Exception("ID = " . $arr_idItem . " not exists.");
                    }
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

    public function editAction() {
        $this->view->action = "edit";
        $this->view->controller = "user";
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                // get all atrribute and validate
                $data = $request->getParams();
                $data = $this->_filter($data);

                // validate data nếu có lỗi thì thông báo trả lại form 
                if ($data['isupdate'] == 1)
                    $this->_validate($data, true);
                else
                    $this->_validate($data, false);

                if (count($this->_arrError) > 0) {
                    $this->view->arrError = $this->_arrError;
                    $this->view->Obj = $data;
                    $this->render("user");
                } else {
                    $this->_user = new Default_Models_User();
                    $data['date_create'] = date("Y-m-d G:i:s");
                    $this->_user->setOptions($data);
                    if (empty($data['id']))
                        $this->_user->setId(null);

                    $this->_user->save();
                    $this->_redirect("/user");
                }
            }elseif ($request->isGet()) {
                $data = $request->getParams();
                $id = $data['id'];
                if (empty($data['id']))
                    $this->_redirect("/user");
                $result = $this->_user->find('id', $id);
                if ($this->_user->getId()) {
                    $Obj = $this->_user->toArray();
                    $Obj['isupdate'] = 1;
                    $this->view->Obj = $Obj;
                    $this->render("user");
                }
            } else {
                $this->_redirect("error/error");
            }
        } catch (Exception $ex) {
            echo "Lỗi hoặc phương thức sai" . $ex->getMessage();
            die();
        }
    }

    public function addAction() {
        $this->view->action = "edit";
        $this->view->controller = "user";
        $Obj = Array();
        $Obj['isupdate'] = 0;  // insert new 
        $Obj['id'] = null;

        $Obj['username'] = '';
        $Obj['group_id'] = '';
        $Obj['password'] = '';
        $Obj['user_code'] = '';
        $Obj['firstname'] = '';
        $Obj['lastname'] = '';
        $Obj['email'] = '';
        $Obj['yahoo'] = '';
        $Obj['skyper'] = '';
        $Obj['phone'] = '';
        $Obj['department'] = '';
        $Obj['address'] = '';
        $Obj['city'] = '';
        $Obj['isblock'] = '';

        $this->view->Obj = $Obj;
        $this->render("user");
    }

    public function _validate($data, $update = false) {
        $this->_arrError = Array();
        // CHECK MỘT SỐ TRƯỜNG NHẬP TRỐNG 
        if (empty($data['username']))
            $this->_arrError[] = "Tài khoảng đăng nhập trống.";
        if (empty($data['password']))
            $this->_arrError[] = "Mật khẩu trống.";
        if (empty($data['group_id']))
            $this->_arrError[] = "Chưa chọn nhóm người dùng.";
        if (empty($data['firstname']))
            $this->_arrError[] = "Tên của người dùng trống.";
        if (empty($data['lastname']))
            $this->_arrError[] = "Tên của người dùng trống.";
        if (!Zend_Validate::is($data['email'], 'EmailAddress'))
            $this->_arrError[] = "Email không hợp lệ.";
        /*
          if(empty($data['time_start'])) 				$this->_arrError[] = "Thời gian bắt đầu lớp học trống.";
          if(empty($data['time_end'])) 				$this->_arrError[] = "Thời gian kết thúc lớp học trống.";
         */
        if (strlen($data['password']) < 5)
            $this->_arrError[] = "Mật khẩu phải từ 5 ký tự trở lên.";
        if (strlen($data['username']) < 5)
            $this->_arrError[] = "Tên tài khoản phải từ 5 ký tự trở lên.";
        $modelUser = new Default_Models_User();
        $where = "`user_code`='" . $data['user_code'] . "'";
        $result = $modelUser->fetchAll($where);
        if (!empty($data['id'])) {
            if (count($result) > 0) {
                if ($data['id'] != $result[0]->getId())
                    $this->_arrError[] = "Mã số sinh viên này đã được đăng ký. Bạn không thể dùng mã số này được";
            }
        }else {
            if (count($result) > 0) {
                $this->_arrError[] = "Mã số sinh viên này đã được đăng ký. Bạn không thể dùng mã số này được";
            }
        }
        $where = " `username`='" . $data['username'] . "'";
        $result = $modelUser->fetchAll($where);
        if (!empty($data['id'])) {
            if (count($result) > 0) {
                if ($data['id'] != $result[0]->getId())
                    $this->_arrError[] = "Tài khoản đăng nhập đã tồn tại.";
            }
        }else {
            if (count($result) > 0) {
                $this->_arrError[] = "Tài khoản đăng nhập đã tồn tại.";
            }
        }

        if ($update) {// is update 
            if (empty($data['id']))
                $this->_arrError[] = "ID người dùng không tồn tại.";
        }else {//is insert 
        }
    }

    private function _filter($data) {

        $data['username'] = Zend_Filter::filterStatic($data['username'], 'StringTrim');
        $data['password'] = Zend_Filter::filterStatic($data['password'], 'StringTrim');
        $data['firstname'] = Zend_Filter::filterStatic($data['firstname'], 'StringTrim');
        $data['lastname'] = Zend_Filter::filterStatic($data['lastname'], 'StringTrim');
        if (empty($data['isblock']))
            $data['isblock'] = 0;
        else
            $data['isblock'] = 1;
        // filter cac script
        $filter = new Zend_Filter_StripTags();
        $data['username'] = $filter->filter($data['username']);
        $data['firstname'] = $filter->filter($data['firstname']);
        $data['lastname'] = $filter->filter($data['lastname']);
        $data['department'] = $filter->filter($data['department']);
        $data['email'] = $filter->filter($data['email']);
        $data['yahoo'] = $filter->filter($data['yahoo']);

        $data['username'] = str_replace('\"', '"', $data['username']);
        $data['username'] = str_replace("\'", "'", $data['username']);
        $data['firstname'] = str_replace('\"', '"', $data['firstname']);
        $data['firstname'] = str_replace("\'", "'", $data['firstname']);

        $data['department'] = str_replace('\"', '"', $data['department']);
        $data['department'] = str_replace("\'", "'", $data['department']);

        $data['yahoo'] = str_replace('\"', '"', $data['yahoo']);
        $data['yahoo'] = str_replace("\'", "'", $data['yahoo']);


        return $data;
    }

}

