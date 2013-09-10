<?php

require_once APPLICATION_PATH . '/models/Classs.php';
require_once APPLICATION_PATH . '/models/Course.php';
require_once APPLICATION_PATH . '/models/User.php';
require_once APPLICATION_PATH . '/models/Test.php';
require_once APPLICATION_PATH . '/models/Subject.php';
require_once LIBRARY_PATH.'/FormatDate.php';
require_once APPLICATION_PATH . '/models/Classhasstudent.php';
require_once APPLICATION_PATH . '/models/Sheduleexam.php';
require_once APPLICATION_PATH . '/models/Exam.php';
require_once APPLICATION_PATH . '/models/Historyofusertest.php';
require_once APPLICATION_PATH . '/models/Teachassignment.php';


class ClasssController extends Zend_Controller_Action
{
	private $_classs;
	private $_arrError;
	
	
	public function init()
	{
        $objSessionNamespace = new Zend_Session_Namespace( 'Zend_Auth' );
		$objSessionNamespace->setExpirationSeconds( 86400 );
		$this->view->controller = "class";
		$this->_helper->layout->setLayout('admin');
		//$this->_helper->layout->setLayout('teacher');
		$this->view->headLink()->appendStylesheet(BASE_URL. '/css/teacher.css');		
		
		$this->_classs = new Default_Models_Classs();
		$this->_cols_view       = array("id","full_name","short_name","subject_id","course_id", "hidden"); 
				// các cột hiển thị <tên sẽ hiển thị trong head của table>
		$this->_cols_view_title = array("id","Tên lớp","Tên viết tăt","Học môn","Khóa học","Trạng thái");

		// Phục vụ việc giữ lại Cái menu đang active ở bên dưới - focus menu
  		$controllerName   = $this->_request->getControllerName();
  		$actionName		  = $this->_request->getActionName();
  		$param			  = $this->_request->getParams();
  		$this->view->controllerName   = $controllerName;
  		$this->view->actionName 	  = $actionName;
  		$this->view->param			  = $param;	  		
		
	}
	
	function preDispatch()
    {
        $auth = Zend_Auth::getInstance();
        
        if (!$auth->hasIdentity()) {
        	$this->view->haslogin = false;
            $this->_redirect('auth/login');
        }else
        {
        	$this->view->haslogin = true;
        	$this->view->userhaslogin = $auth->getStorage()->read();     	
        }
    }			
	
	
	public function serversideAction(){
		try {
			$request = $this->getRequest();
			$data    = $request->getParams();
			if ($request->isPost()) {
				
			}elseif($request->isGet()) {
				$iColumns        = $data['iColumns'];
				$iDisplayStart   = $data['iDisplayStart'];
				$iDisplayLength  = $data['iDisplayLength'];
				$sEcho			 = intval($data['sEcho']);
				$where_class_id		= $data['where_class_id'];
				// Order
				$order = array();
				$hasSort			= $data['iSortCol_0'];
                                $data['sSortDir_0'] = 'DESC';
				if(isset($hasSort)){
					$iSortingCols = $data['iSortingCols']; 
					$listSortColsIndex = array();
					$listSortColsDir   = array();
					for($i=0;$i<$iSortingCols;$i++){
						$iSortColIndex   =  $data["iSortCol_".$i];
						$iSortColDir	 =  $data["sSortDir_".$i];
						$iSortColName    =  $this->_cols_view[$iSortColIndex];
						$iSortColDir = ($iSortColDir=="" ? "ASC" : $iSortColDir);
						$order[].=$iSortColName.' '.$iSortColDir;
					}
				}
				// filter
				$where ='';
				$sSearch	= $data['sSearch'];
				if(!empty($sSearch)){
					foreach($this->_cols_view as $col_viewItem){
						if($col_viewItem!="id")
							$where.='`'.$col_viewItem.'` LIKE "%'.trim(addslashes($sSearch)).'%" OR ';
					}
					$where.="0 AND ";
				}				
				for($i=0;$i<=$iColumns-2;$i++){
					$search_col = $data["sSearch_".$i];
					if(!empty($search_col))
						$where.='`'.$this->_cols_view[$i].'` LIKE "%'.trim(addslashes($search_col)).'%" AND ' ;
				}
				$where.="1";
				if(!empty($where_class_id))
					$where .=' AND ('.$where_class_id.')'; 				
				
				$dataObj    = $this->_classs->fetchAll($where,$order,$iDisplayLength,$iDisplayStart);
				$total 		= count($this->_classs->fetchAll($where));
				$json_data  = new stdClass();
				$json_data->sEcho = $sEcho;
				$json_data->iTotalRecords = $total;
				$json_data->iTotalDisplayRecords = $total;
				$aaData = array();				
				if(count($dataObj)){
					foreach ($dataObj as $dataItem){
						$dataItemArray  = $dataItem->toArray();
						$id  			= $dataItemArray["id"];
						$tmpArr 		= array();
						$modelCourse = new Default_Models_Course();
						$modelCourse->find("id",$dataItemArray["course_id"]);
							$dataItemArray["course_id"] = $modelCourse->getFull_name();
						$modelsSubject = new Default_Models_Subject();
						if(empty($dataItemArray["subject_id"]))
							$dataItemArray["subject_id"] = "";
						else
							$modelsSubject->find("id",$dataItemArray["subject_id"]);
						if($modelsSubject->getId())
							$dataItemArray["subject_id"] = $modelsSubject->getFull_name();
						if($dataItemArray["hidden"] == "on")
							$dataItemArray["hidden"] = "Mở";
						else 
							$dataItemArray["hidden"] = "Đóng";
						$strAction    = '<a class="view-icon"   href="'. BASE_URL .'/'. $data["controller"] .'/viewstudent/id/'.  $id .'"><img class="fugue fugue-eye" alt="" src="'. BASE_URL .'/img/icons/space.gif"/></a>&nbsp;&nbsp;';
						$strAction   .= '<a class="edit-icon"   href="'. BASE_URL .'/'. $data["controller"] .'/edit/id/'.  $id .'"><img class="fugue fugue-pencil" alt="Chỉnh sửa" src="'. BASE_URL .'/img/icons/space.gif"/></a>&nbsp;&nbsp;';
						$strAction   .= '<a class="remove-icon" href="'. BASE_URL .'/'. $data["controller"] .'/delete/id/'.$id.'"  onclick="deleteClick(this,this.href); return false;"><img class="fugue fugue-cross"  alt="Xóa"       src="'. BASE_URL .'/img/icons/space.gif"/></a>'; 
						$tmpArr[] = $strAction;
						if(count($this->_cols_view))
						foreach ($this->_cols_view as $col_viewItem)
							if($col_viewItem!='id')
							 	$tmpArr[]  = $dataItemArray[$col_viewItem];
						// add two collum to action,check all
						$strCheck = '<input  class="check_row" type="checkbox" id="checkbox_row'.$id.'" onclick="return checkRow(this.id);"/>';
						$tmpArr[] = $strCheck;
						$aaData[] = $tmpArr;
					}
				}
				
				$json_data->aaData = $aaData;				
				$str_json = Zend_Json::encode($json_data);
				echo $str_json; 
				die();
			}
		}catch(Exception $ex){
				echo $ex->getMessage();
				die();			  
		}			
	}	

	public function viewstudentAction(){
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
				//$data = $request->getParams();
			}elseif($request->isGet()) {
				$data = $request->getParams();
				$class_id = $data['id'];
				$class_id = trim($class_id);
				if(empty($class_id))
					$this->_redirect("/classs");
					//throw new Exception("Không tồn tại id của lớp học");
				$modelClass = new Default_Models_Classs();
				$modelClass->find("id",$class_id);
				$class_id = $modelClass->getId();
				if(empty($class_id))	
					throw new Exception("Không tồn tại id của lớp học");
				$data = array()	;
					$models_classhasstudent = new Default_Models_ClassHasStudent();
					$where = "`class_id`='".$class_id."'" ;
					$result = $models_classhasstudent->fetchAll($where);
					if(count($result)>0){
					/*
				 	* Begin Table list user của class
				 	*/
						foreach($result as $key=>$resultItem){
							//$resultItem = new Default_Models_ClassHasStudent();
							$modelUser = new Default_Models_User();
							$modelUser->find("id",$resultItem->getUser_id());
							$userId = $modelUser->getId();
							if(!empty($userId)){
								$data[$key]['user_obj'] = $modelUser->toArray();
								/*
								$modelHisTestUser = new Default_Models_Historyofusertest();
								$where = "`class_id`='".$class_id."' AND `user_id`='".$userId."'";
								$modelHisTestUser->find("")
								*/
							}
						}
				/*
				 * End Table list user của class
				 */						
				/*
				 * Begin Table show lịch thi của lớp
				 */
				$modelSheduleExam = new Default_Models_SheduleExam();
				$modelExam 	      = new Default_Models_Exam();
				$modelsHisTest = new Default_Models_Historyofusertest();
				$where = "`class_id`='".$class_id."'";
				$result = $modelSheduleExam->fetchAll($where);
				$TableShowSheduleExamClass=array();
				if(count($result)>0)
				foreach($result as $key=>$resultItem){
					//$resultItem = new Default_Models_SheduleExam();
					$TableShowSheduleExamClass[$key]['shedule_exam'] = $resultItem->toArray();
					$where = "`shedule_exam_id`='".$resultItem->getId()."'";
					$resultHisTest =  $modelsHisTest->fetchAll($where);
					if(count($resultHisTest)>0){
						$TableShowSheduleExamClass[$key]['flagExamHasDone'] = 1;
					}
					$modelExam->find("id",$resultItem->getExam_id());
					$TableShowSheduleExamClass[$key]['exam'] = $modelExam->toArray();
				}
				/*
				 * END Table show lịch thi của lớp
				 */				
				$this->view->tableData = $TableShowSheduleExamClass;
				$this->view->class_obj = $modelClass->toArray();
				$this->view->StudentInClass = $data;
					}
			}
		}catch(Exception $ex){
			$this->_arrError[] = $ex->getMessage();  
			$this->view->arrError = $this->_arrError; 
		}
		
	}
	
	public function viewgradestudentAction(){
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
				//$data = $request->getParams();
			}elseif($request->isGet()) {
				$data = $request->getParams();
				$shedule_exam_id = $data['shedule_exam'];
				$shedule_exam_id = trim($shedule_exam_id);
				if(empty($shedule_exam_id))
					$this->_redirect("/classs");
					//throw new Exception("Không tồn tại lịch thi này của lớp học");
				$modelshedule_exam = new Default_Models_SheduleExam();
				$modelshedule_exam->find("id",$shedule_exam_id);
				$shedule_exam_id =  $modelshedule_exam->getId();
				if(empty($shedule_exam_id))
					throw new Exception("Không tồn tại lịch thi này của lớp học");
					
				// Begin kiểm tra xem đề thi có cập nhật độ phân cách, độ khó tự động không
					$list_test_id = $modelshedule_exam->getList_test_id();
					
					if(!empty($list_test_id))
					{
						$testId = explode(",",$list_test_id);
						$modelTest = new Default_Models_Test();
						$modelTest->find("id",$testId[0]);
						if($modelTest->getId())
							$this->view->auto_update_level = $modelTest->getAuto_update_level();
						
					}
				// End kiểm tra xem đề thi có cập nhật độ phân cách, độ khó tự động không
				// Kiểm tra xem đã cập nhật độ khó và độ phân cách chưa, nếu rồi thì không cho hiện nút cập nhật ở bên dưới nữa
				// Nếu cập nhật rồi thì = 1 
				if(($modelshedule_exam->getCount_update_level()*1)!=1)
					$this->view->flagDisableUpdateLevel = "enable";
				$modelExam = new Default_Models_Exam();
				$modelExam->find("id",$modelshedule_exam->getExam_id());
				$exam_id = $modelExam->getId() ;
				if(!empty($exam_id))
					$this->view->exam_obj = $modelExam->toArray(); 
					
				$class_id = $modelshedule_exam->getClass_id();
				$modelClass = new Default_Models_Classs();
				$modelClass->find("id",$class_id);
				$class_id = $modelClass->getId();
				if(empty($class_id))	
					throw new Exception("Không tồn tại id của lớp học");
				$data = array()	;
					$models_classhasstudent = new Default_Models_ClassHasStudent();
					$where = "`class_id`='".$class_id."'" ;
					$result = $models_classhasstudent->fetchAll($where);
					if(count($result)>0){
					/*
				 	* Begin Table list score user của class
				 	*/
						foreach($result as $key=>$resultItem){
							//$resultItem = new Default_Models_ClassHasStudent();
							$modelUser = new Default_Models_User();
							$modelUser->find("id",$resultItem->getUser_id());
							$userId = $modelUser->getId();
							if(!empty($userId)){
								$data[$key]['user_obj'] = $modelUser->toArray();
								
								$modelHisTestUser = new Default_Models_Historyofusertest();
								$where = "`shedule_exam_id`='".$shedule_exam_id."' AND `class_id`='".$class_id."' AND `user_id`='".$userId."'";
								$resultHisTest = $modelHisTestUser->fetchAll($where);
								if(count($resultHisTest)>0)
									$data[$key]['hist_test_obj'] = $resultHisTest[0]->toArray();
							}
						}
				/*
				 * End Table list score user của class
				 */						
				$this->view->class_obj = $modelClass->toArray();
				$this->view->StudentInClass = $data;
				$this->view->shedule_exam_id = $shedule_exam_id;
				
			}
			}
		}catch(Exception $ex){
			$this->_arrError[] = $ex->getMessage();  
			$this->view->arrError = $this->_arrError; 
		}
		
	}
	
	public function deleteAction(){
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
				$data = $request->getParams();
				$arr_id = $data['id'];
				if(!is_array($arr_id))
					$arr_id = array($arr_id);					
				if(count($arr_id))
				foreach($arr_id as $arr_idItem){
					$this->_classs = new Default_Models_Classs();
					$this->_classs->find("id",$arr_idItem);
					if($this->_classs->getId()){
						$modelClassHasStu = new Default_Models_ClassHasStudent();
						$result = $modelClassHasStu->fetchAll("`class_id`='".$arr_idItem."'");
						if(count($result)>0)
							throw new Exception("Lớp học có sinh viên tham gia, bạn không thể xóa.");
						$this->_classs->delete('id', $arr_idItem);
					}
					else
						throw new Exception("ID = ".$arr_idItem." not exists.");
				}
				$dataResponse = array("success"=>true);
				echo Zend_Json::encode($dataResponse);
				die();				
			}elseif($request->isGet()) {
				$dataResponse = array("error"=>"Lỗi phương thức");
				echo Zend_Json::encode($dataResponse);
				die();
			}
		}catch(Exception $ex){
			$dataResponse = array("error"=>$ex->getMessage());
			echo Zend_Json::encode($dataResponse);
			die();
		}		
	}
	
	public function editAction(){
		$this->view->action = "edit" ;
		$this->view->controller = "classs" ;
		
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
				
			// get all atrribute and validate
				$data = $request->getParams();
				$data = $this->_filter($data); 
				
				// validate data nếu có lỗi thì thông báo trả lại form 
				if($data['isupdate']==1)
					$this->_validate($data,true);
				else 
					$this->_validate($data,false);
					
				if(count($this->_arrError)>0){					
					$this->view->arrError = $this->_arrError;
					$this->view->Obj = $data;
					$this->render("classs");
				}else{
					$this->_classs  = new Default_Models_Classs();
					$this->_classs->setOptions($data);
					if(empty($data['id']))
						$this->_classs->setId(null);	
										
					$this->_classs->save();					
					$this->_redirect("/classs");
				}
				
			}elseif($request->isGet()) {
					$data = $request->getParams();
					$id = $data['id'];			
					if(empty($data['id']))
						$this->_redirect("/classs");					
					$result = $this->_classs->find('id', $id);
					if($this->_classs->getId()){
						$Obj = $this->_classs->toArray() ;
						$Obj['isupdate'] = 1;
						$this->view->Obj  = $Obj;
						$this->render("classs");	 
					}
				}else{
					$this->_redirect("error/error");
				}				
			
		}catch(Exception $ex){
			$this->_arrError[] = $ex->getMessage();  
			$this->view->arrError = $this->_arrError;
			$this->view->Obj = $data;
			$this->render("classs");
		}
	}
	
	public function addAction(){
		$this->view->action = "edit" ;
		$this->view->controller = "classs" ;
		$Obj  = Array();
		$Obj['isupdate'] = 0;  // insert new 
                $Obj['full_name']='';
                $Obj['short_name']='';
                $Obj['subject_id']='';
                $Obj['time_start']='';
                $Obj['time_end']='';
                $Obj['course_id']='';
                $Obj['time_start_register']='';
                $Obj['time_end_register']='';
                $Obj['max_user']='';
                $Obj['hidden']='';
                $Obj['note']='';
                $Obj['id']=null;
                
                
		$this->view->Obj  = $Obj;
		$this->render("classs");	 
	}
	
	public function indexAction()
	{
		$this->view->jQuery()->addJavascriptFile(BASE_URL . '/js/cms.js');
		$this->view->headLink()->appendStylesheet(BASE_URL . '/css/cms.css');
		
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
				$data = $request->getParams();
			}elseif($request->isGet()) {
				$this->view->cols_view_title = $this->_cols_view_title;
				$this->view->cols_view = $this->_cols_view;
			}
		}catch(Exception $ex){
			$this->_arrError[] = $ex->getMessage();  
			$this->view->arrError = $this->_arrError; 
		}
	}

	public function exportexcelscoreAction(){
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
				//$data = $request->getParams();
			}elseif($request->isGet()) {
				$this->_helper->layout->disableLayout();
				$data = $request->getParams();
				$class_id = $data['class_id'];
				$class_id = trim($class_id);
				$modelClass = new Default_Models_Classs();
				$modelClass->find("id",$class_id);
				$class_id = $modelClass->getId();
				$data = array()	;
					$models_classhasstudent = new Default_Models_ClassHasStudent();
					$where = "`class_id`='".$class_id."'" ;
					$result = $models_classhasstudent->fetchAll($where);
					if(count($result)>0){
					/*
				 	* Begin Table list user của class
				 	*/
						foreach($result as $key=>$resultItem){
							//$resultItem = new Default_Models_ClassHasStudent();
							$modelUser = new Default_Models_User();
							$modelUser->find("id",$resultItem->getUser_id());
							$userId = $modelUser->getId();
							if(!empty($userId)){
								$data[$key]['user_obj'] = $modelUser->toArray();
								/*
								$modelHisTestUser = new Default_Models_Historyofusertest();
								$where = "`class_id`='".$class_id."' AND `user_id`='".$userId."'";
								$modelHisTestUser->find("")
								*/
							}
						}
				/*
				 * End Table list user của class
				 */						
				$this->view->tableData = $TableShowSheduleExamClass;
				$this->view->class_obj = $modelClass->toArray();
				$this->view->StudentInClass = $data;
				}
			}
		}catch(Exception $ex){
			$this->_arrError[] = $ex->getMessage();  
			$this->view->arrError = $this->_arrError; 
		}
	}
	
	public function exportwordscoreAction(){
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
				//$data = $request->getParams();
			}elseif($request->isGet()) {
				$this->_helper->layout->disableLayout();
				$data = $request->getParams();
				$class_id = $data['class_id'];
				$class_id = trim($class_id);
				$modelClass = new Default_Models_Classs();
				$modelClass->find("id",$class_id);
				$class_id = $modelClass->getId();
				$data = array()	;
					$models_classhasstudent = new Default_Models_ClassHasStudent();
					$where = "`class_id`='".$class_id."'" ;
					$result = $models_classhasstudent->fetchAll($where);
					if(count($result)>0){
					/*
				 	* Begin Table list user của class
				 	*/
						foreach($result as $key=>$resultItem){
							//$resultItem = new Default_Models_ClassHasStudent();
							$modelUser = new Default_Models_User();
							$modelUser->find("id",$resultItem->getUser_id());
							$userId = $modelUser->getId();
							if(!empty($userId)){
								$data[$key]['user_obj'] = $modelUser->toArray();
								/*
								$modelHisTestUser = new Default_Models_Historyofusertest();
								$where = "`class_id`='".$class_id."' AND `user_id`='".$userId."'";
								$modelHisTestUser->find("")
								*/
							}
						}
				/*
				 * End Table list user của class
				 */						
				$this->view->tableData = $TableShowSheduleExamClass;
				$this->view->class_obj = $modelClass->toArray();
				$this->view->StudentInClass = $data;
				}
			}
		}catch(Exception $ex){
			$this->_arrError[] = $ex->getMessage();  
			$this->view->arrError = $this->_arrError; 
		}
	}	
	
	public function _validate($data,$update=false){
		$this->_arrError = Array();
			// CHECK MỘT SỐ TRƯỜNG NHẬP TRỐNG 
			if(empty($data['full_name']))  				$this->_arrError[] = "Tên lớp học trống.";
			if(empty($data['short_name'])) 				$this->_arrError[] = "Tên viết tắt trống.";
			//if(empty($data['course_id'])) 				$this->_arrError[] = "Chưa chọn khóa học.";
			if(empty($data['time_start'])) 				$this->_arrError[] = "Thời gian bắt đầu lớp học trống.";
			if(empty($data['time_end'])) 				$this->_arrError[] = "Thời gian kết thúc lớp học trống.";
			if(empty($data['time_start_register'])) 	$this->_arrError[] = "Thời gian đăng ký lớp học trống.";
			if(empty($data['time_end_register'])) 		$this->_arrError[] = "Thời gian kết thúc đăng ký lớp học trống.";
		
			if(strtotime($data['time_start'])>strtotime($data['time_end']))
				$this->_arrError[] = "Thời gian kết thúc lớp học phải lớn hơn thời bắt đầu lớp học.";
			if(strtotime($data['time_start_register'])>strtotime($data['time_end_register']))
				$this->_arrError[] = "Thời gian kết thúc đăng ký lớp học phải lớn hơn thời bắt đầu đăng ký lớp học.";
			if(strtotime($data['time_end'])<strtotime($data['time_end_register']))
				$this->_arrError[] = "Thời gian kết thúc đăng ký lớp học phải lớn hơn thời gian kết thúc lớp học.";
				
			$modelCourse = new Default_Models_Course();	
			$modelCourse->find("id",$data['course_id']);
			if($modelCourse->getId()){
				if(strtotime($data['time_start'])<strtotime($modelCourse->getTime_start()) || strtotime($data['time_start'])>strtotime($modelCourse->getTime_end()))
					$this->_arrError[] = "Thời gian bắt đầu lớp học phải nằm trong thời gian của khóa học: <b> từ ".Zend_View_Helper_FormatDate::convertYmdToMdyJustDate($modelCourse->getTime_start())." đến ".Zend_View_Helper_FormatDate::convertYmdToMdyJustDate($modelCourse->getTime_end())."</b>";
				if(strtotime($data['time_end'])<strtotime($modelCourse->getTime_start()) || strtotime($data['time_end'])>strtotime($modelCourse->getTime_end()))
					$this->_arrError[] = "Thời gian kết thúc lớp học phải nằm trong thời gian của khóa học: <b> từ ".Zend_View_Helper_FormatDate::convertYmdToMdyJustDate($modelCourse->getTime_start())." đến ".Zend_View_Helper_FormatDate::convertYmdToMdyJustDate($modelCourse->getTime_end())."</b>";
					
			}	
				
				
		if($update){// is update 
			if(empty($data['id'])) 					$this->_arrError[] = "ID chương lớp học không tồn tại.";			
				
		}else{//is insert 
		}
	}	
	
	private function _filter($data) {
		try{
				$data['full_name'] 					= Zend_Filter::filterStatic($data['full_name'], 'StringTrim');
				$data['short_name'] 				= Zend_Filter::filterStatic($data['short_name'], 'StringTrim');
				$data['time_start']					= Zend_View_Helper_FormatDate::convertMdyToYmd($data['time_start']);
				$data['time_end']					= Zend_View_Helper_FormatDate::convertMdyToYmd($data['time_end']);
				$data['time_start_register']		= Zend_View_Helper_FormatDate::convertMdyToYmd($data['time_start_register']);
				$data['time_end_register']			= Zend_View_Helper_FormatDate::convertMdyToYmd($data['time_end_register']);
				if(empty($data['hidden']))
					$data['hidden'] = 'off';
				if(($data['max_user'])==0)
					$data['max_user'] = '1';	
					
			// filter cac script
        	$filter 	= new Zend_Filter_StripTags();
        	$data['full_name'] = $filter->filter($data['full_name']); 
        	$data['short_name'] = $filter->filter($data['short_name']);
					
		$data['full_name'] = str_replace('\"', '"',$data['full_name']);
		$data['full_name'] = str_replace("\'", "'",$data['full_name']);
		$data['short_name'] = str_replace('\"', '"',$data['short_name']);
		$data['short_name'] = str_replace("\'", "'",$data['short_name']);
		
			return $data;
		}catch (Exception $ex){
			throw new Exception($ex->getMessage());
		}
	}	
	
	
        function getUserId(){
		$auth = Zend_Auth::getInstance();
		if (!$auth->hasIdentity()) {
        	$this->view->haslogin = false;
            $this->_redirect('auth/login');
        }else
        {
        	$userhaslogin = $auth->getStorage()->read();
        	return $userhaslogin->id;	
        }		
	}	
	


	
} 