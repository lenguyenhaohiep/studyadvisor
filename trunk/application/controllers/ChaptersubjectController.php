<?php

require_once APPLICATION_PATH . '/models/Chaptersubject.php';
require_once APPLICATION_PATH . '/models/Subject.php';
require_once APPLICATION_PATH . '/models/Question.php';
require_once VIEW_PATH . '/helpers/SltChapterSubject.php';
require_once 'Zend/Filter/StripTags.php';

class ChapterSubjectController extends Zend_Controller_Action {

    private $_chapter_subject;
    private $_arrError;

    public function init() {
        $objSessionNamespace = new Zend_Session_Namespace('Zend_Auth');
        $objSessionNamespace->setExpirationSeconds(86400);
        $this->view->controller = "chaptersubject";
        $this->_helper->layout->setLayout('admin');
        //$this->_helper->layout->setLayout('teacher');
        $this->view->headLink()->appendStylesheet(BASE_URL . '/css/teacher.css');

        $this->_chapter_subject = new Default_Models_ChapterSubject();

        $this->_cols_view = array("id", "stt", "name", "subject_id", "note",);

        // các cột hiển thị <tên sẽ hiển thị trong head của table>
        $this->_cols_view_title = array("id", "Thứ tự học", "Tên đầy đủ", "Môn học", "Ghi chú");
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

                $dataObj = $this->_chapter_subject->fetchAll($where, $order, $iDisplayLength, $iDisplayStart);
                $total = count($this->_chapter_subject->fetchAll($where));
                $json_data = new stdClass();
                $json_data->sEcho = $sEcho;
                $json_data->iTotalRecords = $total;
                $json_data->iTotalDisplayRecords = $total;
                $aaData = array();
                if (count($dataObj)) {
                    foreach ($dataObj as $dataItem) {
                        $dataItemArray = $dataItem->toArray();
                        $id = $dataItemArray["id"];
                        $tmpArr = array();
                        $modelsSubject = new Default_Models_Subject();
                        if (empty($dataItemArray["subject_id"]))
                            $dataItemArray["subject_id"] = "";
                        else
                            $modelsSubject->find("id", $dataItemArray["subject_id"]);
                        if ($modelsSubject->getId())
                            $dataItemArray["subject_id"] = $modelsSubject->getFull_name();

                        //$strAction    = '<a class="view-icon"   href="'. BASE_URL .'/'. $data["controller"] .'/view/id/'.  $id .'" onclick="viewClick(this.href); return false;"><img class="fugue fugue-eye" alt="" src="'. BASE_URL .'/img/icons/space.gif"/></a>&nbsp;&nbsp;';
                        $strAction = '<a class="edit-icon"   href="' . BASE_URL . '/' . $data["controller"] . '/edit/id/' . $id . '"><img class="fugue fugue-pencil" alt="Chỉnh sửa" src="' . BASE_URL . '/img/icons/space.gif"/></a>&nbsp;&nbsp;';
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
                        $this->_chapter_subject = new Default_Models_ChapterSubject();
                        $this->_chapter_subject->find("id", $arr_idItem);
                        if ($this->_chapter_subject->getId()) {
                            $modelQuestion = new Default_Models_Question();
                            $result = $modelQuestion->fetchAll("`chapter_id`='" . $arr_idItem . "'");
                            if (count($result) > 0)
                                throw new Exception("Chủ đề môn học đã có ngân hàng câu hỏi, bạn không thể xóa được.");
                            $this->_chapter_subject->delete('id', $arr_idItem);
                        }
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
        $this->view->controller = "chaptersubject";
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
                    $this->render("chaptersubject");
                } else {
                    $this->_chapter_subject = new Default_Models_ChapterSubject();
                    $this->_chapter_subject->setOptions($data);
                    if (empty($data['id']))
                        $this->_chapter_subject->setId(null);

                    $this->_chapter_subject->save();
                    $this->_redirect("/chaptersubject");
                }
            }elseif ($request->isGet()) {
                $data = $request->getParams();
                $id = $data['id'];
                if (empty($data['id']))
                    $this->_redirect("/chaptersubject");
                $result = $this->_chapter_subject->find('id', $id);
                if ($this->_chapter_subject->getId()) {
                    $Obj = $this->_chapter_subject->toArray();
                    $Obj['isupdate'] = 1;
                    $this->view->Obj = $Obj;
                    $this->render("chaptersubject");
                }
            } else {
                $this->_redirect("error/error");
            }
        } catch (Exception $ex) {
            
        }
    }

    public function addAction() {
        $this->view->action = "edit";
        $this->view->controller = "chaptersubject";
        $Obj = Array();
        $Obj['isupdate'] = 0;  // insert new 
        $Obj['id'] = null;
        $Obj['name'] = '';
        $Obj['subject_id'] = '';
        $Obj['note'] = '';
        $this->view->Obj = $Obj;
        $this->render("chaptersubject");
    }

    public function uploadAction() {
        if (isset($_POST['submit'])) {
            $pdfDirectory = "pdf/";
            $filename = basename($_FILES['pdf']['name'], ".pdf");
            $filename = preg_replace("/[^A-Za-z0-9_-]/", "", $filename) . ".pdf";


            if (move_uploaded_file($_FILES['pdf']['tmp_name'], $pdfDirectory . $filename)) {
                $pdfWithPath = $pdfDirectory . $filename;
                echo "Đã upload file ".$pdfWithPath;
                echo "<input type='hidden' name='pdfpath' id='pdfpath' value='".$filename."'/>";
            }
        } 
            echo '<form method = "post" action = "" enctype = "multipart/form-data">';
            echo '<input type = "file" name = "pdf" id="pdf" /><br/>';
            echo '<input type = "submit" name = "submit" value = "Upload" />';
            echo '</form>';
        
        die();
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
            }
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }

    public function ajaxgetchaptersubjectAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getParams();
            if ($request->isPost()) {
                $subject_id = $data["subject_id"];
                $models_chaptersubject = new Default_Models_ChapterSubject();
                $zend_view_helper_sltChapterSubject = new Zend_View_Helper_SltChapterSubject();
                $sltChapterSubject = $zend_view_helper_sltChapterSubject->SltChapterSubject("chapter_id", null, $subject_id);

                $dataResponse = array("success" => true, "sltChapterSubject" => $sltChapterSubject);
                echo Zend_Json::encode($dataResponse);
                die();
            } elseif ($request->isGet()) {
                $data_json = array("success" => false, "error" => "Lỗi phương thức");
            }
        } catch (Exception $ex) {
            $data_json = array("success" => false, "error" => "Lỗi " . $ex->getMessage());
        }
        echo Zend_Json::encode($data_json);
        die();
    }

    public function ajaxgetchaptersubjectobjAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getParams();
            if ($request->isPost()) {
                $chapter_id = $data["chapter_id"];
                $modelChapter = new Default_Models_ChapterSubject();
                $modelChapter->find("id", $chapter_id);
                $idchapter = $modelChapter->getId();
                if (empty($idchapter))
                    $chapter = "";
                else
                    $chapter = $modelChapter->toArray();
                $dataResponse = array("success" => true, "chapter" => $chapter);
                echo Zend_Json::encode($dataResponse);
                die();
            }elseif ($request->isGet()) {
                $data_json = array("success" => false, "error" => "Lỗi phương thức");
            }
        } catch (Exception $ex) {
            $data_json = array("success" => false, "error" => "Lỗi " . $ex->getMessage());
        }
        echo Zend_Json::encode($data_json);
        die();
    }

    public function _validate($data, $update = false) {
        $this->_arrError = Array();
        // CHECK MỘT SỐ TRƯỜNG NHẬP TRỐNG 
        if (empty($data['name']))
            $this->_arrError[] = "Tên chương môn học trống.";
        if (empty($data['subject_id']))
            $this->_arrError[] = "Chưa chọn môn học.";
        if (empty($data['path']))
            $this->_arrError[] = "Chưa có File PDF";
        if ($update) {// is update 
            if (empty($data['id']))
                $this->_arrError[] = "ID chương môn học không tồn tại.";
        }else {//is insert 
        }
    }

    private function _filter($data) {
        $data['name'] = Zend_Filter::filterStatic($data['name'], 'StringTrim');
        $data['subject_id'] = Zend_Filter::filterStatic($data['subject_id'], 'StringTrim');
        // filter cac script
        $filter = new Zend_Filter_StripTags();
        $data['name'] = $filter->filter($data['name']);
        $data['name'] = str_replace('\"', '"', $data['name']);
        $data['name'] = str_replace("\'", "'", $data['name']);


        return $data;
    }

}

