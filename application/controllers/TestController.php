<?php
require_once APPLICATION_PATH . '/models/Test.php';
require_once APPLICATION_PATH . '/models/Question.php';
require_once APPLICATION_PATH . '/models/Subject.php';
require_once APPLICATION_PATH . '/models/DoGrade.php';
require_once LIBRARY_PATH.		'/FormatDate.php';
require_once LIBRARY_PATH.		'/lib_str.php';
require_once APPLICATION_PATH . '/models/Teachassignment.php';
require_once 'Zend/Filter/StripTags.php';

require_once APPLICATION_PATH . '/models/Historyofusertest.php';

class TestController extends Zend_Controller_Action
{
	private $_test;
	
	public function init()
	{
        $objSessionNamespace = new Zend_Session_Namespace( 'Zend_Auth' );
		$objSessionNamespace->setExpirationSeconds( 86400 );
		$this->view->controller = "test";
		$this->_helper->layout->setLayout('admin');
		//$this->_helper->layout->setLayout('teacher');
		$this->view->headLink()->appendStylesheet(BASE_URL. '/css/teacher.css');		
		$this->_test 	 = new Default_Models_Test();
		$this->view->jQuery()->addJavascriptFile(BASE_URL . '/js/controller/test/test.js');
		$this->_cols_view       = array("id","title","date_create"); 
				// các cột hiển thị <tên sẽ hiển thị trong head của table>
		$this->_cols_view_title = array("id","Tên đề thi","Ngày tạo");
		
		// Phục vụ việc giữ lại Cái menu đang active ở bên dưới - focus menu
  		$controllerName   = $this->_request->getControllerName();
  		$actionName		  = $this->_request->getActionName();
  		$param			  = $this->_request->getParams();
  		$this->view->controllerName   = $controllerName;
  		$this->view->actionName 	  = $actionName;
  		$this->view->param			  = $param;	  		
  		// Lấy các id môn học mà giảng viên được phân công để hiển thị
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()){
			$userhaslogin = $auth->getStorage()->read();
			$modelsTeachAssign = new Default_Models_Teachassignment();
			$result = $modelsTeachAssign->fetchAll("`user_id`='".$userhaslogin->id."'");
			$arrIdSubject = array();
			if(count($result)>0)
				foreach($result as $resultItem)
					$arrIdSubject[] = $resultItem->getSubject_id();
			$this->view->arrSubjectId			= $arrIdSubject;	  	
			$this->view->userhaslogin_groupID	= $userhaslogin->group_id;
		}
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
				$table_test_sheduleexam   = isset($data['table_test_sheduleexam'])?$data['table_test_sheduleexam']:"";
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
				
				// Nếu view table các đề thi của giảng viên, thì chỉ show những đề thi do người đó tạo ra 
				// Nếu là view table các đề thi trong lập lịch thi thì show những đề thi được mở
				if($table_test_sheduleexam!=1){
					$auth = Zend_Auth::getInstance();
					if ($auth->hasIdentity()){
						$userhaslogin = $auth->getStorage()->read();
						if($userhaslogin->group_id!=5)
							$where .=' AND `user_create`='.$userhaslogin->id; 
					}
				}else
					$where .=' AND `hidden`="on"';
				// End *** view table tesst theo điều kiện 
					
				$where.=" AND 1";
				
				$dataObj    = $this->_test->fetchAll($where,$order,$iDisplayLength,$iDisplayStart);
				$total 		= count($this->_test->fetchAll($where));
				$json_data  = new stdClass();
				$json_data->sEcho = $sEcho;
				$json_data->iTotalRecords = $total;
				$json_data->iTotalDisplayRecords = $total;//count($dataObj);
				$aaData = array();				
				if(count($dataObj)){
					foreach ($dataObj as $dataItem){
						$dataItemArray  = $dataItem->toArray();
						$id  			= $dataItemArray["id"];
						$dataItemArray["date_create"] = Zend_View_Helper_FormatDate::convertYmdToMdy($dataItemArray["date_create"]);
						$tmpArr 		= array();
						$strAction    = '<span class="view-test-for-hidden"><a class="view-icon"   href="'. BASE_URL .'/'. $data["controller"] .'/onlypreview/testID/'.  $id .'" onclick="previewTestPopup(this.href); return false;"><img class="fugue fugue-eye" alt="" src="'. BASE_URL .'/img/icons/space.gif"/></a></span>&nbsp;&nbsp;';
						$strAction   .= '<a class="edit-icon"   href="'. BASE_URL .'/'. $data["controller"] .'/create/testID/'.  $id .'/selectedTab/editTest"><img class="fugue fugue-pencil" alt="Chỉnh sửa" src="'. BASE_URL .'/img/icons/space.gif"/></a>&nbsp;&nbsp;';
						$strAction   .= '<a class="remove-icon" href="'. BASE_URL .'/'. $data["controller"] .'/delete/id/'.$id.'"  onclick="deleteClick(this,this.href); return false;"><img class="fugue fugue-cross"  alt="Xóa"       src="'. BASE_URL .'/img/icons/space.gif"/></a>&nbsp;&nbsp;'; 
						//$strAction   .= '<a class="remove-icon" href="'. BASE_URL .'/'. $data["controller"] .'/exporttopdf/testID/'.$id.'"><img class="fugue fugue-printer--arrow"  alt="Xuất ra PDF" title="Xuất ra PDF" src="'. BASE_URL .'/img/icons/space.gif"/></a>&nbsp;&nbsp;';
						$strAction   .= '<a class="remove-icon" href="'. BASE_URL .'/'. $data["controller"] .'/exporttodoc/testID/'.$id.'"><img class="fugue fugue-book-open-next"  alt="Xuất ra Word" title="Xuất ra Word" src="'. BASE_URL .'/img/icons/space.gif"/></a>';
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
	
	public function exporttopdfAction(){
//		$this->_helper->layout->setLayout('studentdotest');
		$this->view->jQuery()->addJavascriptFile(BASE_URL . '/js/test.do.js');
		$this->view->jQuery()->addJavascriptFile(BASE_URL . '/js/controller/test/test.js');
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
					$data = $request->getParams();
					$data = $this->_filterTest($data);
			}elseif($request->isGet()) {
				$data = $request->getParams();
					$this->view->review_test        = 0;
					$this->view->view_corect        = 0;
					$this->view->view_feedback 		= 0; 
					$this->view->view_send_result   = 0;
					$models_test = new Default_Models_Test();
					$models_test->find("id",$data["testID"]);
					if($models_test->getId()){
						$list_score    				= explode(',',$models_test->getList_score());
						$data["question_id"] 	= explode(',', $models_test->getList_question());
						$data["list_score_in_test_table"]= $list_score;
						$data["question_perpage"] = $models_test->getQuestion_perpage();
						$data["obj_test"] = $models_test->toArray();
						$modelSubject = new Default_Models_Subject();
						$modelSubject->find("id",$models_test->getSubject_id());
						if($modelSubject->getId())
							$data["obj_subject"] = $modelSubject->toArray();
						
					}
					
					$data["question_score"] = array();
					$this->view->data     = $data;
			}
		}catch(Exception $ex){
			$this->_arrError[] = $ex->getMessage();  
			$this->view->arrError = $this->_arrError; 
		}				
	}
	
	public function exporttodocAction(){
		$this->view->jQuery()->addJavascriptFile(BASE_URL . '/js/test.do.js');
		$this->view->jQuery()->addJavascriptFile(BASE_URL . '/js/controller/test/test.js');
		//$this->_helper->layout->disableLayout();
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
					$data = $request->getParams();
					$data = $this->_filterTest($data);
			}elseif($request->isGet()) {
				$data = $request->getParams();
					$this->view->review_test        = 0;
					$this->view->view_corect        = 0;
					$this->view->view_feedback 		= 0; 
					$this->view->view_send_result   = 0;
					$models_test = new Default_Models_Test();
					$models_test->find("id",$data["testID"]);
					if($models_test->getId()){
						$list_score    				= explode(',',$models_test->getList_score());
						$data["question_id"] 	= explode(',', $models_test->getList_question());
						$data["list_score_in_test_table"]= $list_score;
						$data["question_perpage"] = $models_test->getQuestion_perpage();
						$data["obj_test"] = $models_test->toArray();
						$modelSubject = new Default_Models_Subject();
						$modelSubject->find("id",$models_test->getSubject_id());
						if($modelSubject->getId())
							$data["obj_subject"] = $modelSubject->toArray();
						
					}
					
					$data["question_score"] = array();
					$this->view->data     = $data;
			}
		}catch(Exception $ex){
			$this->_arrError[] = $ex->getMessage();  
			$this->view->arrError = $this->_arrError; 
		}				
	} 
	
	public function createdocAction(){
		$this->_helper->layout->disableLayout();
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
					$data = $request->getParams();
					$data['html_submit'] = str_replace('\"', '"',$data['html_submit']);
					$data['html_submit'] = str_replace("\'", "'",$data['html_submit']);
					$this->view->data     = $data;
			}elseif($request->isGet()) {
					//throw new Exception("Lỗi Phương thức");
			}
		}catch(Exception $ex){
			$this->_arrError[] = $ex->getMessage();  
			$this->view->arrError = $this->_arrError; 
		}					
	}
	
	public function addAction(){
		$this->_redirect("/test/create");
	}
	
	public function createAction(){
		
		$this->view->jQuery()->addJavascriptFile(BASE_URL . '/js/cms.js');
		$this->view->headLink()->appendStylesheet(BASE_URL . '/css/cms.css');
		
		$this->view->jQuery()->addJavascriptFile(BASE_URL . '/js/controller/question/index.js');
		$this->view->headLink()->appendStylesheet(BASE_URL . '/css/controller/question/index.css');
		$this->view->cols_view_title = array("id","Tiêu đề","Người tạo","Ngày tạo", "Loại");
		
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
				try{
					$data = $request->getParams();
					$data = $this->_filter($data);
					if(empty($data['subject_id']))
						throw new Exception("subject_id_null");
						// Gán lại list_question mà client đẩy lên cho biến tạm, để phục vụ cập nhật trạng thái câu hỏi đã được thêm vào bài test
						$data["list_question"]	= isset($data["list_question"])?$data["list_question"]:array();
						$new_list_question	= $data["list_question"];
						if(!is_array($new_list_question))
							$new_list_question = array();
						if($data['isupdate']==1){ 
							if(!empty($data['id'])){
								$modelsTest = new Default_Models_Test();
								$modelsTest->find('id', $data['id']);
								if($modelsTest->getId()){
									$old_list_question 	 = explode(',',$modelsTest->getList_question());
									$data['user_create']  = $modelsTest->getUser_create();
									$data['date_modify'] = date("Y-m-d H:i:s");
									$data['date_create'] = date("Y-m-d H:i:s");
									
								}
							}else
								throw new Exception("Không tồn tại câu hỏi này.");
						}
					// check list score is number và xóa khoảng trắng 2 bên
					$temp = array();
					if(isset($data["list_score"]) && count($data["list_score"])>0){					
						foreach($data["list_score"] as $key=>$questionScoreItem){
							$temp[$key] = trim($questionScoreItem);
							if(!is_numeric($temp[$key]))
								throw new Exception("Điểm của câu hỏi phải là số.");
						}
						$data["list_score"] = implode(',', $temp);
					}else $data["list_score"]='';
					
					// Xóa khoảng trắng và check id question có phải là số không
					$temp_ques_id = array();
					// Gom các id của question thành 1 list -> save vào database					
					if(count($data["list_question"])>0){	
						foreach($data["list_question"] as $key=>$list_questionItem){
								$temp_ques_id[$key] = trim($list_questionItem);
								if(!is_numeric($temp_ques_id[$key]))
									throw new Exception("ID ".$list_questionItem." question không tồn tại.");
							}
						$data["list_question"] = implode(',', $temp_ques_id);
					}else	$data["list_question"] = '';
						
					// định dạng lại ngày tháng phù hợp với kiểu datetime của mysql			
					//$data['date_create'] 	= Zend_View_Helper_FormatDate::convertMdyToYmd($data['date_create']);
					//$data['date_modify'] 	= Zend_View_Helper_FormatDate::convertMdyToYmd($data['date_modify']);
						if($data['isupdate']==0){ // Tạo mới bài test
							$data['date_create'] = date("Y-m-d G:i:s");
							$data['date_modify'] = date("Y-m-d G:i:s");
							$auth = Zend_Auth::getInstance();
							if ($auth->hasIdentity()){
								$userhaslogin = $auth->getStorage()->read();
								$data['user_create']  = $userhaslogin->id;
								}						
						}
						if(empty($data['hidden']))
							$data['hidden'] = 'off';	
						$this->_test->setOptions($data);
						if(empty($data['id']))
							$this->_test->setId(null);
						// save to database 
						$id_last_insert_test = $this->_test->save();
						if(empty($id_last_insert_test))
							$id_last_insert_test = $data['id'];
						/*
						 * Sau khi thêm câu hỏi vào test ta thêm id của bài test vào question 
						 * khi còn một id của bài test trong question ==> không thể xóa question đó
						 * 
						 */
						
						// Step 1: trường hợp tạo mới test
						$modelsQuestion = new Default_Models_Question();
						if($data['isupdate']==0){ // Tạo mới bài test 
							if(count($new_list_question)>0)
							foreach($new_list_question as $key=>$new_list_questionItem){
								$modelsQuestion->find("id",$new_list_questionItem);
								if($modelsQuestion->getId()){
										$getList_test_id  = $modelsQuestion->getList_test_id();
									if( is_null($getList_test_id) || $getList_test_id == 0 || empty($getList_test_id) ){
										$modelsQuestion->setList_test_id($id_last_insert_test);
										//var_dump($getList_test_id); die();
									}
									else
										$modelsQuestion->setList_test_id($modelsQuestion->getList_test_id().','.$id_last_insert_test);
									$modelsQuestion->save();										
								}
							}
						}
						
						// Step 2 : trường hợp update test
						$list_question_remove = array(); // Những Question bị xóa khỏi bài test
						$list_question_add    = array(); // Những Question được thêm vào bài test

						if($data['isupdate']==1){
							// Tìm ra những câu hỏi được thêm vào bài test
							if(count($new_list_question)>0)
							foreach ($new_list_question as $key=>$new_list_questionItem){
								if(!in_array($new_list_questionItem, $old_list_question)){
								 	//$list_question_add[$key] = $new_list_questionItem ;
									// Thêm id của bài test vào list id test của question và cập nhật lại list id đó
								 	$modelsQuestion->find("id",$new_list_questionItem);								 	
									if($modelsQuestion->getId()){
										if(strlen(($modelsQuestion->getList_test_id()))==0){
											$modelsQuestion->setList_test_id($data['id']);
										}else{
											//$list_test_id_temp_in_add	 = explode(',',$modelsQuestion->getList_test_id()); 
											$modelsQuestion->setList_test_id($modelsQuestion->getList_test_id().",".$data['id']);
										}
											$modelsQuestion->save();
									}								 	
								 	
								 }	
								 // <-- End Thêm id của bài test vào list id test của question và cập nhật lại list id đó								 	
							}	
							 
							// Tìm ra những câu hỏi bị xóa khỏi bài test
							if(count($old_list_question)>0)
							foreach ($old_list_question as $key=>$old_list_questionItem){
								if(!in_array($old_list_questionItem, $new_list_question)){
								 	//$list_question_remove[$key] = $old_list_questionItem ;
									// Xóa id của bài test ra khỏi list id test của question và cập nhật lại list id đó
								 	$modelsQuestion->find("id",$old_list_questionItem);
									if($modelsQuestion->getId()){
										if(!empty($data['id'])){
											// Kiểm tra xem danh sách test id có rỗng không
											if(strlen(($modelsQuestion->getList_test_id()))!=0){
												// Nếu không rỗng ta tách chuỗi ra thành mảng
												$list_test_id_temp 	 = explode(',',$modelsQuestion->getList_test_id());
												if(count($list_test_id_temp)>0){
													foreach($list_test_id_temp as $key=>$list_test_id_tempItem)
														if($list_test_id_tempItem==$data['id'])
															unset($list_test_id_temp[$key]);
												}
												// Cập nhật lại list test id của question và lưu lại
												if(count($list_test_id_temp)>0)
												{
													// Re-index array:
													$list_test_id_temp = array_values($list_test_id_temp);	
													$modelsQuestion->setList_test_id(implode(',',$list_test_id_temp));
												}else
													$modelsQuestion->setList_test_id("");
												$modelsQuestion->save();
											}
										}
									}								 	
								 	
								 }	
								 // <-- End Xóa id của bài test ra khỏi list id test của question và cập nhật lại list id đó
							}
						}
						
						/*
						echo "<br>list question cũ: "; var_dump($old_list_question);
						echo "<br>list question Mới: "; var_dump($new_list_question);
						echo "<br>list question remove: "; var_dump($list_question_remove);
						echo "<br>list question add: "; var_dump($list_question_add);
						*/
						
						$json_data = array("success"=>true,"id_last_insert_test"=>$id_last_insert_test, "isupdate"=>"1");
						}catch (Exception  $ex){
							$json_data = array("success"=>false,"error"=>$ex->getMessage());
						}
						echo Zend_Json::encode($json_data);
						die();							
				
			}elseif ($request->isGet()){
				$data = $request->getParams();
				$selectedTab   = isset($data["selectedTab"])?$data["selectedTab"]:1;
				$this->view->selectedTab  = $selectedTab;
				$testID = isset($data['testID'])?$data['testID']:0;
				$modelsTest = new Default_Models_Test();
				if(!empty($testID)){
					$modelsTest->find('id', $testID);
					if($modelsTest->getId()){
					// BEGIN Xử lý không cho 1 user edit đề thi của user khác
					
					$auth = Zend_Auth::getInstance();
					if ($auth->hasIdentity()){
						$userhaslogin = $auth->getStorage()->read();
						if($userhaslogin->group_id != 5)
							if($modelsTest->getUser_create() != $userhaslogin->id)
								throw new Exception("Bạn không được phép chỉnh sửa đề thi của người khác.");
					}
					// END Xử lý không cho 1 user edit đề thi của user khác					
						
						$this->view->test = $modelsTest->toArray();
						$this->view->test["isupdate"] = 1;	
					}else
					throw new Exception('id không tồn tại');
				}else{
					//$modelsTest->setSubject_id(6) ;
					$modelsTest->setTitle("Tiêu đề đề thi") ;
					$modelsTest->setContent("Nội dung đề thi");
					$modelsTest->setDuration_test(60) ;
					$modelsTest->setGrade_method(1);
					$modelsTest->setShuffle_question(0);
					$modelsTest->setShuffle_answer(0);
					$modelsTest->setQuestion_perpage(10) ;
					$modelsTest->setDecimal_digits_in_grades(2) ;
					$modelsTest->setList_question("") ;
					$modelsTest->setList_score("") ;
					$modelsTest->setReview_after_test("0000") ;
					$modelsTest->setReview_while_test_open("0000") ;
					$modelsTest->setReview_after_test_close("0000") ;
					$this->view->test = $modelsTest->toArray(); 					
					$this->view->test["isupdate"] = 0;
				}								
			}
		}catch (Exception  $ex){
			$this->_arrError[] = $ex->getMessage();  
			$this->view->arrError = $this->_arrError; 			
		}
	}

	public function onlypreviewAction(){
		$this->view->jQuery()->addJavascriptFile(BASE_URL . '/js/controller/test/test.js');
			$request = $this->getRequest();
			if ($request->isPost()) {
					$data = $request->getParams();
					$this->view->review_test        = 0;
					$this->view->view_corect        = 1;
					$this->view->view_feedback 		= 1; 
					$this->view->view_send_result   = 1;
					$models_test = new Default_Models_Test();
					$models_test->find("id",$data["testID"]);
					if($models_test->getId()){
						$list_score    		= explode(',',$models_test->getList_score());
						$data["list_score_in_test_table"]= $list_score;
						$data["question_perpage"] = $models_test->getQuestion_perpage();
					}
					$question_id    = $data["question_id"]; // array
					$answer_of_user = $data["question"];    // array
                                        $data["obj_test"] = $models_test->toArray();
					$total_score = 0;
					if(count($question_id)>0){
						foreach ($question_id as $key=>$question_idItem){
							if(!empty($answer_of_user[$question_idItem])){ 
								// người dùng có thao tác đến question này
								// với true false,multichoice nếu ko check thì không đẩy lên post
								// đối với matching,completion,short ans,essay test luôn luôn đẩy lên post
								$obj =  DoGrade::_DoGrade($list_score[$key],$question_idItem,$answer_of_user[$question_idItem]);
								$total_score+= $obj->num_score;
								$data["question_score"][$key] = $obj->num_score;
							}else{
								$data["question_score"][$key] = 0;
							}
						}						  	
					}		
					$this->view->data     = $data;
				
			}elseif($request->isGet()) {
				$this->view->jQuery()->addJavascriptFile(BASE_URL . '/js/test.do.js');
				$this->view->jQuery()->addJavascriptFile(BASE_URL . '/js/controller/test/test.js');
				
				$data = $request->getParams();
				$test_id = $data["testID"];
				
					$data = $request->getParams();
					$this->view->review_test        = 0;
					$this->view->view_corect        = 0;
					$this->view->view_feedback 		= 0; 
					$this->view->view_send_result   = 1;
					$models_test = new Default_Models_Test();
					$models_test->find("id",$data["testID"]);
					if($models_test->getId()){
						$list_score    				= explode(',',$models_test->getList_score());
						$data["question_id"] 	= explode(',', $models_test->getList_question());
						$data["list_score_in_test_table"]= $list_score;
						$data["question_perpage"] = $models_test->getQuestion_perpage();
						$data["obj_test"] = $models_test->toArray();
						$modelSubject = new Default_Models_Subject();
						$modelSubject->find("id",$models_test->getSubject_id());
						if($modelSubject->getId())
							$data["obj_subject"] = $modelSubject->toArray();
					}
					
					$data["question_score"] = array();
					/*  
					$total_score = 0;
					if(count($question_id)>0){
						foreach ($question_id as $key=>$question_idItem){
							if(!empty($answer_of_user[$question_idItem])){ 
								// người dùng có thao tác đến question này
								// với true false,multichoice nếu ko check thì không đẩy lên post
								// đối với matching,completion,short ans,essay test luôn luôn đẩy lên post
								$obj =  DoGrade::_DoGrade($list_score[$key],$question_idItem,$answer_of_user[$question_idItem]);
								$total_score+= $obj->num_score;
								$data["question_score"][$key] = $obj->num_score;
							}else{
								$data["question_score"][$key] = 0;
							}
						}						  	
					}*/		
					$this->view->data     = $data;
					$this->view->hasClock = true;
			}						
		
		
	}
	
	public function reviewtestAction(){
		$this->view->jQuery()->addJavascriptFile(BASE_URL . '/js/controller/test/test.js');
		$this->_helper->layout->setLayout('reviewtest-teacher');
		//$this->_helper->layout->disableLayout();
			$request = $this->getRequest();
			if ($request->isPost()) {
					$data = $request->getParams();
					$this->view->review_test        = 0;
					$this->view->view_corect        = 1;
					$this->view->view_feedback 		= 1; 
					$this->view->view_send_result   = 1;
					$models_test = new Default_Models_Test();
					$models_test->find("id",$data["testID"]);
					if($models_test->getId()){
						$list_score    		= explode(',',$models_test->getList_score());
						$data["list_score_in_test_table"]= $list_score;
						$data["question_perpage"] = $models_test->getQuestion_perpage();
					}
					
					$question_id    = $data["question_id"]; // array
					$answer_of_user = $data["question"];    // array
					$total_score = 0;
					if(count($question_id)>0){
						foreach ($question_id as $key=>$question_idItem){
							if(!empty($answer_of_user[$question_idItem])){ 
								// người dùng có thao tác đến question này
								// với true false,multichoice nếu ko check thì không đẩy lên post
								// đối với matching,completion,short ans,essay test luôn luôn đẩy lên post
								$obj =  DoGrade::_DoGrade($list_score[$key],$question_idItem,$answer_of_user[$question_idItem]);
								$total_score+= $obj->num_score;
								$data["question_score"][$key] = $obj->num_score;
							}else{
								$data["question_score"][$key] = 0;
							}
						}						  	
					}		
					$this->view->data     = $data;
				
			}elseif($request->isGet()) {
				$data = $request->getParams();
				$test_id = $data["testID"];
					$this->view->review_test        = 0;
					$this->view->view_corect        = 0;
					$this->view->view_feedback 		= 0; 
					$this->view->view_send_result   = 1;
					$models_test = new Default_Models_Test();
					$models_test->find("id",$data["testID"]);
					if($models_test->getId()){
						$list_score    				= explode(',',$models_test->getList_score());
						$data["question_id"] 	= explode(',', $models_test->getList_question());
						$data["list_score_in_test_table"]= $list_score;
						$data["question_perpage"] = $models_test->getQuestion_perpage();
					}
					
					$data["question_score"] = array();
					/*  
					$total_score = 0;
					if(count($question_id)>0){
						foreach ($question_id as $key=>$question_idItem){
							if(!empty($answer_of_user[$question_idItem])){ 
								// người dùng có thao tác đến question này
								// với true false,multichoice nếu ko check thì không đẩy lên post
								// đối với matching,completion,short ans,essay test luôn luôn đẩy lên post
								$obj =  DoGrade::_DoGrade($list_score[$key],$question_idItem,$answer_of_user[$question_idItem]);
								$total_score+= $obj->num_score;
								$data["question_score"][$key] = $obj->num_score;
							}else{
								$data["question_score"][$key] = 0;
							}
						}						  	
					}*/		
					$this->view->data     = $data;
			}						
	}

	public function testonequestionAction(){
		try{
			$request = $this->getRequest();
			if ($request->isPost()) {
				$data = $request->getParams();
				$question_id    = $data["question_id"];
				$answer_of_user = isset($data["answer_of_user"])?$data["answer_of_user"]:array(); // is array of answer of user
				$test_id        = isset($data["testID"])?$data["testID"]:0;			
				$question_title_order       = $data["question_title_order"];

				if(!empty($test_id)){
					// Đoạn này để lấy score mà user custom ở một bài test
					$models_test = new Default_Models_Test();
					$models_test->find("id",$test_id );
					// Tìm trong list score với id của câu hỏi tương ứng
					if ($models_test->getId())
					{
						$list_questionID 	= explode(',', $models_test->getList_question());
						$list_score    		= explode(',',$models_test->getList_score());
						if(!empty($list_questionID[0]))		
								foreach($list_questionID as $key=>$list_questionIDItem){ 
									if(trim($list_questionIDItem)==trim($question_id)){
										$question_score = $list_score[$key];
										break;
									}
							}
						
					}
				}
				
				/*
				 * Nếu không có test id tức là preview câu hỏi trong cms
				 * Lúc đó $question_score: sẽ là giá trị default của question đó
				 */
				// Tính điểm câu hỏi
                                $question_score = isset($question_score)?$question_score:1;
				$obj =  DoGrade::_DoGrade($question_score,$question_id,$answer_of_user);

				// Trả kết quả xuống view với những option tương ứng
				$this->view->review_test        = 0;
				$this->view->view_corect        = 1;
				$this->view->view_feedback 		= 1; 
				$this->view->view_send_result   = 1;
				$this->view->question_score     = $obj->num_score;
				$this->view->answer_of_user     = $answer_of_user;
				$this->view->test_id 			= $test_id;
				$this->view->question_id 		= $question_id;	
				$this->view->question_title_order 	= $question_title_order;	
				$this->view->score_of_question 	= $question_score;
				$this->_helper->layout->disableLayout();			
				
			}elseif($request->isGet()){
				
			}
		}catch (Exception $ex){
			echo $ex->getMessage(); die();
		}
	}	

	public function deleteAction(){
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
				$data = $request->getParams();
				$test_ids = $data['id'];
				if(!is_array($test_ids))
					$test_ids = array($test_ids);					
				if(count($test_ids))
				foreach($test_ids as $test_idsItem){
					$this->_test = new Default_Models_Test();
					$this->_test->find("id",$test_idsItem);
					if($this->_test->getId()){
						// BEGIN Xử lý không cho 1 user xóa đề thi của user khác
						$auth = Zend_Auth::getInstance();
						if ($auth->hasIdentity()){
							$userhaslogin = $auth->getStorage()->read();
							if($userhaslogin->group_id != 5)
								if($this->_test->getUser_create()!= $userhaslogin->id)
									throw new Exception("Bạn không thể xóa đề thi của người khác.");
						}
						// END Xử lý không cho 1 user xóa đề thi của user khác					
						
						if(strlen($this->_test->getList_shedule_exam()!= 0) ){
							throw new Exception("Đề thi này đã được thêm vào lịch thi, bạn không thể xóa.");						
						}
						// Xóa id của test trong list test id của những question
						$list_question_need_delete_test_id = explode(",",$this->_test->getList_question());
						if(count($list_question_need_delete_test_id)>0)
						foreach($list_question_need_delete_test_id as $list_question_need_delete_test_idItem){
							$modelQuestion = new Default_Models_Question();
							$modelQuestion->find("id",$list_question_need_delete_test_idItem);
							if($modelQuestion->getId())
							{
								$list_test_id_in_question = explode(",",$modelQuestion->getList_test_id());
								if(in_array($test_idsItem,$list_test_id_in_question))
								foreach($list_test_id_in_question as $key=>$list_test_idItem)
									if($list_test_idItem==$test_idsItem)
									 	unset($list_test_id_in_question[$key]);
																	 	
								$modelQuestion->setList_test_id(implode(',',$list_test_id_in_question));
								$modelQuestion->save();
							}
						}
						$this->_test->delete('id', $test_idsItem);
					}
					else
						throw new Exception("Test id = ".$test_idsItem." not exists.");
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

	// Chấm điểm cả bài test -- đã join vào reviewtest
	public function gradingtestAction(){
	try{
			$request = $this->getRequest();
			if ($request->isPost()) {
					$data = $request->getParams();
					$this->view->review_test        = 0;
					$this->view->view_corect        = 1;
					$this->view->view_feedback 		= 1; 
					$this->view->view_send_result   = 1;
					$models_test = new Default_Models_Test();
					$models_test->find("id",$data["test_id"]);
					if($models_test->getId()){
						$list_score    		= explode(',',$models_test->getList_score());
						$data["list_score_in_test_table"]= $list_score;
						$data["question_perpage"] = $models_test->getQuestion_perpage();
					}
					
					$question_id    = $data["question_id"]; // array
					$answer_of_user = $data["question"];    // array
					$total_score = 0;
					if(count($question_id)>0){
						foreach ($question_id as $key=>$question_idItem){
							if(!empty($answer_of_user[$question_idItem])){ 
								// người dùng có thao tác đến question này
								// với true false,multichoice nếu ko check thì không đẩy lên post
								// đối với matching,completion,short ans,essay test luôn luôn đẩy lên post
								$obj =  DoGrade::_DoGrade($list_score[$key],$question_idItem,$answer_of_user[$question_idItem]);
								$total_score+= $obj->num_score;
								$data["question_score"][$key] = $obj->num_score;
							}else{
								$data["question_score"][$key] = 0;
							}
						}						  	
					}		
					$this->view->data     = $data;
					
					
			}elseif($request->isGet()){
				throw new Exception("Phương thức sai.");
			}
		}catch (Exception $ex){
			echo "Lỗi hoặc phương thức sai".$ex->getMessage();
			die();
		}
	}
	
	public function autogeneratetestAction(){
			$this->view->headLink()->appendStylesheet(BASE_URL . '/css/controller/test/gentest.css');
				
			$this->view->controller = "autogeneratetest";
			try {
			$request = $this->getRequest();
			if ($request->isPost()) {
				$data = $request->getParams();
				$ListChapterId 		= $data["chapter_subject_id"];
				$subject_id 		= $data["subject_id"];

				$ListLevelFrom		 	= $data["levelFrom"];
				$ListLevelTo		 	= $data["levelTo"];
				$ListClassificationFrom		 = $data["classificationFrom"];
				$ListClassificationTo		 = $data["classificationTo"];
				
				$ListTypeQuestion 		= $data["SltTypeQuestion"];
				$ListAmountQuestion = $data["amount_question"];
				if(count($data["amount_question"])>0){					
						foreach($data["amount_question"] as $key=>$amount_questionItem){
							if(!is_numeric(trim($amount_questionItem)))
								throw new Exception("Số lượng câu hỏi phải là số.");
						}
				}				
				
				$result_test = array();
				
				if (count($ListChapterId)>0 ){
					foreach($ListChapterId as $key=>$ListChapterIdItem){
						$where = "";
						if(!empty($ListChapterIdItem) && is_numeric(trim($ListChapterIdItem)))
							$where .= "`chapter_id`=".$ListChapterIdItem;	
						elseif(!empty($subject_id) && is_numeric(trim($subject_id)))
							$where .= "`subject_id`=".$subject_id;							

						if(!empty($ListTypeQuestion[$key]))	
							$where .= " AND `type_question`=".$ListTypeQuestion[$key];
							
						//if(!empty($ListLevel[$key]))	
							//$where .= " AND `level`=".$ListLevel[$key];
						if(!empty($ListLevelFrom[$key]) && empty($ListLevelTo[$key]))	
							$where .= " AND `level`<=".$ListLevelFrom[$key];
						if(empty($ListLevelFrom[$key]) && !empty($ListLevelTo[$key]))	
							$where .= " AND `level`>=".$ListLevelTo[$key];
						if(!empty($ListLevelFrom[$key]) && !empty($ListLevelTo[$key]))	
							$where .= " AND `level` BETWEEN ".$ListLevelTo[$key]." AND ".$ListLevelFrom[$key];

						if(!empty($ListClassificationFrom[$key]) && empty($ListClassificationTo[$key]))	
							$where .= " AND `classification`>=".$ListClassificationFrom[$key];
						if(empty($ListClassificationFrom[$key]) && !empty($ListClassificationTo[$key]))	
							$where .= " AND `classification`<=".$ListClassificationTo[$key];
						if(!empty($ListClassificationFrom[$key]) && !empty($ListClassificationTo[$key]))	
							$where .= " AND `classification` BETWEEN ".$ListClassificationFrom[$key]." AND ".$ListClassificationTo[$key];							
							
						//	var_dump($where);
						if(!empty($where)){
							$models_question = new Default_Models_Question();
							$result_row  = $models_question->fetchAll($where);
						}
						
						$total  = count($result_row);
						if($total > 0){
							// hàm array_rand($arr,$num) lấy ngẫu nhiên từ mảng $arr $num phần tử
							// nhưng nó lại return lại cái key của mảng arr thôi
							if($ListAmountQuestion[$key]>count($result_row))
								$numSelect = count($result_row);
							else 
								$numSelect = $ListAmountQuestion[$key];
								
							$result_row_key  = array_rand($result_row, $numSelect);
							// Khi mà $numSelect == 1 có nghĩa là chỉ phát sinh radom 1 câu hỏi thì hàm array_rand trả về 
							// 1 số kiểu int chứ không phải array => ta phải ép nó vào array  
							if($numSelect==1) $result_row_key = (Array('0'=>$result_row_key));
							$row  			 = array();
							if(count($result_row_key)>0)
								foreach($result_row_key as $result_row_keyItem)
									$row[]   	 = $result_row[$result_row_keyItem]->getId();
							$result_test["list_question"][]  = $row;
							$result_test["total_question"][] = $total;
							$result_test["amount_question"][] = $ListAmountQuestion[$key];
							$result_test["question_perpage"] = $data["question_perpage"];
							$result_test["duration_test"] =   $data["duration_test"];
						}else
						{
							$result_test["list_question"][] = 0;
							$result_test["total_question"][] = 0;
							$result_test["amount_question"][] =$ListAmountQuestion[$key];
							$result_test["question_perpage"] = $data["question_perpage"];
							$result_test["duration_test"] =   $data["duration_test"];
						} 
					}
					
				}
				$json_data = array("success"=>true,"result"=>$result_test);
				echo Zend_Json::encode($json_data); 
				die();					
			}elseif ($request->isGet()){
				$auth = Zend_Auth::getInstance();
				if ($auth->hasIdentity()){
					$userhaslogin = $auth->getStorage()->read();
					$roleUser = $userhaslogin->group_id;
					//if($roleUser==3 ){
						$modelTeachAssign = new Default_Models_Teachassignment();
						$resultTeach =  $modelTeachAssign->fetchAll("`user_id`='".$userhaslogin->id."'");
						$ListSubjectId = array();
						if(count($resultTeach)>0)
						foreach ($resultTeach as $keyTeach=>$resultTeachItem){
							//$resultTeachItem = new Default_Models_Teachassignment();
								$ListSubjectId[]=$resultTeachItem->getSubject_id();
						}
					//}
				}
					$modelsTest = new Default_Models_Test();
					$modelsTest->setTitle("Tiêu đề đề thi") ;
					$modelsTest->setContent("Nội dung đề thi");
					if(!empty($ListSubjectId)){
						$modelsTest->setSubject_id($ListSubjectId[0]);
					}
					$modelsTest->setDuration_test(60) ;
					$modelsTest->setGrade_method(1);
					$modelsTest->setShuffle_question(0);
					$modelsTest->setShuffle_answer(0);
					$modelsTest->setQuestion_perpage(10) ;
					$modelsTest->setDecimal_digits_in_grades(2) ;
					$modelsTest->setList_question("") ;
					$modelsTest->setList_score("") ;
					$modelsTest->setReview_after_test("0000") ;
					$modelsTest->setReview_while_test_open("0000") ;
					$modelsTest->setReview_after_test_close("0000") ;
					$this->view->test = $modelsTest->toArray(); 	
			}
				
		}
		catch (Exception $ex) {
			$json_data = array("success"=>false,"error"=>$ex->getMessage());
			echo Zend_Json::encode($json_data); 
			die();					
			
		}		
	}
	
	public function reviewhistorytestAction(){
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
				$data = $request->getParams();
				
			}elseif($request->isGet()) {
					$data = $request->getParams();
					$his_test_id = $data["his_test_id"];
					$modelsHisTest = new Default_Models_Historyofusertest();
					$modelsHisTest->find("id",$his_test_id) ;
					if($modelsHisTest->getId())
					{
					$this->view->review_test        = 0;
					$this->view->view_corect        = 1;
					$this->view->view_feedback 		= 1; 
					$this->view->view_send_result   = 1;
					/*
					$models_test = new Default_Models_Test();
					$models_test->find("id",$data["testID"]);
					if($models_test->getId()){
						$list_score    		= explode(',',$models_test->getList_score());
						$data["list_score_in_test_table"]= $list_score;
						$data["question_perpage"] = $models_test->getQuestion_perpage();
					}
					*/
					
					$data["question_id"]    = Zend_Json::decode($modelsHisTest->getList_question_id()) ; // array
					$data["question"] = Zend_Json::decode($modelsHisTest->getList_answer_of_user()) ;    // array
					/*
					$total_score = 0;
					if(count($question_id)>0){
						foreach ($question_id as $key=>$question_idItem){
							if(!empty($answer_of_user[$question_idItem])){ 
								// người dùng có thao tác đến question này
								// với true false,multichoice nếu ko check thì không đẩy lên post
								// đối với matching,completion,short ans,essay test luôn luôn đẩy lên post
								$obj =  DoGrade::_DoGrade($list_score[$key],$question_idItem,$answer_of_user[$question_idItem]);
								$total_score+= $obj->num_score;
								$data["question_score"][$key] = $obj->num_score;
							}else{
								$data["question_score"][$key] = 0;
							}
						}						  	
					}	
					*/
					$data["question_score"] = Zend_Json::decode($modelsHisTest->getList_score_of_question());
					$data["question_perpage"] = 10 ; // gắn tạm	
					$this->view->data     = $data;				
						
					}
					
			}
		}catch(Exception $ex){
			echo $ex->getMessage();  
		}		
		
	}
	
	private function _filter($data) {
			$data['title'] 					= Zend_Filter::filterStatic($data['title'], 'StringTrim');
			$filter 	= new Zend_Filter_StripTags();
        	$data['title'] = $filter->filter($data['title']); 
			
			/*
			$data['password'] 					= Zend_Filter::filterStatic($data['password'], 'StringTrim');
			$data['firstname'] 					= Zend_Filter::filterStatic($data['firstname'], 'StringTrim');
			$data['lastname'] 					= Zend_Filter::filterStatic($data['lastname'], 'StringTrim');
			// filter cac script
        	$filter 	= new Zend_Filter_StripTags();
        	$data['username'] = $filter->filter($data['username']); 
			$data['firstname'] = $filter->filter($data['firstname']);
			$data['lastname'] = $filter->filter($data['lastname']);
			$data['department'] = $filter->filter($data['department']);
			$data['email'] = $filter->filter($data['email']);
			$data['yahoo'] = $filter->filter($data['yahoo']);
				*/
			if($data['duration_test']==0)
				$data['duration_test'] = 1;
				return $data;
	}	
	
	
}



 