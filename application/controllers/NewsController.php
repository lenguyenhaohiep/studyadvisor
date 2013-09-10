<?php
require_once APPLICATION_PATH . '/models/News.php';
require_once APPLICATION_PATH . '/models/Category.php';
require_once LIBRARY_PATH.		'/FormatDate.php';
require_once APPLICATION_PATH . '/models/User.php';
require_once 'Zend/Filter/StripTags.php';

class NewsController extends Zend_Controller_Action
{
	private $_news;
	private $_arrError;
	
	
	public function init()
	{
		$this->view->controller = "news";
		$this->_helper->layout->setLayout('admin');
		//$this->_helper->layout->setLayout('teacher');
		$this->view->headLink()->appendStylesheet(BASE_URL. '/css/teacher.css');		
		$this->_news 	 = new Default_Models_News();
		$this->_arrError = Array();
		$this->_cols_view       = array("id","title","category_id","created"); 
				// các cột hiển thị <tên sẽ hiển thị trong head của table>
		$this->_cols_view_title = array("id","Tiêu đề","Chuyên mục","Ngày tạo");

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
				
				$dataObj    = $this->_news->fetchAll($where,$order,$iDisplayLength,$iDisplayStart);
				$total 		= count($this->_news->fetchAll($where));
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
						
						if(!empty($dataItemArray["category_id"]))
						{
							$modelCategory = new Default_Models_Category();
							$modelCategory->find("id",$dataItemArray["category_id"]);
							if($modelCategory->getId())
								$dataItemArray["category_id"] = $modelCategory->getName(); 
							
						}else
							$dataItemArray["category_id"]="Chưa có";
						
						$zend_view_helper_date = new Zend_View_Helper_FormatDate();
						if(!empty($dataItemArray["created"]))
							$dataItemArray["created"] = $zend_view_helper_date->convertSecondToDateTimeHasHour($dataItemArray["created"]);
						
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
					$this->_news->find("id",$arr_idItem);
					if($this->_news->getId())
						$this->_news->delete('id', $arr_idItem);
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
					$this->render("news");
				}else{
					$this->_news = new Default_Models_News();
					$this->_news->setOptions($data);
					if(empty($data['id']))
						$this->_news->setId(null);	
										
					$this->_news->save();					
					$this->_redirect("/news");
				}
				
			}elseif($request->isGet()) {
					$data = $request->getParams();
					$id = $data['id'];	
					if(empty($data['id']))
						$this->_redirect("/news");							
					$result = $this->_news->find('id', $id);
					if($this->_news->getId()){
						$Obj = $this->_news->toArray() ;
						$Obj['isupdate'] = 1;
						$this->view->Obj  = $Obj;
					}
				}else{
					$this->_redirect("error/error");
				}				
			
		}catch(Exception $ex){
		}
	}
	
	public function addAction(){
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
					//$this->render("category");
				}else{
					$this->_news = new Default_Models_News();
					$this->_news->setOptions($data);
					if(empty($data['id']))
						$this->_news->setId(null);	
										
					$this->_news->save();					
					$this->_redirect("/news");
				}
				
			}elseif($request->isGet()) {
				$Obj  = Array();
				$Obj['isupdate'] = 0;  // insert new 
                                $Obj['id']=null;
                                $Obj['title']='';
                                $Obj['description']='';
                                $Obj['content']='';
                                $Obj['path_image']='';
                                $Obj['category_id']='';
                                $Obj['created']='';                                
                                $Obj['modified']='';                                
                                $Obj['publish']='';                                
                                
				$this->view->Obj  = $Obj;
			}
		}catch(Exception $ex){
		}		
/*		
		$this->view->action = "edit" ;
		$Obj  = Array();
		$Obj['isupdate'] = 0;  // insert new 
		$this->view->Obj  = $Obj;
		$this->render("subject");
*/	 
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

	public function viewnewsAction(){
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
				
			}elseif($request->isGet()) {
			$this->_helper->layout->setLayout('student');
			$this->view->headLink()->appendStylesheet(BASE_URL. '/js/jquery-ui-1.8.9.custom/css/custom-theme/jquery-ui-1.8.9.custom.css');
			$this->view->headLink()->appendStylesheet(BASE_URL. '/css/student.css');				
				$data = $request->getParams();
				$modelNews = new Default_Models_News();
				$modelNews->find("id",$data['id']);
				if($modelNews->getId()){
					$this->view->news = $modelNews->toArray();
					$modelCategory = new Default_Models_Category();
					$modelCategory->find("id",$modelNews->getCategory_id());
					if($modelCategory->getId())
						$this->view->category = $modelCategory->toArray();
					$modelUser = new Default_Models_User();
					$modelUser->find("id",$modelNews->getUser_create());
					$this->view->user_create =  $modelUser->getFirstname()." ".$modelUser->getLastname();
				}
			}
		}catch(Exception $ex){
			echo $ex->getMessage();  
		}		
		
	}
	
	public function _validate($data,$update=false){
		
			// CHECK MỘT SỐ TRƯỜNG NHẬP TRỐNG 
			if(empty($data['title']))  							$this->_arrError[] = "Tiêu đề trống.";
			if(empty($data['description']))  					$this->_arrError[] = "Mô tả tin tức trống.";
			if(empty($data['content']))  						$this->_arrError[] = "Nội dung tin tức trống.";
			if(empty($data['category_id']))  					$this->_arrError[] = "Chưa chọn chuyên mục.";
		
		if($update){// is update 
			if(empty($data['id'])) 				$this->_arrError[] = "ID tin tức không tồn tại.";			
				
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
			$data['title'] 				= Zend_Filter::filterStatic($data['title'], 'StringTrim');
			$data['publish'] 			= Zend_Filter::filterStatic($data['publish'], 'StringTrim');
			$filter 	= new Zend_Filter_StripTags();
        	$data['title'] = $filter->filter($data['title']); 
        	
		if($data['isupdate'] == 0 ){ // add new question
			$data['user_create']		= $this->getUserId();
			$data['created'] = date("U");  
		}
		if($data['isupdate'] == 1 ){
			$data['modified'] = date("U");
			$modelNew = new Default_Models_News();
			$modelNew->find("id",$data['id']);
			if($modelNew->getId()){
				$data['user_create'] = $modelNew->getUser_create();
				$data['created'] 	 = $modelNew->getCreated();  
			}
		}
        	
		$data['title'] = str_replace('\"', '"',$data['title']);
		$data['title'] = str_replace("\'", "'",$data['title']);
		
		$data['description'] = str_replace('\"', '"',$data['description']);
		$data['description'] = str_replace("\'", "'",$data['description']);
		$data['content'] = str_replace('\"', '"',$data['content']);
		$data['content'] = str_replace("\'", "'",$data['content']);
		
		return $data;
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