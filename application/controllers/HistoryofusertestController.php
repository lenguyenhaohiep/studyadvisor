<?php
require_once APPLICATION_PATH . '/models/Historyofusertest.php';
require_once APPLICATION_PATH . '/models/Sheduleexam.php';
require_once APPLICATION_PATH . '/models/User.php';
require_once APPLICATION_PATH . '/models/Test.php';
require_once APPLICATION_PATH . '/models/Classs.php';
require_once APPLICATION_PATH . '/models/Question.php';
require_once APPLICATION_PATH . '/models/Classhasstudent.php';
require_once APPLICATION_PATH . '/models/Exam.php';
require_once LIBRARY_PATH.'/FormatDate.php';
require_once LIBRARY_PATH.		'/lib_arr.php';

class HistoryOfUserTestController extends Zend_Controller_Action
{
	private $_historyOfUserTest;
	private $_arrError;
	
	
	public function init()
	{
        $objSessionNamespace = new Zend_Session_Namespace( 'Zend_Auth' );
		$objSessionNamespace->setExpirationSeconds( 86400 );
		$this->_historyOfUserTest 	 = new Default_Models_Historyofusertest();

		$this->_cols_view       = array("id","user_id","test_id","time_start","time_finished", "total_score"); 
				// các cột hiển thị <tên sẽ hiển thị trong head của table>
		$this->_cols_view_title = array("id","Mã sinh viên","Mã số bài test","Thời gian bắt đầu","Kết thúc làm bài", "Tổng điểm");		
		
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
				
				$dataObj    = $this->_historyOfUserTest->fetchAll($where,$order,$iDisplayLength,$iDisplayStart);
				$total 		= count($this->_historyOfUserTest->fetchAll($where));
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
					$this->_historyOfUserTest->find("id",$arr_idItem);
					if($this->_historyOfUserTest->getId())
						$this->_historyOfUserTest->delete('id', $arr_idItem);
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
		$this->view->controller = "historyofusertest" ;
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
					$this->render("historyofusertest");
				}else{
					$this->_historyOfUserTest  = new Default_Models_Historyofusertest();
					$this->_historyOfUserTest->setOptions($data);
					if(empty($data['id']))
						$this->_historyOfUserTest->setId(null);	
										
					$this->_classs->save();					
					$this->_redirect("/historyofusertest");
				}
				
			}elseif($request->isGet()) {
					$data = $request->getParams();
					$id = $data['id'];			
					$result = $this->_historyOfUserTest->find('id', $id);
					if($this->_historyOfUserTest->getId()){
						$Obj = $this->_historyOfUserTest->toArray() ;
						$Obj['isupdate'] = 1;
						$this->view->Obj  = $Obj;
						$this->render("historyofusertest");	 
					}
				}else{
					$this->_redirect("error/error");
				}				
			
		}catch(Exception $ex){
		}
	}
	
	public function addAction(){
		$this->view->action = "edit" ;
		$this->view->controller = "historyofusertest" ;
		$Obj  = Array();
		$Obj['isupdate'] = 0;  // insert new 
		$this->view->Obj  = $Obj;
		$this->render("historyofusertest");	 
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

	public function deletealltestclassAction(){
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
				$data = $request->getParams();
				$shedule_exam_id = trim($data['shedule_exam_id']);
				$class_id		 = trim($data['class_id']);
				$modelSheduleExam = new Default_Models_SheduleExam();
				$result = $modelSheduleExam->fetchAll("`id`='".$shedule_exam_id."' AND `class_id`='".$class_id."'");
				if(count($result)>0)
				{
					$modelHisTest = new Default_Models_Historyofusertest();
					$resultHisTest = $modelHisTest->fetchAll("`shedule_exam_id`='".$shedule_exam_id."'");
					if(count($resultHisTest)>0)
					foreach($resultHisTest as $resultHisTestItem){
						$modelHisTest->delete('id', $resultHisTestItem->getId());
					}
					
				}else
					throw new Exception("Không tồn tại bài thi của lớp này.");
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
	
	public function teacherreviewstudenttestAction(){
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
				
			}elseif($request->isGet()) {
				$this->view->headLink()->appendStylesheet(BASE_URL. '/css/student.css');
				$data = $request->getParams();
					$his_test_id = $data["hisid"];
					$modelsHisTest = new Default_Models_Historyofusertest();
					$modelsHisTest->find("id",$his_test_id) ;
					if($modelsHisTest->getId())
					{
						$data = $modelsHisTest->toArray();
						$this->view->review_test        = 1;
						$this->view->view_corect        = 1;
						$this->view->view_feedback 		= 0; 
						$this->view->view_send_result   = 0;
						/*
						$models_test = new Default_Models_Test();
						$models_test->find("id",$data["testID"]);
						if($models_test->getId()){
							$list_score    		= explode(',',$models_test->getList_score());
							$data["list_score_in_test_table"]= $list_score;
							$data["question_perpage"] = $models_test->getQuestion_perpage();
						}
						*/
						$data["question_score"] = Zend_Json::decode($modelsHisTest->getList_score_of_question());
						$data["question_id"]    = Zend_Json::decode($modelsHisTest->getList_question_id()) ; // array
						$data["question"] = Zend_Json::decode($modelsHisTest->getList_answer_of_user()) ;  // array
						
						$ESSAY_list_question_id = Zend_Json::decode($modelsHisTest->getEssay_list_question_id());
						$ESSAY_list_score_of_question = Zend_Json::decode($modelsHisTest->getEssay_list_score_of_question());
						//$ESSAY_list_score_table_test = array();
						
						/*
						 * Begin xử lý thêm các thẻ answerNoEatScore, answerNoEatScore, questionNotDone đẻ cho gom nhóm các câu hỏi mà user được điểm, không được điểm
						 */
						$answer_of_user = $data["question"];  
						$question_id    = $data["question_id"];
						$question_socre_user = $data["question_score"] ; // dùng  
						if(count($question_id)>0){
							foreach ($question_id as $key=>$question_idItem){
								if(!empty($answer_of_user[$question_idItem])){
									/* ----------BEGIN CHECK $answer_of_user[$question_idItem] */
									// Check 1 số kiểu câu hỏi khi mà user không làm mà vẫn submit lên. Check để gán biến thống
									// kê state_question_after_dogarade: câu đúng, câu sai, câu ko làm
									$flag_tempQuesItem = false;
									if(count($answer_of_user[$question_idItem]) > 0){
										 foreach($answer_of_user[$question_idItem] as $tempQuesItem)
										 {
										 		if(!empty($tempQuesItem))
										 		{
										 			$flag_tempQuesItem = true ;
										 			break;
										 		}
										 }
									}
									/* ----------END CHECK $answer_of_user[$question_idItem] */
									
									// người dùng có thao tác đến question này
									// với true false,multichoice nếu ko check thì không đẩy lên post
									// đối với matching,completion,short ans,essay test luôn luôn đẩy lên post
									if($flag_tempQuesItem){
										if($question_socre_user[$key]>0)								
											$data["state_question_after_dogarade"][$key] = "answerEatScore";
										else
											$data["state_question_after_dogarade"][$key] = "answerNoEatScore";
									}
									else
									{
										$data["state_question_after_dogarade"][$key] = "questionNotDone";
									}
								}else{
									$data["state_question_after_dogarade"][$key] = "questionNotDone";
								}
							}						  	
						}
						
						/*
						 * Begin xử lý thêm các thẻ answerNoEatScore, answerNoEatScore, questionNotDone đẻ cho gom nhóm các câu hỏi mà user được điểm, không được điểm
						 */
						
						$tesid = $modelsHisTest->getTest_id();
						$data['test_id'] = $tesid;
						$modelUser = new Default_Models_User();
						$modelUser->find("id",$data['user_id']);
						if($modelUser->getId())
							$data['user_obj'] = $modelUser->toArray();
							
						$modelClass = new Default_Models_Classs();
						$modelClass->find("id",$data['class_id']);
						if($modelClass->getId())
							$data['class_obj'] = $modelClass->toArray();
							
						$modelTest = new Default_Models_Test();
						$modelTest->find("id",$tesid);
						$question_perpage = 10;
						if($modelTest->getId()){
							$question_perpage = $modelTest->getQuestion_perpage();
							$data["list_score_in_test_table"] = Zend_Json::decode($modelsHisTest->getList_score_table_test()); 
						}
						// điểm làm bài của user
						
						$data["question_perpage"] = $question_perpage ; // gắn tạm	
						$this->view->data     = $data;				
						
					}
				
			}
		}catch(Exception $ex){
			echo $ex->getMessage();  
		}
	}
	
	public function deletetestonestudentAction(){
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
				$data = $request->getParams();
				$histest_id = trim($data['hisid']);
				$modelHisTest = new Default_Models_Historyofusertest();
				$modelHisTest->find("id",$histest_id);
				if($modelHisTest->getId())
						$modelHisTest->delete('id', $histest_id);
				else
					throw new Exception("Không tồn tại bài thi của sinh viên này.");
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
	
	public function exportexcelscoreAction(){
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
				$data = $request->getParams();
			}elseif($request->isGet()) {
				$data = $request->getParams();
				$shedule_exam_id = trim($data['shedule_exam_id']);
				$class_id		 = trim($data['class_id']);
				$modelshedule_exam = new Default_Models_SheduleExam();
				$result = $modelshedule_exam->fetchAll("`id`='".$shedule_exam_id."' AND `class_id`='".$class_id."'");
				if(count($result)>0)
				{
							$modelshedule_exam = new Default_Models_SheduleExam();
							$modelshedule_exam->find("id",$shedule_exam_id);
							$shedule_exam_id =  $modelshedule_exam->getId();

							$modelExam = new Default_Models_Exam();
							$modelExam->find("id",$modelshedule_exam->getExam_id());
							$exam_id = $modelExam->getId() ;
							if(!empty($exam_id))
								$this->view->exam_obj = $modelExam->toArray(); 
								
							$class_id = $modelshedule_exam->getClass_id();
							$modelClass = new Default_Models_Classs();
							$modelClass->find("id",$class_id);
							$class_id = $modelClass->getId();
							//if(empty($class_id))	
								//throw new Exception("Không tồn tại id của lớp học");
							$data = array()	;
								$models_classhasstudent = new Default_Models_ClassHasStudent();
								$where = "`class_id`='".$class_id."'" ;
								$result = $models_classhasstudent->fetchAll($where);
								if(count($result)>0){
								/*
							 	* Begin Table list score user của class
							 	*/
									foreach($result as $key=>$resultItem){
										//$resultItem = new Default_Models_ClassHasStudent();
										$modelUser = new Default_Models_User();
										$modelUser->find("id",$resultItem->getUser_id());
										$userId = $modelUser->getId();
										if(!empty($userId)){
											$data[$key]['user_obj'] = $modelUser->toArray();
											
											$modelHisTestUser = new Default_Models_Historyofusertest();
											$where = "`shedule_exam_id`='".$shedule_exam_id."' AND `class_id`='".$class_id."' AND `user_id`='".$userId."'";
											$resultHisTest = $modelHisTestUser->fetchAll($where);
											if(count($resultHisTest)>0)
												$data[$key]['hist_test_obj'] = $resultHisTest[0]->toArray();
										}
									}
							/*
							 * End Table list score user của class
							 */						
							$this->view->class_obj = $modelClass->toArray();
							$this->view->StudentInClass = $data;
							$this->view->shedule_exam_id = $shedule_exam_id;
							
						}
					
				}
			}
		}catch(Exception $ex){
			echo $ex->getMessage();  
		}		
	}
	
	public function exportwordscoreAction(){
	try {
			$request = $this->getRequest();
			if ($request->isPost()) {
				$data = $request->getParams();
			}elseif($request->isGet()) {
				$data = $request->getParams();
				$shedule_exam_id = trim($data['shedule_exam_id']);
				$class_id		 = trim($data['class_id']);
				$modelshedule_exam = new Default_Models_SheduleExam();
				$result = $modelshedule_exam->fetchAll("`id`='".$shedule_exam_id."' AND `class_id`='".$class_id."'");
				if(count($result)>0)
				{
							$modelshedule_exam = new Default_Models_SheduleExam();
							$modelshedule_exam->find("id",$shedule_exam_id);
							$shedule_exam_id =  $modelshedule_exam->getId();

							$modelExam = new Default_Models_Exam();
							$modelExam->find("id",$modelshedule_exam->getExam_id());
							$exam_id = $modelExam->getId() ;
							if(!empty($exam_id))
								$this->view->exam_obj = $modelExam->toArray(); 
								
							$class_id = $modelshedule_exam->getClass_id();
							$modelClass = new Default_Models_Classs();
							$modelClass->find("id",$class_id);
							$class_id = $modelClass->getId();
							//if(empty($class_id))	
								//throw new Exception("Không tồn tại id của lớp học");
							$data = array()	;
								$models_classhasstudent = new Default_Models_ClassHasStudent();
								$where = "`class_id`='".$class_id."'" ;
								$result = $models_classhasstudent->fetchAll($where);
								if(count($result)>0){
								/*
							 	* Begin Table list score user của class
							 	*/
									foreach($result as $key=>$resultItem){
										//$resultItem = new Default_Models_ClassHasStudent();
										$modelUser = new Default_Models_User();
										$modelUser->find("id",$resultItem->getUser_id());
										$userId = $modelUser->getId();
										if(!empty($userId)){
											$data[$key]['user_obj'] = $modelUser->toArray();
											
											$modelHisTestUser = new Default_Models_Historyofusertest();
											$where = "`shedule_exam_id`='".$shedule_exam_id."' AND `class_id`='".$class_id."' AND `user_id`='".$userId."'";
											$resultHisTest = $modelHisTestUser->fetchAll($where);
											if(count($resultHisTest)>0)
												$data[$key]['hist_test_obj'] = $resultHisTest[0]->toArray();
										}
									}
							/*
							 * End Table list score user của class
							 */						
							$this->view->class_obj = $modelClass->toArray();
							$this->view->StudentInClass = $data;
							$this->view->shedule_exam_id = $shedule_exam_id;
							
						}
					
				}
			}
		}catch(Exception $ex){
			echo $ex->getMessage();  
		}		
		
	}
	
	public function updatelevelclassificationAction(){
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
				$data = $request->getParams();
				$shedule_exam_id = trim($data['shedule_exam_id']);
				$class_id		 = trim($data['class_id']);
				/*
				 * BEGIN UDDATE level , Classification cho mỗi question 
				 */
				// Kiểm tra xem lịch thi và lớp học này đã được lập chưa???
				$modelSheduleExam = new Default_Models_SheduleExam();
				
				$result = $modelSheduleExam->fetchAll("`id`='".$shedule_exam_id."' AND `class_id`='".$class_id."'");
				if(count($result)>0)
				{
					// Nếu tồn tại lịch  thi và lớp học đó thì ta lấy toàn bộ bài làm của sinh viên ra
					$modelHisTest = new Default_Models_Historyofusertest();
					$resultHisTest = $modelHisTest->fetchAll("`shedule_exam_id`='".$shedule_exam_id."'");
					$arrQuestionId = array();
					$arrQuestionScore = array();
					$arrTotalScoreOfOneTest = array();
					if(count($resultHisTest)>0)
					foreach($resultHisTest as $resultHisTestItem){
						//$resultHisTestItem = new Default_Models_Historyofusertest();
						$data["question_id"]    = Zend_Json::decode($resultHisTestItem->getList_question_id()) ; // array
						$data["question_score"] = Zend_Json::decode($resultHisTestItem->getList_score_of_question());
						$arrQuestionId[] =  $data["question_id"] ; 
						$arrQuestionScore[] = $data["question_score"] ;
						$arrTotalScoreOfOneTest[] = $resultHisTestItem->getTotal_score();
					}
					
					//$result1 =   Lib_Arr::my_array_sort($arrQuestionId,$arrQuestionScore);
					// Sắp xếp mảng question id (đồng thời cả mảng score của từng question) để tính xem câu hỏi đó có bao nhiêu sinh viên làm đúng
					$arrQuestionHasSort= array();
					$arrScoreHasSort= array();
					foreach($arrQuestionId as $key=>$arrQuestionIdItem)
					{
						$result1 =   Lib_Arr::my_array_sort($arrQuestionIdItem,$arrQuestionScore[$key]);
						$arrQuestionHasSort[] 	=  $result1[0] ;
						$arrScoreHasSort[] 		=  $result1[1] ;
					}
					
					$totalStudentDoTest = count($arrQuestionId);
					if($totalStudentDoTest<10)
						throw new Exception("Số lượng sinh viên làm bài phải từ 20 người để cập nhật độ phân cách.");
					
					/* Sau khi sort thì ra mảng như sau, cái này là đã implode cho dễ nhìn, thực ra nó là mảng
					 *$arrQuestionHasSort  array(4) {
							  [0]=>
							  string(47) "547,548,549,550,551,552,554,555,556,564,567,568"
							  [1]=>
							  string(47) "547,548,549,550,551,552,554,555,556,564,567,568"
							}
						$arrScoreHasSort	array(4) {
							  [0]=>
							  string(25) "0,0,0,9,0,0,0,0,0,5,44,25"
							  [1]=>
							  string(25) "0,87,0,0,0,0,0,0,7,5,0,25"
							}
					 * 
					 */
					/*
					 * BEGIN UDDATE LEVEL -- chỗ này đã được làm 1 phần riêng ko dùng đoạn này nữa
					 *
						
						$arrCountTrueQuestion = array();
						// Bắt đầu tính toán có bao nhiêu sinh viên làm đúng các câu hỏi và cập nhật 
						if(count($arrQuestionHasSort)>0)
						foreach ($arrQuestionHasSort[0] as $i=>$arrQuestionHasSortItem){
							$count = 0; // biến đếm số người trả lời đúng câu hỏi thứ $j
							for($j=0;$j<count($arrScoreHasSort); $j++){
								if($arrScoreHasSort[$j][$i]>0)
									$count++;
							}
							$arrCountTrueQuestion[$arrQuestionHasSortItem]= $count;
							// Cập nhật vào cơ sở dữ liệu
							$p = 0;
							if($totalStudentDoTest!=0) 
								$p = round($count/$totalStudentDoTest,2);
							$modelQuestion = new Default_Models_Question();
							$modelQuestion->find("id",$arrQuestionHasSortItem);
							if($modelQuestion->getId()){
								$modelQuestion->setLevel($p);
								$modelQuestion->save();
							}
						}
					/*
					 * END UDDATE LEVEL
					 */
					/*
					 * BEGIN UDDATE CLASSIFICATION
					 */
					// Sắp xếp lại mảng các sinh viên theo tổng điểm bài làm từ thấp đến cao	
					$arrScoreOneStuHasSort= array();
					$arrScoreHasSortIndex= array();
						$result1 =   Lib_Arr::my_array_sort($arrTotalScoreOfOneTest,$arrScoreHasSort);
						$arrScoreOneStuHasSort   =  $result1[0] ;
						$arrScoreHasSortIndex    =  $result1[1] ;
					 // B1 tính số lượng người trong mỗi nhóm cao và thấp 
					//$student_in_one_group = (int)(0.27*$totalStudentDoTest);
					$student_in_one_group = (int)(0.27*$totalStudentDoTest);
					$indexEndGroupLow = ($student_in_one_group-1);  // Chỉ số của sinh viên cao điểm nhất trong nhóm thấp
					// từ 0->$indexGroupLow là chỉ số các sinh viên nhóm thấp trong mảng $arrScoreOneStuHasSort  
					$indexBeginGroupHight =  ($totalStudentDoTest - $student_in_one_group);// 
					// Chỉ số của sinh viên thấp điểm nhất trong nhóm cao
					// từ $indexGroupHight->$totalStudentDoTest-1 là chỉ số các sinh viên nhóm cao trong mảng $arrScoreOneStuHasSort
					$arrCLASSIFICATIONQuestion = array();
					foreach ($arrQuestionHasSort[0] as $i=>$arrQuestionHasSortItem){
						$countGroupLow = 0;
						for($j=0;$j<=$indexEndGroupLow;$j++)
							if($arrScoreHasSortIndex[$j][$i]!=0)						
								$countGroupLow++;
						$countGroupHight = 0;
						for($j=$indexBeginGroupHight;$j<$totalStudentDoTest;$j++)
							if($arrScoreHasSortIndex[$j][$i]!=0)						
								$countGroupHight++;
						$d = round(($countGroupHight-$countGroupLow)/$student_in_one_group,2);
						$arrCLASSIFICATIONQuestion[$arrQuestionHasSortItem] = $d;
						$modelQuestion = new Default_Models_Question();
						$modelQuestion->find("id",$arrQuestionHasSortItem);
						if($modelQuestion->getId()){
							$modelQuestion->setClassification($d);
							$modelQuestion->save();
						}
						
					}
					/*
					 * END UDDATE CLASSIFICATION
					 */
					// Cập nhật biến xác nhận đã update level
					$modelSheduleExam = new Default_Models_SheduleExam();
					$modelSheduleExam->find("id",$shedule_exam_id);
					if($modelSheduleExam->getId()){
						$modelSheduleExam->setCount_update_level(1);
						$modelSheduleExam->save();
					}
					
				}else
					throw new Exception("Không tồn tại bài thi của lớp này.");
				/*
				 * END UDDATE level, Classification  cho mỗi question 
				 */
				
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
	
	public function dogradebyhandAction(){
		$this->_helper->layout->setLayout('reviewtest-teacher');
		$this->view->headLink()->appendStylesheet(BASE_URL. '/css/teacher.css');
	try {
			$request = $this->getRequest();
			if ($request->isPost()) {
				$data = $request->getParams();
				$shedule_exam_id = trim($data['shedule_exam_id']);
				$this->view->shedule_exam_id = $shedule_exam_id;
				/*
				 *
				 * mảng listscore đưa lên dạng như sau
				 * nghĩa là có 2 người làm( có his testid là 2 và 3), mỗi người làm 3 câu luận đề [610] [611] [612]
					["listscore"]=>  array(2)  
					[3]=>  array(3) { [610]=>  array(1) { [0]=>  string(1) "1" } [611]=>  array(1) { [0]=>  string(1) "2" } [612]=>  array(1) { [0]=>  string(1) "3" } } 
					[2]=>  array(3) { [610]=>  array(1) { [0]=>  string(1) "4" } [612]=>  array(1) { [0]=>  string(1) "5" } [611]=>  array(1) { [0]=>  string(1) "6" } }  				 * 
				 */
				$ArrayScore = $data['listscore'];
				if(count($ArrayScore)>0)
				foreach ($ArrayScore as $key=>$ArrayScoreItem){
					$ESSAY_list_question_id=array(); // 
					$ESSAY_list_score_of_question = array(); // new list score 
					$modelHisTestUser = new Default_Models_Historyofusertest();
					$modelHisTestUser->find("id",$key);
					if($modelHisTestUser->getId()){
						$scoreTableTest = Zend_Json::decode($modelHisTestUser->getEssay_list_score_table_test());
					}
					$list_score_of_one_stu = array();
					if(count($ArrayScoreItem)>0)
					foreach($ArrayScoreItem as $keyIdQuestion=>$scoreOneQues){
						$ESSAY_list_question_id[]=$keyIdQuestion;
						$i = 0; // Dùng để chạy mảng score table test để so sánh giá trị hợp lệ của điểm
						foreach($scoreOneQues as $scoreOneQuesItem){
							if(is_numeric($scoreOneQuesItem)){
								$list_score_of_one_stu[]= $scoreOneQuesItem<=$scoreTableTest[$i] ? $scoreOneQuesItem: "0" ;
							}
							else
								$list_score_of_one_stu[]= "0";
							$i++;	
						}
					}
					$ESSAY_list_score_of_question = $list_score_of_one_stu;
					
					if($modelHisTestUser->getId()){
						$_list_scoreUserEat = Zend_Json::decode($modelHisTestUser->getList_score_of_question());
						$_list_question_id    = Zend_Json::decode($modelHisTestUser->getList_question_id()) ; // array
						if(count($_list_scoreUserEat)>0){
							foreach($_list_question_id as $key=>$_list_question_idItem){
								// begin cập nhật điểm các câu hỏi chấm thủ công 
								if(in_array($_list_question_idItem,$ESSAY_list_question_id)){
									foreach($ESSAY_list_question_id as $keyEssayId=>$ESSAY_list_question_idItem)
										if($_list_question_idItem==$ESSAY_list_question_idItem)
											$_list_scoreUserEat[$key] = $ESSAY_list_score_of_question[$keyEssayId];
								}
								// end cập nhật điểm các câu hỏi chấm thủ công
							}
							 	// update sum score
							 $sumScore = 0;
						 	foreach($_list_scoreUserEat as $_list_scoreUserEatItem)
						 		$sumScore+= ($_list_scoreUserEatItem*1);
						}
						 
						// Làm tròn điểm theo cài đặt bài test
						$modelTest = new Default_Models_Test();
						$modelTest->find("id",$modelHisTestUser->getTest_id());
						if($modelTest->getId())
							$sumScore = round($sumScore,$modelTest->getDecimal_digits_in_grades());
						$modelHisTestUser->setTotal_score($sumScore);
						$modelHisTestUser->setList_score_of_question(Zend_Json::encode($_list_scoreUserEat));
						$modelHisTestUser->setEssay_list_score_of_question(Zend_Json::encode($list_score_of_one_stu));
						$modelHisTestUser->save();
					}
				}
				$this->view->successGradeByhand = "successGradeByhand";
			}elseif($request->isGet()) {
				$data = $request->getParams();
				if(empty($data['shedule_exam_id']) || empty($data['class_id']))
					throw new Exception("Đường dẫn không hợp lệ");
				$shedule_exam_id = trim($data['shedule_exam_id']);
				$class_id		 = trim($data['class_id']);
				//// DOGRADE BY HAND
				$modelshedule_exam = new Default_Models_SheduleExam();
				$result = $modelshedule_exam->fetchAll("`id`='".$shedule_exam_id."' AND `class_id`='".$class_id."'");
				if(count($result)>0)
				{
							$modelshedule_exam = new Default_Models_SheduleExam();
							$modelshedule_exam->find("id",$shedule_exam_id);
							$shedule_exam_id =  $modelshedule_exam->getId();

							$modelExam = new Default_Models_Exam();
							$modelExam->find("id",$modelshedule_exam->getExam_id());
							$exam_id = $modelExam->getId() ;
							if(!empty($exam_id))
								$this->view->exam_obj = $modelExam->toArray(); 
								
							$class_id = $modelshedule_exam->getClass_id();
							$modelClass = new Default_Models_Classs();
							$modelClass->find("id",$class_id);
							$class_id = $modelClass->getId();
							//if(empty($class_id))	
								//throw new Exception("Không tồn tại id của lớp học");
							$data = array()	;
								// begin lấy danh sách sinh viên của lớp học
								$models_classhasstudent = new Default_Models_ClassHasStudent();
								$where = "`class_id`='".$class_id."'" ;
								$result = $models_classhasstudent->fetchAll($where);
								// end lấy danh sách sinh viên của lớp học
								if(count($result)>0){
								/*
							 	* Begin Table list score user của class
							 	*/
									foreach($result as $key=>$resultItem){
										//$resultItem = new Default_Models_ClassHasStudent();
										$modelUser = new Default_Models_User();
										$modelUser->find("id",$resultItem->getUser_id());
										$userId = $modelUser->getId();
										if(!empty($userId)){
											// Lấy thông tin cá nhân của sinh viên 
											//$data[$key]['user_obj'] = $modelUser->toArray();
											$modelHisTestUser = new Default_Models_Historyofusertest();
											$where = "`shedule_exam_id`='".$shedule_exam_id."' AND `class_id`='".$class_id."' AND `user_id`='".$userId."'";
											$resultHisTest = $modelHisTestUser->fetchAll($where);
											
											if(count($resultHisTest)>0){
												$data[]['hist_test_obj'] = $resultHisTest[0]->toArray();
												$list_essay_id = $resultHisTest[0]->getEssay_list_question_id();
												if(empty($list_essay_id))
													$flagNotHaveEssayInTest = 1 ;	
												
											}
												
										}
									}
							/*
							 * End Table list score user của class
							 */						
									
							$this->view->flagNotHaveEssayInTest = $flagNotHaveEssayInTest;
							$this->view->class_obj = $modelClass->toArray();
							$this->view->StudentInClass = $data;
							$this->view->shedule_exam_id = $shedule_exam_id;
						}
				}
			}
		}catch(Exception $ex){
			echo $ex->getMessage();  
		}			
	}
	
	public function viewleveltestAction(){
	try {
		$this->_helper->layout->setLayout('admin');
		$this->view->headLink()->appendStylesheet(BASE_URL. '/css/teacher.css');
		
			$request = $this->getRequest();
			if ($request->isPost()) {
				//$data = $request->getParams();
			}elseif($request->isGet()) {
				$data = $request->getParams();
				$shedule_exam_id = trim($data['shedule_exam_id']);
				$class_id		 = trim($data['class_id']);
				$modelModels_Historyofusertest= new Default_Models_Historyofusertest();
				$result = $modelModels_Historyofusertest->fetchAll("`shedule_exam_id`='".$shedule_exam_id."' AND `class_id`='".$class_id."'");
				if(count($result)>0)
				{
					$resultArray = $result[0]->toArray();
					if(!empty($resultArray['test_id']))
					{
						$modelTest = new Default_Models_Test();
						$modelTest->find("id",$resultArray['test_id']);
						if($modelTest->getId()){
							$list_questionId = explode(",",$modelTest->getList_question());
							$this->view->ObjTest = $modelTest->toArray();							
						}
					}
					$arrObjQuestion = array();
					if(count($list_questionId)>0)
					foreach($list_questionId as $list_questionIdItem){
						$modelQuestion = new Default_Models_Question();
						$modelQuestion->find("id",$list_questionIdItem);
						if($modelQuestion->getId())
							$arrObjQuestion[]=$modelQuestion->toArray();
					}
					$this->view->arrObjQuestion = $arrObjQuestion;
					$this->view->shedule_exam_id = $shedule_exam_id;
					
				}
			}
		}catch(Exception $ex){
			echo $ex->getMessage();  
		}		
	}
	
	public function autosavegradebyhandAction(){
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
				$data = $request->getParams();
				$shedule_exam_id = trim($data['shedule_exam_id']);
				/*
				 *
				 * mảng listscore đưa lên dạng như sau
				 * nghĩa là có 2 người làm( có his testid là 2 và 3), mỗi người làm 3 câu luận đề [610] [611] [612]
					["listscore"]=>  array(2)  
					[3]=>  array(3) { [610]=>  array(1) { [0]=>  string(1) "1" } [611]=>  array(1) { [0]=>  string(1) "2" } [612]=>  array(1) { [0]=>  string(1) "3" } } 
					[2]=>  array(3) { [610]=>  array(1) { [0]=>  string(1) "4" } [612]=>  array(1) { [0]=>  string(1) "5" } [611]=>  array(1) { [0]=>  string(1) "6" } }  				 * 
				 */
				$ArrayScore = $data['listscore'];
				if(count($ArrayScore)>0)
				foreach ($ArrayScore as $key=>$ArrayScoreItem){
					$ESSAY_list_question_id=array(); // 
					$ESSAY_list_score_of_question = array(); // new list score 
					$modelHisTestUser = new Default_Models_Historyofusertest();
					$modelHisTestUser->find("id",$key);
					if($modelHisTestUser->getId()){
						$scoreTableTest = Zend_Json::decode($modelHisTestUser->getEssay_list_score_table_test());
					}
					$list_score_of_one_stu = array();
					if(count($ArrayScoreItem)>0)
					foreach($ArrayScoreItem as $keyIdQuestion=>$scoreOneQues){
						$ESSAY_list_question_id[]=$keyIdQuestion;
						$i = 0; // Dùng để chạy mảng score table test để so sánh giá trị hợp lệ của điểm
						foreach($scoreOneQues as $scoreOneQuesItem){
							if(is_numeric($scoreOneQuesItem)){
								$list_score_of_one_stu[]= $scoreOneQuesItem<=$scoreTableTest[$i] ? $scoreOneQuesItem: "0" ;
							}
							else
								$list_score_of_one_stu[]= "0";
							$i++;	
						}
					}
					$ESSAY_list_score_of_question = $list_score_of_one_stu;
					
					if($modelHisTestUser->getId()){
						$_list_scoreUserEat = Zend_Json::decode($modelHisTestUser->getList_score_of_question());
						$_list_question_id    = Zend_Json::decode($modelHisTestUser->getList_question_id()) ; // array
						if(count($_list_scoreUserEat)>0){
							foreach($_list_question_id as $key=>$_list_question_idItem){
								// begin cập nhật điểm các câu hỏi chấm thủ công 
								if(in_array($_list_question_idItem,$ESSAY_list_question_id)){
									foreach($ESSAY_list_question_id as $keyEssayId=>$ESSAY_list_question_idItem)
										if($_list_question_idItem==$ESSAY_list_question_idItem)
											$_list_scoreUserEat[$key] = $ESSAY_list_score_of_question[$keyEssayId];
								}
								// end cập nhật điểm các câu hỏi chấm thủ công
							}
							 	// update sum score
							 $sumScore = 0;
						 	foreach($_list_scoreUserEat as $_list_scoreUserEatItem)
						 		$sumScore+= ($_list_scoreUserEatItem*1);
						}
						 
						// Làm tròn điểm theo cài đặt bài test
						$modelTest = new Default_Models_Test();
						$modelTest->find("id",$modelHisTestUser->getTest_id());
						if($modelTest->getId())
							$sumScore = round($sumScore,$modelTest->getDecimal_digits_in_grades());
						$modelHisTestUser->setTotal_score($sumScore);
						$modelHisTestUser->setList_score_of_question(Zend_Json::encode($_list_scoreUserEat));
						$modelHisTestUser->setEssay_list_score_of_question(Zend_Json::encode($list_score_of_one_stu));
						$modelHisTestUser->save();
					}
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
	
	public function setupretestagainallclassAction(){
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
				$data = $request->getParams();
				$shedule_exam_id = trim($data['shedule_exam_id']);
				$class_id		 = trim($data['class_id']);
				$modelModels_Historyofusertest= new Default_Models_Historyofusertest();
				$result = $modelModels_Historyofusertest->fetchAll("`shedule_exam_id`='".$shedule_exam_id."' AND `class_id`='".$class_id."'");
				if(count($result)>0){
					foreach($result as $key=>$resultItem){
						$modelModels_Historyofusertest->find("id",$resultItem->getId());
						if($modelModels_Historyofusertest->getId()){
							$modelModels_Historyofusertest->setDo_test_again(1);
							$modelModels_Historyofusertest->save();
						}
					}
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
	
	public function redotestonestudentAction(){
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
				$data = $request->getParams();
				$hisid = trim($data['hisid']);
				if(empty($hisid))
					throw new Exception("Không tồn tại bài thi của sinh viên này.");
				$modelModels_Historyofusertest= new Default_Models_Historyofusertest();
				$modelModels_Historyofusertest->find("id", $hisid);
				if($modelModels_Historyofusertest->getId()){
					$modelModels_Historyofusertest->setDo_test_again(1);
					$modelModels_Historyofusertest->save();
				}else
					throw new Exception("Không tồn tại bài thi của sinh viên này.");
				
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
	
	public function setupstopalltestinclassAction(){
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
				$data = $request->getParams();
				$shedule_exam_id = trim($data['shedule_exam_id']);
				$class_id		 = trim($data['class_id']);
				$stop_do_test_now = trim($data['stop_do_test_now']);
				/*
				 * 
				 */
				// Kiểm tra xem lịch thi và lớp học này đã được lập chưa???
				$modelSheduleExam = new Default_Models_SheduleExam();
				$result = $modelSheduleExam->fetchAll("`id`='".$shedule_exam_id."' AND `class_id`='".$class_id."'");
				
				if(count($result)>0)
				{
					// Nếu tồn tại lịch  thi và lớp học đó thì ta lấy toàn bộ bài làm của sinh viên ra
					$modelHisTest = new Default_Models_Historyofusertest();
					$resultHisTest = $modelHisTest->fetchAll("`shedule_exam_id`='".$shedule_exam_id."' AND `class_id`='".$class_id."'");
					if(count($resultHisTest)>0){
						foreach($resultHisTest as $resultHisTestItem){
							//$resultHisTestItem = new Default_Models_Historyofusertest();
							$modelHisTest = new  Default_Models_Historyofusertest();
							$modelHisTest->find("id",$resultHisTestItem->getId());
							if($modelHisTest->getId()){
								$modelHisTest->setStop_do_test_now($stop_do_test_now);
								$modelHisTest->save();
							}
						}
						
					}
					
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
		}
		return $data;
	}	
	
	
	
	


	
} 