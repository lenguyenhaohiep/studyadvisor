<?php
require_once APPLICATION_PATH . '/models/Teachassignment.php';
require_once APPLICATION_PATH . '/models/User.php';
require_once APPLICATION_PATH . '/models/Subject.php';
require_once APPLICATION_PATH . '/models/Teachassignment.php';
require_once APPLICATION_PATH . '/models/Classs.php';

class TeachassignmentController extends Zend_Controller_Action
{
	private $_teachassignment;
	private $_arrError;
	
	
	public function init()
	{
        $objSessionNamespace = new Zend_Session_Namespace( 'Zend_Auth' );
		$objSessionNamespace->setExpirationSeconds( 86400 );
		$this->view->controller = "teachassignment";
		$this->_helper->layout->setLayout('admin');
		//$this->_helper->layout->setLayout('teacher');
		$this->view->headLink()->appendStylesheet(BASE_URL. '/css/teacher.css');		
		$this->_teachassignment 	 = new Default_Models_Teachassignment();

		$this->_cols_view       = array("id","subject_id","class_id","full_name","user_code"); 
				// các cột hiển thị <tên sẽ hiển thị trong head của table>
		$this->_cols_view_title = array("id","Môn học","Lớp học","Giảng viên","Mã số giảng viên");

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
				
				$dataObj    = $this->_teachassignment->fetchAll($where,$order,$iDisplayLength,$iDisplayStart);
				$total 		= count($this->_teachassignment->fetchAll($where));
				$json_data  = new stdClass();
				$json_data->sEcho = $sEcho;
				$json_data->iTotalRecords = $total;
				$json_data->iTotalDisplayRecords = $total;//count($dataObj);
				$aaData = array();				
				if(count($dataObj)){
					foreach ($dataObj as $dataItem){
						$dataItemArray  = $dataItem->toArray();
						$id  			= $dataItemArray["id"];
						
						if(empty($dataItemArray["class_id"]))
							$dataItemArray["class_id"] = "";
						else
						{	
							$modelsClass = new Default_Models_Classs();
							$modelsClass->find("id",$dataItemArray["class_id"]);
							if($modelsClass->getId())
							{
								$dataItemArray["class_id"] = $modelsClass->getFull_name();
							}
							else
								$dataItemArray["class_id"] = "";
						}
						
						
						$modelSubject = new Default_Models_Subject();
						$modelSubject->find("id",$dataItemArray["subject_id"]);
						$dataItemArray["subject_id"] = $modelSubject->getFull_name();
						$modelUser = new Default_Models_User();
						$modelUser->find("id",$dataItemArray["user_id"]);
						$dataItemArray["full_name"] = $modelUser->getFirstname()." ". $modelUser->getLastname(); 
						$dataItemArray["user_code"] = $modelUser->getUser_code();
						$tmpArr 		= array();
						//$strAction    = '<a class="view-icon"   href="'. BASE_URL .'/'. $data["controller"] .'/view/id/'.  $id .'" onclick="viewClick(this.href); return false;"><img class="fugue fugue-eye" alt="" src="'. BASE_URL .'/img/icons/space.gif"/></a>&nbsp;&nbsp;';
						$strAction    = '<a class="edit-icon"   href="'. BASE_URL .'/'. $data["controller"] .'/edit/id/'.  $id .'"><img class="fugue fugue-pencil" alt="Chỉnh sửa" src="'. BASE_URL .'/img/icons/space.gif"/></a>&nbsp;&nbsp;';
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
					$this->_teachassignment->find("id",$arr_idItem);
					if($this->_teachassignment->getId())
						$this->_teachassignment->delete('id', $arr_idItem);
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
					$this->render("teachassignment");
				}else{
					
					$this->_teachassignment = new Default_Models_Teachassignment();
					$this->_teachassignment->setOptions($data);
					if(empty($data['id']))
						$this->_teachassignment->setId(null);
					$this->_teachassignment->save();
					$this->_redirect("/teachassignment");
				}
				
			}elseif($request->isGet()) {
					$data = $request->getParams();
					$id = $data['id'];		
					if(empty($data['id']))
						$this->_redirect("/teachassignment");			
					$result = $this->_teachassignment->find('id', $id);
					if($this->_teachassignment->getId()){
						$Obj = $this->_teachassignment->toArray() ;
						$Obj['isupdate'] = 1;
						$modelUser = new Default_Models_User();
						$modelUser->find("id",$Obj['user_id']);
						if($modelUser->getId()){
							$Obj['user_code'] = $modelUser->getUser_code();
						}
						$this->view->Obj  = $Obj;
						$this->render("teachassignment");	 
					}
				}else{
					$this->_redirect("error/error");
				}				
			
		}catch(Exception $ex){
		}
	}
	
	public function addAction(){
		$this->view->action = "edit" ;
		$Obj  = Array();
		$Obj['isupdate'] = 0;  // insert new 
                $Obj['id']=null;
                
                $Obj['course_id']='';
                $Obj['user_id']='';
                $Obj['class_id']='';
                $Obj['subject_id']='';
                $Obj['user_code']='';
                
		$this->view->Obj  = $Obj;
		$this->render("teachassignment");	 
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
			echo $ex->getMessage();  
		}
	}

	public function _validate($data,$update=false){
		$this->_arrError = Array();
			// CHECK MỘT SỐ TRƯỜNG NHẬP TRỐNG 
			//if(empty($data['user_id']))  				$this->_arrError[] = "Chưa nhập mã số giảng viên.";
			if(empty($data['subject_id'])) 				$this->_arrError[] = "Chưa chọn môn học.";
			if(empty($data['class_id'])) 				$this->_arrError[] = "Chưa chọn lớp học.";
		
			$usercode = $data["user_code"];
			$modelUser = new Default_Models_User();
			$result = $modelUser->fetchAll("`user_code`='".$usercode."'");
			if(count($result)>0)
			{
				$objUser = $result[0]->toArray();
				
				if($objUser['group_id']!=3)
					$this->_arrError[] = "Mã số giảng viên không đúng.";
			}else
					$this->_arrError[] = "Mã số giảng viên không đúng.";
			$modelTeachassign = new Default_Models_Teachassignment();
			$result = $modelTeachassign->fetchAll("`user_id`='".$data['user_id']."' AND `subject_id`='".$data['subject_id']."' AND `class_id`='".$data['class_id']."'");
			if(count($result)>0 && $data['id']!=$result[0]->getId())
				$this->_arrError[] = "Giảng viên đã được phân công dạy lớp này.";
			
		if($update){// is update 
			if(empty($data['id'])) 				$this->_arrError[] = "ID phân công giảng dạy không tồn tại.";			
				
		}else{//is insert 
		}
	}	
	
	public function validateusercodeAction(){
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
				$data = $request->getParams();
				$usercode = $data["user_code"];
				$modelUser = new Default_Models_User();
				$result = $modelUser->fetchAll("`user_code`='".$usercode."'");
				if(count($result)>0)
				{
					$objUser = $result[0]->toArray();
					if($objUser['group_id']!=3)
						throw new Exception("Mã số giảng viên không đúng.");
						
					$json_data = array("success"=>true,"data_user"=>$objUser);
				}else
					throw new Exception("Mã số giảng viên không đúng.");
				
			}elseif ($request->isGet()){
				throw new Exception("Phương thức không đúng.");
			}
		}
		catch (Exception $ex) {
			$json_data = array("success"=>false,"error"=>$ex->getMessage());
		}	
		echo Zend_Json::encode($json_data);
		die();
	}
	
	private function _filter($data) {
		
			//$data['class_id'] 			= Zend_Filter::filterStatic($data['class_id'], 'StringTrim');
			$data['user_id'] 			= Zend_Filter::filterStatic($data['user_id'], 'StringTrim');
			
		return $data;
	}	
	
	
	
	


	
} 