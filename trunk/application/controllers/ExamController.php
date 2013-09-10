<?php

require_once APPLICATION_PATH . '/models/Exam.php';
require_once APPLICATION_PATH . '/models/Course.php';
require_once APPLICATION_PATH . '/models/Sheduleexam.php';
require_once VIEW_PATH . '/helpers/SltClass.php';
require_once VIEW_PATH . '/helpers/SltExam.php';
require_once VIEW_PATH . '/helpers/SltClassSubject.php';
require_once LIBRARY_PATH.		'/FormatDate.php';
require_once 'Zend/Filter/StripTags.php';

class ExamController extends Zend_Controller_Action
{
	private $_exam;
	private $_arrError;
	
	
	public function init()
	{
        $objSessionNamespace = new Zend_Session_Namespace( 'Zend_Auth' );
		$objSessionNamespace->setExpirationSeconds( 86400 );
		
		$this->view->controller = "exam";
		$this->_helper->layout->setLayout('admin');
		//$this->_helper->layout->setLayout('teacher');
		$this->view->headLink()->appendStylesheet(BASE_URL. '/css/teacher.css');		
		$this->_exam 	 = new Default_Models_Exam();

		$this->_cols_view       = array("id","full_name","short_name","summary","hidden"); 
				// các cột hiển thị <tên sẽ hiển thị trong head của table>
		$this->_cols_view_title = array("id","Tên đầy đủ","Tên viết tăt","Mô tả","Tình trạng");

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
				
				$dataObj    = $this->_exam->fetchAll($where,$order,$iDisplayLength,$iDisplayStart);
				$total 		= count($this->_exam->fetchAll($where));
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
						if($dataItemArray["hidden"] == "on")
							$dataItemArray["hidden"] = "Mở";
						else 
							$dataItemArray["hidden"] = "Đóng";
						$strAction    = '<a class="view-icon"   href="'. BASE_URL .'/'. $data["controller"] .'/view/id/'.  $id .'" onclick="viewClick(this.href); return false;"><img class="fugue fugue-eye" alt="" src="'. BASE_URL .'/img/icons/space.gif"/></a>&nbsp;&nbsp;';
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
					$this->_exam->find("id",$arr_idItem);
					if($this->_exam->getId()){
						$modelSheduleExam = new Default_Models_SheduleExam();
						$result = $modelSheduleExam->fetchAll("`exam_id`='".$arr_idItem."'");
						if(count($result)>0)
							throw new Exception("Kỳ thi này đã được lập lịch thi, bạn không thể xóa.");
						$this->_exam->delete('id', $arr_idItem);
					}
					else
						throw new Exception("ID = ".$arr_idItem." không tồn tại.");
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
		$this->view->controller = "exam" ;
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
					$this->render("exam");
				}else{
					$this->_exam  = new Default_Models_Exam() ;
					$this->_exam->setOptions($data);
					if(empty($data['id']))
						$this->_exam->setId(null);	
										
					$this->_exam->save();					
					$this->_redirect("/exam");
				}
				
			}elseif($request->isGet()) {
					$data = $request->getParams();
					$id = $data['id'];		
					if(empty($data['id']))
						$this->_redirect("/exam");						
					$result = $this->_exam->find('id', $id);
					if($this->_exam->getId()){
						$Obj = $this->_exam->toArray() ;
						$Obj['isupdate'] = 1;
						$this->view->Obj  = $Obj;
						$this->render("exam");	 
					}
				}else{
					$this->_redirect("error/error");
				}				
			
		}catch(Exception $ex){
		}
	}
	
	public function addAction(){
		$this->view->action = "edit" ;
		$this->view->controller = "exam" ;
		$Obj  = Array();
		$Obj['isupdate'] = 0;  // insert new 
                $Obj['id']=null;
                $Obj['course_id']='';
                $Obj['full_name']='';
                $Obj['short_name']='';
                $Obj['summary']='';
                $Obj['hidden']='';
                $Obj['note']='';                
		$this->view->Obj  = $Obj;
		$this->render("exam");	 
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
			}
		}catch(Exception $ex){
			echo $ex->getMessage();  
		}
	}

	public function getinfocourseAction(){
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
				$data = $request->getParams();
				$course_id = $data['course_id'];
				$modelCourse = new Default_Models_Course();
				$modelCourse->find("id",$course_id);
				$courseID = $modelCourse->getId();
				if(empty($courseID))
					throw new Exception("Khóa học có ID  = ".$courseID." không tồn tại.");
				/*
				$modelExam = new Default_Models_Exam();
				$modelClass = new Default_Models_Classs();
				// Chỉ lấy những khóa học đang mở
				// Chỉ lấy những lớp học đang mở
				// Tất cả các lớp và khóa học bị đóng ta không hiện lên trên giao diện.
				$where = "`course_id`='".$course_id."'";
				$resultExam = $modelExam->fetchAll($where);
				$arrExamObj = array();
				if(count($resultExam)>0)
					foreach($resultExam as $key=>$resultExamItem){
						if($resultExamItem->getHidden()=="on")
							$arrExamObj = $resultExamItem->toArray(); 							
					}
				$resultClass = $modelClass->fetchAll($where);
				$arrClassObj = array();
				if(count($arrClassObj)>0)
					foreach($arrClassObj as $key=>$arrClassObjItem){
						if($arrClassObjItem->getHidden()=="on")
							$arrClassObj = $arrClassObjItem->toArray(); 							
					}
				*/
				$zend_view_helper_sltExam = new Zend_View_Helper_SltExam();	
				$zend_view_helper_sltClass = new Zend_View_Helper_SltClass();
				$sltExam    =   $zend_view_helper_sltExam->SltExam("exam_id", null, $course_id);
				$sltClass   =   $zend_view_helper_sltClass->SltClass("class_id", null, $course_id);
				$dataResponse = array("success"=>true,"exam_id"=>$sltExam,"class_id"=>$sltClass );
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

	public function getsubjectofclassAction(){
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
				$data = $request->getParams();
				$subject_id = $data['subject_id'];
				$modelClasss = new Default_Models_Classs();
				$result = $modelClasss->fetchAll("`subject_id`='".$subject_id."'");
				if(count($result)>0){
					$zend_view_helper_sltClassSubject = new Zend_View_Helper_SltClassSubject();
					$sltClassSubject   =   $zend_view_helper_sltClassSubject->SltClassSubject("class_id", null, $subject_id);
				}
				$dataResponse = array("success"=>true,"slt_class_subject"=>$sltClassSubject );
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
	
	public function getinfocourseobjAction(){
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
				$data = $request->getParams();
				$course_id = $data['course_id'];
				$modelCourse = new Default_Models_Course();
				$modelCourse->find("id",$course_id);
				$courseID = $modelCourse->getId();
				$courseObj = array();
				if(!empty($courseID)){
					$courseObj = $modelCourse->toArray();
					$zend_view_helper_formatDate = new Zend_View_Helper_FormatDate();
					$courseObj['time_start'] = $zend_view_helper_formatDate->convertYmdToMdyJustDate($courseObj['time_start']);
					$courseObj['time_end'] = $zend_view_helper_formatDate->convertYmdToMdyJustDate($courseObj['time_end']);
					$dataResponse = array("success"=>true,"course_obj"=>$courseObj);
				}else
					$dataResponse = array("success"=>true,"course_obj"=>1);
				
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
	
	public function _validate($data,$update=false){
		$this->_arrError = Array();
			// CHECK MỘT SỐ TRƯỜNG NHẬP TRỐNG 
			if(empty($data['full_name']))  				$this->_arrError[] = "Tên lớp học trống.";
			if(empty($data['short_name'])) 				$this->_arrError[] = "Tên viết tắt trống.";
			/*
			if(empty($data['time_start'])) 				$this->_arrError[] = "Thời gian bắt đầu lớp học trống.";
			if(empty($data['time_end'])) 				$this->_arrError[] = "Thời gian kết thúc lớp học trống.";
			*/
		
		if($update){// is update 
			if(empty($data['id'])) 					$this->_arrError[] = "ID chương kỳ thi không tồn tại.";			
				
		}else{//is insert 
		}
	}	
	
	private function _filter($data) {
		
			$data['full_name'] 					= Zend_Filter::filterStatic($data['full_name'], 'StringTrim');
			$data['short_name'] 				= Zend_Filter::filterStatic($data['short_name'], 'StringTrim');
			// filter cac script
        	$filter 	= new Zend_Filter_StripTags();
        	$data['full_name'] = $filter->filter($data['full_name']); 
        	$data['short_name'] = $filter->filter($data['short_name']);
		$data['full_name'] = str_replace('\"', '"',$data['full_name']);
		$data['full_name'] = str_replace("\'", "'",$data['full_name']);
        	
		$data['short_name'] = str_replace('\"', '"',$data['short_name']);
		$data['short_name'] = str_replace("\'", "'",$data['short_name']);
		
		return $data;
	}	
	
	
	
	


	
} 