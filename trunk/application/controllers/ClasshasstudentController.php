<?php

require_once APPLICATION_PATH . '/models/Classhasstudent.php';
require_once APPLICATION_PATH . '/models/Classs.php';
require_once APPLICATION_PATH . '/models/User.php';

class ClassHasStudentController extends Zend_Controller_Action
{
	private $_classHasStudent;
	private $_arrError;
	
	
	public function init()
	{
		
        $objSessionNamespace = new Zend_Session_Namespace( 'Zend_Auth' );
		$objSessionNamespace->setExpirationSeconds( 86400 );
		$this->view->controller = "classhasstudent";
		$this->_helper->layout->setLayout('admin');
		//$this->_helper->layout->setLayout('teacher');
		$this->view->headLink()->appendStylesheet(BASE_URL. '/css/teacher.css');		
		
		$this->_classHasStudent 	 = new Default_Models_ClassHasStudent();

		$this->_cols_view       = array("id","class_id","name_student","user_id"); 
				// các cột hiển thị <tên sẽ hiển thị trong head của table>
		$this->_cols_view_title = array("id","Tên lớp","Tên sinh viên", "Mã số");

		// Phục vụ việc giữ lại Cái menu đang active ở bên dưới - focus menu
  		$controllerName   = $this->_request->getControllerName();
  		$actionName		  = $this->_request->getActionName();
  		$param			  = $this->_request->getParams();
  		$this->view->controllerName   = $controllerName;
  		$this->view->actionName 	  = $actionName;
  		$this->view->param			  = $param;	  		
		
		
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
				
				$dataObj    = $this->_classHasStudent->fetchAll($where,$order,$iDisplayLength,$iDisplayStart);
				$total 		= count($this->_classHasStudent->fetchAll($where));
				$json_data  = new stdClass();
				$json_data->sEcho = $sEcho;
				$json_data->iTotalRecords = $total;
				$json_data->iTotalDisplayRecords = $total;
				$aaData = array();				
				if(count($dataObj)){
					foreach ($dataObj as $dataItem){
						$dataItemArray  = $dataItem->toArray();
						$id  			= $dataItemArray["id"];
						
						
						$modelsClass = new Default_Models_Classs();
						if(empty($dataItemArray["class_id"]))
							$dataItemArray["class_id"] = "";
						else
						$modelsClass->find("id",$dataItemArray["class_id"]);
						if($modelsClass->getId())
							$dataItemArray["class_id"] = $modelsClass->getFull_name();
						if(empty($dataItemArray["user_id"]))
							$dataItemArray["name_student"] = "";
						else
						{	
							$modelsUser = new Default_Models_User();
							$modelsUser->find("id",$dataItemArray["user_id"]);
							if($modelsUser->getId())
							{
								$dataItemArray["name_student"] = $modelsUser->getFirstname()." ".$modelsUser->getLastname();
								$dataItemArray["user_id"] 	   = $modelsUser->getUser_code();
							}
							else
								$dataItemArray["name_student"] = "";
						}
						
						
						$tmpArr 		= array();
						//$strAction    = '<a class="view-icon"   href="'. BASE_URL .'/'. $data["controller"] .'/view/id/'.  $id .'" onclick="viewClick(this.href); return false;"><img class="fugue fugue-eye" alt="" src="'. BASE_URL .'/img/icons/space.gif"/></a>&nbsp;&nbsp;';
						$strAction   = '<a class="edit-icon"   href="'. BASE_URL .'/'. $data["controller"] .'/edit/id/'.  $id .'"><img class="fugue fugue-pencil" alt="Chỉnh sửa" src="'. BASE_URL .'/img/icons/space.gif"/></a>&nbsp;&nbsp;';
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
					$this->_classHasStudent = new Default_Models_ClassHasStudent();
					$this->_classHasStudent->find("id",$arr_idItem);
					if($this->_classHasStudent->getId())
						$this->_classHasStudent->delete('id', $arr_idItem);
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
		$this->view->controller = "classhasstudent" ;
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
					$this->render("classhasstudent");
				}else{
					$this->_classHasStudent  = new Default_Models_ClassHasStudent();
					$this->_classHasStudent->setOptions($data);
					if(empty($data['id']))
						$this->_classHasStudent->setId(null);	
										
					$this->_classHasStudent->save();					
					$this->_redirect("/classhasstudent");
				}
				
			}elseif($request->isGet()) {
					$data = $request->getParams();
					$id = $data['id'];			
					$result = $this->_classHasStudent->find('id', $id);
					if($this->_classHasStudent->getId()){
						$Obj = $this->_classHasStudent->toArray() ;
						$Obj['isupdate'] = 1;
						$this->view->Obj  = $Obj;
						$this->render("classhasstudent");	 
					}
				}else{
					$this->_redirect("error/error");
				}				
			
		}catch(Exception $ex){
		}
	}
	
	public function addAction(){
		$this->view->action = "edit" ;
		$this->view->controller = "classhasstudent" ;
		$Obj  = Array();
		$Obj['isupdate'] = 0;  // insert new 
		$this->view->Obj  = $Obj;
		$this->render("classhasstudent");	 
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

	public function removestudentAction(){
	try {
			$request = $this->getRequest();
			if ($request->isPost()) {
				$data = $request->getParams();
				$class_id = $data["class_id"];
				$user_id = $data["user_id"];
				$modelClassHasStudent = new Default_Models_ClassHasStudent();
				$result = $modelClassHasStudent->fetchAll("`class_id`='".$class_id."' AND `user_id`='".$user_id."'");
				if(count($result)>0)
				{
					foreach ($result as $resultItem )
					{
						$modelClassHasStudent->delete("id",$resultItem->getId());
					}
					$json_data = array("success"=>true);
				}else
					throw new Exception("Không tồn tại sinh viên này trong lớp học.");
				
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
	
	public function _validate($data,$update=false){
		$this->_arrError = Array();
			// CHECK MỘT SỐ TRƯỜNG NHẬP TRỐNG 
			if(empty($data['class_id']))  					$this->_arrError[] = "Chưa chọn lớp học.";
			if(empty($data['user_id']))		 				$this->_arrError[] = "Chưa chọn sinh viên.";
		
		if($update){// is update 
			if(empty($data['id'])) 					$this->_arrError[] = "ID không tồn tại.";			
				
		}else{//is insert 
		}
	}	
	
	private function _filter($data) {
		
		if ($data['action'] == 'add' || $data['action'] == 'edit') {
			
		}
		return $data;
	}	
	
	
	
	


	
} 