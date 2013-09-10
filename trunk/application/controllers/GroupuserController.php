<?php

require_once APPLICATION_PATH . '/models/Groupuser.php';
require_once APPLICATION_PATH . '/models/Groupdetail.php';
require_once 'Zend/Filter/StripTags.php';

class GroupUserController extends Zend_Controller_Action
{
	private $_groupUser;
	private $_arrError;
	
	
	public function init()
	{
        $objSessionNamespace = new Zend_Session_Namespace( 'Zend_Auth' );
		$objSessionNamespace->setExpirationSeconds( 86400 );
		$this->view->controller = "groupuser";
		$this->_helper->layout->setLayout('admin');
		//$this->_helper->layout->setLayout('teacher');
		$this->view->headLink()->appendStylesheet(BASE_URL. '/css/teacher.css');		
		$this->_groupUser 	 = new Default_Models_GroupUser();

		$this->_cols_view       = array("id","group_name"); 
				// các cột hiển thị <tên sẽ hiển thị trong head của table>
		$this->_cols_view_title = array("id","Tên nhóm người dùng");

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
							$where.='`'.$col_viewItem.'` LIKE "%'.addslashes($sSearch).'%" OR ';
					}
					$where.="0 AND ";
				}				
				for($i=0;$i<=$iColumns-2;$i++){
					$search_col = $data["sSearch_".$i];
					if(!empty($search_col))
						$where.='`'.$this->_cols_view[$i].'` LIKE "%'.addslashes($search_col).'%" AND ' ;
				}
				$where.="1";
				
				$dataObj    = $this->_groupUser->fetchAll($where,$order,$iDisplayLength,$iDisplayStart);
				$total 		= count($this->_groupUser->fetchAll($where));
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
					$this->_groupUser->find("id",$arr_idItem);
					if($this->_groupUser->getId())
						$d5j45= "";
						//$this->_groupUser->delete('id', $arr_idItem);
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
		$this->view->controller = "groupuser" ;
		$models_group_detail    = new Default_Models_GroupDetail();
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
			// get all atrribute and validate
				$data = $request->getParams();
				$data = $this->_filter($data);
				$permissions   = $data["permission"];
				$group_id      = $data["group_id"];
				$group_name    = $data["group_name"];
				$this->_groupUser = new Default_Models_GroupUser() ;					
				$this->_groupUser->setGroup_name($group_name);
				$this->_groupUser->setId($group_id);
				if(empty($group_id)){ // thêm mới
					$this->_groupUser->setId(null);
					$group_id      = $this->_groupUser->save();
				}else
					$this->_groupUser->save();
					
				// bước tiếp theo xóa hết các group_detail có thằng group_id
				$models_group_detail->delete('group_id',$group_id);
				// thêm mới lại vào thằng detail
				if(count($permissions)>0)
					foreach($permissions as $permissionItem){						 
						$arr  = explode('-',$permissionItem);
						$controller  = $arr[0];
						$action		 = $arr[1];
						if(isset($data[$permissionItem])){
							 $value_controler_action = $data[$permissionItem];
							 $show_menu 		     = $data[$permissionItem."_show_menu"];
							 
							 $models_group_detail->setId(null);
							 $models_group_detail->setGroup_id($group_id);
							 $models_group_detail->setController($controller);
							 $models_group_detail->setAction($action);
							 $models_group_detail->setValue($value_controler_action);
							 $models_group_detail->setShow_menu($show_menu);
							 $models_group_detail->save();
						} 	  						
					}
					$result = $this->_groupUser->find('id', $group_id);
					if($this->_groupUser->getId()){
						$Obj = $this->_groupUser->toArray();
                                                $Obj['isupdate'] = 1;  // insert new 
						$models_group_detail  = new Default_Models_GroupDetail();
						$group_detail         = $models_group_detail->fetchAll('`group_id`='.$group_id);
						$this->view->group_detail = $group_detail;						
						$this->view->Obj  = $Obj;
						$this->view->msgSuccess  = "success";
						$this->render("groupuser");	 
					}
			}elseif($request->isGet()) 
			{
					$data = $request->getParams();
					$id = $data['id'];		
					if(empty($data['id']))
						$this->_redirect("/groupuser");						
					$result = $this->_groupUser->find('id', $id);
					if($this->_groupUser->getId()){
						$Obj = $this->_groupUser->toArray();
						$Obj['isupdate'] = 1;
						$models_group_detail  = new Default_Models_GroupDetail();
						$group_detail         = $models_group_detail->fetchAll('`group_id`='.$id);
						
						
						$this->view->group_detail = $group_detail;
						$this->view->Obj  = $Obj;

						$this->render("groupuser");	 
					}
			}else{
				$this->_redirect("error/error");
			}	
			
			
		}catch(Exception $ex){
			$this->_redirect("/groupuser");	
		}
	}
	
	public function addAction(){
		$this->view->action = "edit" ;
		$this->view->controller = "groupuser" ;
		$Obj  = Array();
		$Obj['isupdate'] = 0;  // insert new 
                $Obj['id']=null;
                
                $Obj['group_name']='';
		$this->view->Obj  = $Obj;
		$this->render("groupuser");	 
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

	public function _validate($data,$update=false){
		$this->_arrError = Array();
			// CHECK MỘT SỐ TRƯỜNG NHẬP TRỐNG 
			if(empty($data['name']))  					$this->_arrError[] = "Tên lớp học trống.";
			if(empty($data['short_name'])) 				$this->_arrError[] = "Tên viết tắt trống.";
		
		if($update){// is update 
			if(empty($data['id'])) 					$this->_arrError[] = "ID không tồn tại.";			
				
		}else{//is insert 
		}
	}	
	
	private function _filter($data) {
		
			$data['group_name'] 					=  Zend_Filter::filterStatic($data['group_name'], 'StringTrim');
			// filter cac script
        	$filter 	= new Zend_Filter_StripTags();
        	$data['group_name'] = $filter->filter($data['group_name']); 
		$data['group_name'] = str_replace('\"', '"',$data['group_name']);
		$data['group_name'] = str_replace("\'", "'",$data['group_name']);
        	
		return $data;
	}	
	
	
	
	


	
} 