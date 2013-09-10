<?php

require_once APPLICATION_PATH . '/models/Permissionusercustom.php';

class PermissionUserCustomController extends Zend_Controller_Action
{
	private $_permissionUserCustom;
	private $_arrError;
	
	
	public function init()
	{
		$this->_permissionUserCustom 	 = new Default_Models_PermissionUserCustom();

		$this->_cols_view       = array("id","user_id","group_user_id"); 
				// các cột hiển thị <tên sẽ hiển thị trong head của table>
		$this->_cols_view_title = array("id","Mã số User", "Nhóm User");		
		
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
				
				$dataObj    = $this->_permissionUserCustom->fetchAll($where,$order,$iDisplayLength,$iDisplayStart);
				$total 		= count($this->_permissionUserCustom->fetchAll($where));
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
					$this->_permissionUserCustom->find("id",$arr_idItem);
					if($this->_permissionUserCustom->getId())
						$this->_permissionUserCustom->delete('id', $arr_idItem);
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
		$this->view->controller = "permissionusercustom" ;
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
					$this->render("permissionusercustom");
				}else{
					$this->_permissionUserCustom = new Default_Models_PermissionUserCustom() ;
					$this->_permissionUserCustom->setOptions($data);
					if(empty($data['id']))
						$this->_permissionUserCustom->setId(null);	
										
					$this->_permissionUserCustom->save();					
					$this->_redirect("/permissionusercustom");
				}
				
			}elseif($request->isGet()) {
					$data = $request->getParams();
					$id = $data['id'];			
					$result = $this->_permissionUserCustom->find('id', $id);
					if($this->_permissionUserCustom->getId()){
						$Obj = $this->_permissionUserCustom->toArray() ;
						$Obj['isupdate'] = 1;
						$this->view->Obj  = $Obj;
						$this->render("permissionusercustom");	 
					}
				}else{
					$this->_redirect("error/error");
				}				
			
		}catch(Exception $ex){
		}
	}
	
	public function addAction(){
		$this->view->action = "edit" ;
		$this->view->controller = "permissionusercustom" ;
		$Obj  = Array();
		$Obj['isupdate'] = 0;  // insert new 
		$this->view->Obj  = $Obj;
		$this->render("permissionusercustom");	 
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
		if($update){// is update 
			if(empty($data['id'])) 					$this->_arrError[] = "ID không tồn tại.";			
				
		}else{//is insert 
		}
	}	
	
	private function _filter($data) {
		
		if ($data['action'] == 'add' || $data['action'] == 'edit') {
			//$data['full_name'] 					=  Zend_Filter::filterStatic($data['full_name'], 'StringTrim');
		}
		return $data;
	}	
	
	
	
	


	
} 