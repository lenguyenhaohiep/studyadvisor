<?php

require_once APPLICATION_PATH . '/models/Sheduleexam.php';
require_once LIBRARY_PATH.'/FormatDate.php';
require_once APPLICATION_PATH . '/models/Test.php';
require_once APPLICATION_PATH . '/models/Exam.php';
require_once APPLICATION_PATH . '/models/Classs.php';
require_once 'Zend/Filter/StripTags.php';

class SheduleExamController extends Zend_Controller_Action
{
	private $_sheduleExam;
	private $_arrError;
	
	
	public function init()
	{
        $objSessionNamespace = new Zend_Session_Namespace( 'Zend_Auth' );
		$objSessionNamespace->setExpirationSeconds( 86400 );
		$this->view->controller = "sheduleexam";
		$this->_helper->layout->setLayout('admin');
		//$this->_helper->layout->setLayout('teacher');
		$this->view->headLink()->appendStylesheet(BASE_URL. '/css/teacher.css');		
		$this->_sheduleExam 	 = new Default_Models_SheduleExam();
		$this->_cols_view       = array("id","name","exam_id","class_id", "hidden"); 
				// các cột hiển thị <tên sẽ hiển thị trong head của table>
		$this->_cols_view_title = array("id","Tên đầy đủ","Kỳ thi","Lớp học", "Tình trạng");
		$this->_cols_view_title_test = array("id","Tên đề thi","Ngày tạo");	
		$this->view->jQuery()->addJavascriptFile(BASE_URL . '/js/cms.js');
		$this->view->headLink()->appendStylesheet(BASE_URL . '/css/cms.css');
		
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
				
				$dataObj    = $this->_sheduleExam->fetchAll($where,$order,$iDisplayLength,$iDisplayStart);
				$total 		= count($this->_sheduleExam->fetchAll($where));
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

						if(empty($dataItemArray["exam_id"]))
							$dataItemArray["exam_id"] = "";
						else
						{	
							$modelsExam = new Default_Models_Exam();
							$modelsExam->find("id",$dataItemArray["exam_id"]);
							if($modelsExam->getId())
							{
								$dataItemArray["exam_id"] = $modelsExam->getFull_name();
							}
							else
								$dataItemArray["exam_id"] = "";
						}
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
				if(count($arr_id)>0)
				foreach($arr_id as $arr_idItem){
					$this->_sheduleExam = new Default_Models_SheduleExam();
					$this->_sheduleExam->find("id",$arr_idItem);
					if($this->_sheduleExam->getId()){
						// Begin Xóa các id đề thi trong lịch thi
						$List_Test_Id = $this->_sheduleExam->getList_test_id();
						if(!empty($List_Test_Id)){
							$arrTestId = explode(",",$List_Test_Id);
							if(count($arrTestId)>0)
							foreach($arrTestId as $arrTestIdItem){
								$modelTest = new Default_Models_Test();
								$modelTest->find("id",$arrTestIdItem);
								if($modelTest->getId())
								{
									$List_Shedule_Exam = $modelTest->getList_shedule_exam();
									$arrNewListSheduleExam = array();
									if(!empty($List_Shedule_Exam)){
										$arrSheduleExam = explode(",",$List_Shedule_Exam);
										if(count($arrSheduleExam)>0)
										foreach($arrSheduleExam as $key=>$arrSheduleExamItem){
											if($arrSheduleExamItem!=$this->_sheduleExam->getId())
												$arrNewListSheduleExam[] = $arrSheduleExamItem;									
										}
									}
									if(count($arrNewListSheduleExam)>0)
										$ListSheduleExamUpdate = implode(",",$arrNewListSheduleExam); 
										
									$modelTest->setList_shedule_exam($ListSheduleExamUpdate);
									$modelTest->save();
								}
							}
						}
						// End Xóa các id đề thi trong lịch thi
						
						$this->_sheduleExam->delete('id', $arr_idItem);
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
		$this->view->controller = "sheduleexam" ;
		$this->view->cols_view_title_test = $this->_cols_view_title_test;
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
			// get all atrribute and validate
				$data = $request->getParams();
				$data = $this->_filter($data);
				$list_test_id     = $data["list_test_id"]; 
				// $indexDechinhThuc ở đây là id của test luôn, chứ ko lấy chỉ số như cách làm cũ, cách đó gây ra lỗi 
				$indexDechinhThuc = $data["rdDechinhThuc"];
				if(count($data["list_test_id"])>0 && !is_numeric($indexDechinhThuc))
				{
						$this->_arrError[] = "Giá trị đề chính thức  không phải là số.";
						throw new Exception("");
				}
				
				$arr_list_test_id_temp = array();
				if($indexDechinhThuc>=0){ // Nếu có đề thi trong bài tesst
						//$arr_list_test_id_temp[] = $list_test_id[$indexDechinhThuc]; 
						if(count($list_test_id)>0){
							foreach($list_test_id as $key=>$list_test_idItem){
								if( $indexDechinhThuc != $list_test_idItem )
									$arr_list_test_id_temp[] = $list_test_idItem;
							}
		
							// Chuyển vị trí đề chính thức lên đầu tiên của list
							$arr_list_test_id_temp = array_merge(array(0=>$indexDechinhThuc), $arr_list_test_id_temp);
							$data["list_test_id"] = implode(',',$arr_list_test_id_temp) ;
						}
				}
				
				$new_list_test_id = $arr_list_test_id_temp ;
				if($data['isupdate']==1){
					if(!empty($data['id'])){
						$modelsSheduleExam = new Default_Models_SheduleExam();
						$modelsSheduleExam->find('id', $data['id']);
						if($modelsSheduleExam->getId())
						$old_list_test_id 	 = explode(',',$modelsSheduleExam->getList_test_id());
					}else
						throw new Exception("Test id không tồn tại.");
				}
				
				
				// validate data nếu có lỗi thì thông báo trả lại form 
				if($data['isupdate']==1)
					$this->_validate($data,true);
				else 
					$this->_validate($data,false);
					
				if(count($this->_arrError)>0){
					throw new Exception("Loi ");					
				}else{
					
					$this->_sheduleExam  = new Default_Models_SheduleExam();
					$this->_sheduleExam->setOptions($data);
					if(empty($data['id']))
						$this->_sheduleExam->setId(null);	
							
					$idSheduleExam_last_insert =  $this->_sheduleExam->save();					
					
					
					/*
					 * ==================== BEGIN PROCESS LIST TEST(TABLE SHEDULE EXAM), LIST SHEDULE(TABLE TEST)==============
					 */
						/*
						 * Sau khi thêm TEST ID vào SHEDULE EXAM ta thêm id của SHEDULE EXAM vào TEST 
						 * khi còn một id của SHEDULE EXAM  trong TEST ==> không thể xóa BAI TEST đó
						 * 
						 */
						
						// Step 1: trường hợp tạo mới SHEDULE EXAM
						$modelsTest = new Default_Models_Test();
						if($data['isupdate']==0){ // Tạo mới bài test 
							if(count($arr_list_test_id_temp)>0)
							foreach($arr_list_test_id_temp as $key=>$arr_list_test_id_tempItem){
								$modelsTest->find("id",$arr_list_test_id_tempItem);
								if($modelsTest->getId()){
										$getList_shedule_exam  = $modelsTest->getList_shedule_exam();
									if( is_null($getList_shedule_exam) || $getList_shedule_exam == 0 || empty($getList_shedule_exam) ){
										$modelsTest->setList_shedule_exam($idSheduleExam_last_insert);
										//var_dump($getList_test_id); die();
									}
									else
										$modelsTest->setList_shedule_exam($modelsTest->getList_shedule_exam().','.$idSheduleExam_last_insert);
									$modelsTest->save();										
								}
							}
						}
						// Step 2 : trường hợp update shedule_exam

						if($data['isupdate']==1){
							// Tìm ra những bài test được thêm vào shedule exam
							if(count($new_list_test_id)>0)
							foreach ($new_list_test_id as $key=>$new_list_test_idItem){
								if(!in_array($new_list_test_idItem, $old_list_test_id)){
								 	//$list_question_add[$key] = $new_list_questionItem ;
									// Thêm id của shedule exam vào list shedule exam của bai test và cập nhật lại list id đó
								 	$modelsTest->find("id",$new_list_test_idItem);
									if($modelsTest->getId()){
										if(strlen(($modelsTest->getList_shedule_exam()))==0){
											$modelsTest->setList_shedule_exam($data['id']);
										}else{
											$modelsTest->setList_shedule_exam($modelsTest->getList_shedule_exam().",".$data['id']);
										}
											$modelsTest->save();
									}								 	
								 	
								 }	
								 // <-- End Thêm id của bài test vào list id test của question và cập nhật lại list id đó								 	
							}	
							 
							// Tìm ra những test id bị xóa khỏi shedule exam
							if(count($old_list_test_id)>0 && ( !empty($old_list_test_id[0])) )
							foreach ($old_list_test_id as $key=>$old_list_test_idItem){
								if(!in_array($old_list_test_idItem, $new_list_test_id)){
									// Xóa id của shedule exam ra khỏi list id shedule exam của test và cập nhật lại list id đó
								 	$modelsTest->find("id",$old_list_test_idItem);
									if($modelsTest->getId()){
										if(!empty($data['id'])){
											// Kiểm tra xem danh sách shedule exam id có rỗng không
											if(strlen(($modelsTest->getList_shedule_exam()))!=0){
												// Nếu không rỗng ta tách chuỗi ra thành mảng
												$list_shedule_id_temp 	 = explode(',',$modelsTest->getList_shedule_exam());
												if(count($list_shedule_id_temp)>0){
													foreach($list_shedule_id_temp as $key=>$list_shedule_id_tempItem)
														if($list_shedule_id_tempItem==$data['id'])
															unset($list_shedule_id_temp[$key]);
												}
												
												// Cập nhật lại list test id của question và lưu lại
												if(count($list_shedule_id_temp)>0)
												{
													// Re-index array:
													$list_shedule_id_temp = array_values($list_shedule_id_temp);	
													$modelsTest->setList_shedule_exam(implode(',',$list_shedule_id_temp));
												}else
													$modelsTest->setList_shedule_exam("");
												$modelsTest->save();
											}
										}
									}								 	
								 	
								 }	
								 // <-- End Xóa id củashedule exam  ra khỏi list shedule exam id  của test và cập nhật lại list id đó
							}
						}
						
					/*
					 * ==================== END PROCESS LIST TEST(TABLE SHEDULE EXAM), LIST SHEDULE(TABLE TEST)==============
					 */
					
					$this->_redirect("/sheduleexam");
				}
				
			}elseif($request->isGet()) {
					$data = $request->getParams();
					$id = $data['id'];
					if(empty($data['id']))
						$this->_redirect("/sheduleexam");					
					if(!empty($id)){
						$result = $this->_sheduleExam->find('id', $id);
						if($this->_sheduleExam->getId()){
							$Obj = $this->_sheduleExam->toArray() ;
							/*				
							$modelExam = new Default_Models_Exam();
							$modelExam->find("id",$Obj['exam_id']);
							$Obj['course_id']    = $modelExam->getCourse_id();
							*/
							$Obj['isupdate'] = 1;
							$this->view->Obj  = $Obj;
							$this->render("sheduleexam");	 
						}
					}else
					{
						$this->_arrError[] = "ID không tồn tại.";
						throw new Exception("");
					}
						
					
				}else{
					$this->_redirect("error/error");
				}				
			
		}catch(Exception $ex){
			$this->view->arrError = $this->_arrError;
			$this->view->Obj = $data;
			$this->render("sheduleexam");
		}
	}
	
	public function addAction(){
		$this->view->action = "edit" ;
		$this->view->controller = "sheduleexam" ;
		$Obj  = Array();
		$Obj['isupdate'] = 0;  // insert new 
                $Obj['id']=null;
                $Obj['name']='';
                $Obj['course_id']='';
                $Obj['exam_id']='';                
                $Obj['class_id']='';                
                $Obj['time_start']='';                
                $Obj['time_end']='';                
                $Obj['list_test_id']='';                
                $Obj['created_user']='';                
                $Obj['hidden']='';                
                $Obj['note']='';                
                $Obj['count_update_level']='';                
                
                
		$this->view->Obj  = $Obj;
		$this->view->cols_view_title_test = $this->_cols_view_title_test;	
		$this->render("sheduleexam");	 
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
			if(empty($data['name']))  				$this->_arrError[] = "Tên lịch thi trống.";
			//if(empty($data['exam_id'])) 				$this->_arrError[] = "Chưa chọn kỳ thi.";
			if(empty($data['time_start'])) 				$this->_arrError[] = "Thời gian bắt đầu lịch thi trống.";
			if(empty($data['time_end'])) 				$this->_arrError[] = "Thời gian kết thúc lịch thi trống.";
			if(empty($data['class_id'])) 				$this->_arrError[] = "Chưa chọn lớp cho lịch thi.";
				
			// Check trong 1 kỳ thi thì 1 lớp học chỉ có 1 lịch thi duy nhất 
				$modelSheduleExam = new Default_Models_SheduleExam();
				$where = "`exam_id`='".$data['exam_id']."' AND `class_id`='".$data['class_id']."'";
				$result = $modelSheduleExam->fetchAll($where);
				if(count($result)>0){
					if($result[0]->getId() != $data['id']){
						$modelExam = new Default_Models_Exam();
						$modelClass = new Default_Models_Classs();
						$modelExam->find("id",$data['exam_id']);
						$modelClass->find("id",$data['class_id']);
						$this->_arrError[] = " <font style='color: #1484e6; font-weight: bold;' > Trong kỳ thi: ".$modelExam->getFull_name()." - lớp học: ".$modelClass->getFull_name()."  đã được lập lịch thi, bạn không thể tạo lịch thi trong kỳ thi ".$modelExam->getFull_name()." cho lớp ".$modelExam->getFull_name()."  nữa.</font>";
					}
				}
		if($update){// is update 
			if(empty($data['id'])) 					$this->_arrError[] = "ID lịch thi không tồn tại.";			
				
		}else{//is insert 
		}
	}	
	
	
	private function _filter($data) {
			$data['name'] 						= Zend_Filter::filterStatic($data['name'], 'StringTrim');
			$data['time_start']					= Zend_View_Helper_FormatDate::convertMdyToYmd($data['time_start']);
			$data['time_end']					= Zend_View_Helper_FormatDate::convertMdyToYmd($data['time_end']);
			
			// filter cac script
        	$filter 	= new Zend_Filter_StripTags();
        	$data['name'] = $filter->filter($data['name']); 
        	$data['note'] = $filter->filter($data['note']);
        	$data['hidden'] = isset($data['hidden'])?$filter->filter($data['hidden']):0;

		$data['name'] = str_replace('\"', '"',$data['name']);
		$data['name'] = str_replace("\'", "'",$data['name']);        	
		$data['note'] = str_replace('\"', '"',$data['note']);
		$data['note'] = str_replace("\'", "'",$data['note']);        	
		return $data;
	}	
	
	
	
	


	
} 
