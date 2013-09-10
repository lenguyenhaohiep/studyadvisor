<?php

require_once APPLICATION_PATH . '/models/Course.php';
require_once APPLICATION_PATH . '/models/Classs.php';
require_once APPLICATION_PATH . '/models/Classhasstudent.php';
require_once LIBRARY_PATH.'/FormatDate.php';
require_once 'Zend/Filter/StripTags.php';

class CourseController extends Zend_Controller_Action
{
	private $_course;
	private $_arrError;
	
	
	public function init()
	{
        $objSessionNamespace = new Zend_Session_Namespace( 'Zend_Auth' );
		$objSessionNamespace->setExpirationSeconds( 86400 );
		$this->view->controller = "course";
		$this->_helper->layout->setLayout('admin');
		//$this->_helper->layout->setLayout('teacher');
		$this->view->headLink()->appendStylesheet(BASE_URL. '/css/teacher.css');		
		
		$this->_course 	 = new Default_Models_Course();

		$this->_cols_view       = array("id","full_name","short_name","summary","hidden"); 
				// các cột hiển thị <tên sẽ hiển thị trong head của table>
		$this->_cols_view_title = array("id","Tên đầy đủ","Tên viết tăt","Mô tả","Đóng hay mở");
				
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
				
				$dataObj    = $this->_course->fetchAll($where,$order,$iDisplayLength,$iDisplayStart);
				$total 		= count($this->_course->fetchAll($where));
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
					$this->_course->find("id",$arr_idItem);
					if($this->_course->getId()){
						$modelClass = new Default_Models_Classs();
						$result = $modelClass->fetchAll("`course_id`='".$arr_idItem."'");
						if(count($result)>0)
							throw new Exception("Khóa học này đã có lớp học tham gia, bạn không thể xóa được.");
						$this->_course->delete('id', $arr_idItem);
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
		$this->view->controller = "course" ;
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
					$this->render("course");
				}else{
					$this->_course  = new Default_Models_Course();
					$this->_course->setOptions($data);
					if(empty($data['id']))
						$this->_course->setId(null);	
										
					$this->_course->save();					
					$this->_redirect("/course");
				}
				
			}elseif($request->isGet()) {
					$data = $request->getParams();
					$id = $data['id'];	
					if(empty($data['id']))
						$this->_redirect("/course");
					$result = $this->_course->find('id', $id);
					if($this->_course->getId()){
						$Obj = $this->_course->toArray() ;
						$Obj['isupdate'] = 1;
						$this->view->Obj  = $Obj;
						$this->render("course");	 
					}
				}else{
					$this->_redirect("error/error");
				}				
			
		}catch(Exception $ex){
		}
	}
	
	public function addAction(){
		$this->view->action = "edit" ;
		$this->view->controller = "course" ;
		$Obj  = Array();
		$Obj['isupdate'] = 0;  // insert new
                $Obj['full_name']='';
                $Obj['short_name']='';
                $Obj['id']=null;
                $Obj['summary']='';
                $Obj['time_start']='';
                $Obj['time_end']='';
                $Obj['hidden']='';
                
		$this->view->Obj  = $Obj;
		$this->render("course");	 
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

	public function getlistclassAction(){
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
				$data = $request->getParams();
				$course_id = $data['course_id'];
				$modelsClass = new Default_Models_Classs();
				$result = $modelsClass->fetchAll("`course_id`='".$course_id."'");
				$arrJson=array();
				$okGetMoreStu=array();
				if(count($result)>0)
					foreach($result as $resultItem){
						$arrJson[] = $resultItem->toArrayHaveConvertDate();
						//$resultItem = new Default_Models_Classs();
						$modelClassHasStudent = new Default_Models_ClassHasStudent();
						$maxStudentInClass = $modelClassHasStudent->fetchAll("`class_id`='".$resultItem->getId()."'");
						if(count($maxStudentInClass)<$resultItem->getMax_user())
							$okGetMoreStu[] = 1;
						else
							$okGetMoreStu[] = 0;
					}
				else $arrJson = null; 
				$dataResponse = array("success"=>true,"data"=>$arrJson,"getmore"=>$okGetMoreStu);
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
			if(empty($data['time_start'])) 				$this->_arrError[] = "Thời gian bắt đầu lớp học trống.";
			if(empty($data['time_end'])) 				$this->_arrError[] = "Thời gian kết thúc lớp học trống.";
			if(!empty($data['time_start']))
				if(strtotime($data['time_start'])>strtotime($data['time_end']))
					$this->_arrError[] = "Thời gian kết thúc khóa học phải lớn hơn thời bắt đầu khóa học.";
			
		
		if($update){// is update 
			if(empty($data['id'])) 					$this->_arrError[] = "ID chương khóa học không tồn tại.";			
				
		}else{//is insert 
		}
	}	
	
	private function _filter($data) {
		
			$data['full_name'] 					= Zend_Filter::filterStatic($data['full_name'], 'StringTrim');
			$data['short_name'] 				= Zend_Filter::filterStatic($data['short_name'], 'StringTrim');
			$data['time_start']					= Zend_View_Helper_FormatDate::convertDmyToYmd($data['time_start']);
			$data['time_end']					= Zend_View_Helper_FormatDate::convertDmyToYmd($data['time_end']);
			// filter cac script
        	$filter 	= new Zend_Filter_StripTags();
        	$data['full_name'] = $filter->filter($data['full_name']); 
        	$data['short_name'] = $filter->filter($data['short_name']);
			if(empty($data['hidden']))
				$data['hidden'] = 'off';
        	
		$data['full_name'] = str_replace('\"', '"',$data['full_name']);
		$data['full_name'] = str_replace("\'", "'",$data['full_name']);
		
		$data['short_name'] = str_replace('\"', '"',$data['short_name']);
		$data['short_name'] = str_replace("\'", "'",$data['short_name']);
		
		return $data;
	}	
	

	
} 