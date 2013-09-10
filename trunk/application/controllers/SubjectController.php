<?php
require_once APPLICATION_PATH . '/models/Subject.php';
require_once APPLICATION_PATH . '/models/Question.php';
require_once VIEW_PATH . '/helpers/SltChapterSubject.php';
require_once 'Zend/Filter/StripTags.php';

class SubjectController extends Zend_Controller_Action
{
	private $_subject;
	private $_arrError;
	
	
	public function init()
	{
        $objSessionNamespace = new Zend_Session_Namespace( 'Zend_Auth' );
		$objSessionNamespace->setExpirationSeconds( 86400 );
		$this->view->controller = "subject";
		$this->_helper->layout->setLayout('admin');
		//$this->_helper->layout->setLayout('teacher');
		$this->view->headLink()->appendStylesheet(BASE_URL. '/css/teacher.css');		
		$this->_subject 	 = new Default_Models_Subject();

		$this->_cols_view       = array("id","full_name","short_name","summary"); 
				// các cột hiển thị <tên sẽ hiển thị trong head của table>
		$this->_cols_view_title = array("id","Tên môn học","Viết tắt","Mô tả");

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
				
				$dataObj    = $this->_subject->fetchAll($where,$order,$iDisplayLength,$iDisplayStart);
				$total 		= count($this->_subject->fetchAll($where));
				$json_data  = new stdClass();
				$json_data->sEcho = $sEcho;
				$json_data->iTotalRecords = $total;
				$json_data->iTotalDisplayRecords = $total;//count($dataObj);
				$aaData = array();				
				if(count($dataObj)){
					foreach ($dataObj as $dataItem){
						$dataItemArray  = $dataItem->toArray();
						$id  			= $dataItemArray["id"];
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
					$this->_subject->find("id",$arr_idItem);
					if($this->_subject->getId()){
						$modelQuestion = new Default_Models_Question();
						$result = $modelQuestion->fetchAll("`subject_id`='".$arr_idItem."'");
						if(count($result)>0)
							throw new Exception("Môn học đã có ngân hàng câu hỏi, bạn không thể xóa được.");
						$this->_subject->delete('id', $arr_idItem);
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
					$this->render("subject");
				}else{
					$this->_subject = new Default_Models_Subject();
					$this->_subject->setOptions($data);
					if(empty($data['id']))
						$this->_subject->setId(null);	
                                        $this->_subject->save();
//					var_dump($this->_subject->save());die;
					$this->_redirect("/subject");
				}
				
			}elseif($request->isGet()) {
					$data = $request->getParams();
					$id = $data['id'];	
					if(empty($data['id']))
						$this->_redirect("/subject");							
					$result = $this->_subject->find('id', $id);
					if($this->_subject->getId()){
						$Obj = $this->_subject->toArray() ;
						$Obj['isupdate'] = 1;
						$this->view->Obj  = $Obj;
						$this->render("subject");	 
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
                $Obj['full_name']='';
                $Obj['short_name']='';
                $Obj['id']=null;
                $Obj['summary']='';
		$this->view->Obj  = $Obj;
		$this->render("subject");	 
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
			if(empty($data['full_name']))  				$this->_arrError[] = "Tên môn học trống.";
			if(empty($data['short_name'])) 				$this->_arrError[] = "Tên viết tắt của môn học trống.";
			if(empty($data['summary'])) 				$this->_arrError[] = "Mô tả môn học trống.";
		
		if($update){// is update 
			if(empty($data['id'])) 				$this->_arrError[] = "ID môn học không tồn tại.";			
				
		}else{//is insert 
		}
	}	
	
	public function getchapterAction(){
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
				$data = $request->getParams();
				$subject_id = $data["id"];
				
				$zend_view_helper_sltChapterSubject = new Zend_View_Helper_SltChapterSubject();
				$view   =   $zend_view_helper_sltChapterSubject->SltChapterSubject("chapter_subject_id",null,$subject_id);
				$json_data = array("success"=>true,"view"=>$view);
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
		
			$data['full_name'] 				= Zend_Filter::filterStatic($data['full_name'], 'StringTrim');
			$data['short_name'] 			= Zend_Filter::filterStatic($data['short_name'], 'StringTrim');
			$data['summary'] 				= Zend_Filter::filterStatic($data['summary'], 'StringTrim');
			
			$filter 	= new Zend_Filter_StripTags();
        	$data['full_name'] = $filter->filter($data['full_name']); 
        	$data['short_name'] = $filter->filter($data['short_name']);
        	
			$data['full_name'] = str_replace('\"', '"',$data['full_name']);
			$data['full_name'] = str_replace("\'", "'",$data['full_name']);
			$data['short_name'] = str_replace('\"', '"',$data['short_name']);
			$data['short_name'] = str_replace("\'", "'",$data['short_name']);
			$data['summary'] = str_replace('\"', '"',$data['summary']);
			$data['summary'] = str_replace("\'", "'",$data['summary']);
			
		return $data;
	}	
	
	
	
	


	
} 