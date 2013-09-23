<?php
/* Date create: 18-10-2010
 * Created by: Ngô Chí Công, Nguyễn Tiến Dũng
 * Description: controller xử lý thêm mới và edit tất cả các loại câu hỏi
 * 				mỗi loại câu hỏi ta có 2 action: 1 add và 1 edit
 * 
 */
require_once APPLICATION_PATH . '/models/Question.php';
require_once APPLICATION_PATH . '/models/ProcessXML/questionXML.php';
require_once LIBRARY_PATH.		'/lib_xml.php';
require_once APPLICATION_PATH . '/models/Answer.php';
require_once LIBRARY_PATH.		'/lib_arr.php';
require_once LIBRARY_PATH.		'/FormatDate.php';
require_once APPLICATION_PATH . '/models/DoGrade.php';
require_once APPLICATION_PATH . '/models/User.php';
require_once APPLICATION_PATH . '/models/Teachassignment.php';
require_once 'Zend/Filter/StripTags.php';

class QuestionController extends Zend_Controller_Action
{
	private $_arrError;
	private $_question;
	private $_XML;
	private $_answer;
	public function init()
	{
        $objSessionNamespace = new Zend_Session_Namespace( 'Zend_Auth' );
		$objSessionNamespace->setExpirationSeconds( 86400 );
		$this->view->controller = "question";		
		$this->_helper->layout->setLayout('admin');
		//$this->_helper->layout->setLayout('teacher');
		$this->view->headLink()->appendStylesheet(BASE_URL. '/css/teacher.css');	
		$this->view->jQuery()->addJavascriptFile(BASE_URL . '/js/cms.js');
		$this->view->headLink()->appendStylesheet(BASE_URL . '/css/cms.css');
		$this->view->jQuery()->addJavascriptFile(BASE_URL . '/js/controller/question/index.js');
		$this->view->headLink()->appendStylesheet(BASE_URL . '/css/controller/question/index.css');
		
		$this->_question 	 = new Default_Models_Question();
		$this->_answer       = new Default_Models_Answer();
		$arrError = Array();
		/*		 
		$this->_cols_view_titleQuestion = array("id","Tiêu đề","Nội dung");
		$this->_colsViewQuestion		= array("id","question_title","content");
		*/
		$this->_cols_view_titleQuestion = array("id","Tiêu đề","Ngày tạo","Người tạo", "Loại");
		$this->_colsViewQuestion		= array("id","question_title","timecreated","created_user", "type_question");
		
		// Cái này nếu muốn thêm 1 trường nữa thì phải thêm ở TestController action Create
		// $this->view->cols_view_title  đây là table question ở create test
		$this->_cols_view_titleCreateTest = array("id","Tiêu đề","Người tạo","Ngày tạo", "Loại");
		$this->_colsViewCreateTest		  = array("id","question_title","created_user","timecreated", "type_question");
		
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
			$this->view->arrSubjectId	= $arrIdSubject;	  	
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
	
	public function testAction(){
		try{
			$request = $this->getRequest();
			if ($request->isPost()) {
					$data = $request->getParams();

					$this->view->review_test        = 0;
					$this->view->view_corect        = 1;
					$this->view->view_feedback 		= 1; 
					$this->view->view_send_result   = 1;
					$question_id    = $data["question_id"]; // array
					$answer_of_user = $data["question"];    // array
					$total_score = 0;
					if(count($question_id)>0){
						foreach ($question_id as $key=>$question_idItem){
							if(!empty($answer_of_user[$question_idItem])){ 
								// người dùng có thao tác đến question này
								// với true false,multichoice nếu ko check thì không đẩy lên post
								// đối với matching,completion,short ans,essay test luôn luôn đẩy lên post
								$obj =  DoGrade::_DoGrade($question_idItem,$answer_of_user[$question_idItem]);
								$total_score+= $obj->num_score;
								$data["question_score"][$key] = $obj->num_score;
							}else{
								$data["question_score"][$key] = 0;
							}
						}						  	
					}		
					$this->view->data     = $data;
					
					
			}elseif($request->isGet()){
					$question_of_test  = array(8 ,6 ,9 ,11,28,26);
					$str = ' var_dump($question_of_test);';
					eval($str);					
					
					
					$answer_of_user    = array(8=>array(),6=>array(),9=>array(),11=>array(),28=>array(),26=>array());					
					$question_score    = array(-1,-1,-1,-1,-1,-1);
					
					$data = array();
					$data["question_id"]     = $question_of_test;
					$data["question"]  		 = $answer_of_user; // id of question + answer of user for this question
					$data["question_score"]  = $question_score; 
					//$review_test=0,$view_corect=0,$view_feedback=0,$view_send_result=0,$view_score=-1) {
					$this->view->review_test        = 0;
					$this->view->view_corect        = 0;
					$this->view->view_feedback 		= 0; 
					$this->view->view_send_result   = 1;
					$this->view->data     = $data;
					
			}
		}catch (Exception $ex){
			echo "lỗi hoặc phương thức sai".$ex->getMessage();
			die();
		}
	}
	
	public function indexAction()
	{
		
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
				$data = $request->getParams();
			}elseif($request->isGet()) {
				// các cột được hiển thị <tên các cột trong table thật>
				$this->view->cols_view_title = $this->_cols_view_titleQuestion;
				$this->view->subject_id = "";
				$this->view->chapter_subject_id = "";
				
			}
		}catch(Exception $ex){
			echo $ex->getMessage();  
		}
	
	}
	
	public function getonequestionAction(){
		try {
			$request = $this->getRequest();
			$data    = $request->getParams();
			if ($request->isPost()) {
				$question_id = $data["id"];
				$models_question = new Default_Models_Question();
				$models_question->find("id",$question_id);
				if($models_question->getId()){					
					$questionXML = new QuestionXML($models_question->getQuestiontext());
					$score = $questionXML->getScoreFromXml();
					$title = $models_question->getQuestion_title();
					$dataRes = array("question_id"=>$models_question->getId(),"question_title"=>$title,"question_score"=>$score);
					$data_json = array("success" => true,"data" => $dataRes);
				}else
					$data_json = array("success" => false,"error" => "Id không tồn tại.");		
			}elseif($request->isGet()) {
				$data_json = array("success" => false,"error" => "Lỗi phương thức");
			}
		}catch(Exception $ex){
			$data_json = array("success" => false,"error" => "Lỗi ".$ex->getMessage());
		}
		echo Zend_Json::encode($data_json);
		die();
	}			
	
		// serverside của create test 
	public function serversidecreatetestAction(){
		$this->_cols_view       = $this->_colsViewCreateTest;		
		$this->_cols_view_title = $this->_cols_view_titleCreateTest;		
		try {
			$request = $this->getRequest();
			$data    = $request->getParams();
			if ($request->isPost()) {
				
			}elseif($request->isGet()) {
				$iColumns        = $data['iColumns'];
				$iDisplayStart   = $data['iDisplayStart'];
				$iDisplayLength  = $data['iDisplayLength'];
				$sEcho			 = intval($data['sEcho']);
				$subject_id			= trim($data['subject_id']);
				$chapter_subject_id	= trim($data['chapter_subject_id']);
				$chkShowAllBankQuestion	= trim($data['chkShowAllBankQuestion']);
				$SltTypeQuestion		= $data['SltTypeQuestion'];
				
				// Order
				$order = array();
				$hasSort			= $data['iSortCol_0'];
				if(isset($hasSort)){
					$iSortingCols = $data['iSortingCols']; 
					$listSortColsIndex = array();
					$listSortColsDir   = array();
					for($i=0;$i<$iSortingCols;$i++){
						$iSortColIndex   =  $data["iSortCol_".$i];
						$iSortColDir	 =  $data["sSortDir_".$i];
						$iSortColName    =  $this->_cols_view[$iSortColIndex];
						$iSortColDir = ($iSortColDir=="" ? "ASC" : $iSortColDir);
						//
						if($iSortColName!="id_")
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
				if(!empty($subject_id))
					$where .=' AND `subject_id`='.$subject_id; 
				if(!empty($chapter_subject_id))
					$where .=' AND `chapter_id`='.$chapter_subject_id; 
				if($chkShowAllBankQuestion==0){
					$auth = Zend_Auth::getInstance();
			  		$userhaslogin = $auth->getStorage()->read();
					$where .=' AND `created_user`='.$userhaslogin->id;
				} 
				if(!empty($SltTypeQuestion))
					$where .=' AND `type_question`='.$SltTypeQuestion; 
					
				$where.=" AND 1";
				
				$dataObj    = $this->_question->fetchAll($where,$order,$iDisplayLength,$iDisplayStart);
				
				$total 		= count($this->_question->fetchAll($where));
				
				
				
				$json_data  = new stdClass();
				$json_data->sEcho = $sEcho;
				$json_data->iTotalRecords = $total;
				$json_data->iTotalDisplayRecords =$total;
				$aaData = array();				
				if(count($dataObj)){
					foreach ($dataObj as $dataItem){						
						$dataItemArray  = $dataItem->toArray();
						$id  			= $dataItemArray["id"];
						$auth = Zend_Auth::getInstance();
			  			$userhaslogin = $auth->getStorage()->read();
						if(!empty($dataItemArray["created_user"]))
						{
							$modelUser = new Default_Models_User();
							$idCreate_User = $dataItemArray["created_user"];
							$modelUser->find("id",$dataItemArray["created_user"]);
							if($modelUser->getId())
								$dataItemArray["created_user"] = $modelUser->getFirstname()." ".$modelUser->getLastname() ;
						}
						
						switch ($dataItemArray["type_question"]){
							case 1 : 
								$dataItemArray["type_question"] = "Đúng sai"; break ;
							case 2 : 
								$dataItemArray["type_question"] = "Nhiều lựa chọn"; break ;
							case 3 : 
								$dataItemArray["type_question"] = "Ghép cặp đôi"; break ;
							case 4 : 
								$dataItemArray["type_question"] = "Điền khuyết"; break ;
							case 5 : 
								$dataItemArray["type_question"] = "Luận đề"; break ;
							case 6 : 
								$dataItemArray["type_question"] = "Trả lời ngắn"; break ;
							default: $dataItemArray["type_question"]="";
						}
						
						
						$len = 50; $text =  $dataItemArray["question_title"] ;
						if(strlen($text)<50) { $len= strlen($text); $i=$len;}
						else
						for($i=$len;$i>0;$i--)
							if(isset($text[$i]) && $text[$i]==" ")  break;
						if($i==0) $i=$len;
						$text1= substr($text,0,$i);
						$text1.="...";
				
						
						$dataItemArray["question_title"] = '<span class="test-has-question-'.$id.'">'.$text1.'</span>';
						$dataItemArray["timecreated"] = Zend_View_Helper_FormatDate::convertYmdToMdy($dataItemArray["timecreated"]);
						//$dataItemArray["id_"] = implode("-",$order);	
																	
						$tmpArr 		= array();
						$strAction    = '<a class="add-to-test-icon"   href="#" onclick="addToTest('.$id.'); return false;"><img class="fugue fugue-control-double-180" alt="Đưa vào đề thi" src="'. BASE_URL .'/img/icons/space.gif"/></a>&nbsp;&nbsp;';
						$strAction   .= '<a class="view-icon"   href="'. BASE_URL .'/'. $data["controller"] .'/preview/id/'.  $id .'" onclick="viewClick(this.href); return false;"><img class="fugue fugue-eye" alt="" src="'. BASE_URL .'/img/icons/space.gif"/></a>&nbsp;&nbsp;';
						if($userhaslogin->id==$idCreate_User || $userhaslogin->group_id == 5){
							$strAction   .= '<a class="edit-icon-popup"   href="'. BASE_URL .'/'. $data["controller"] .'/edit/id/'.  $id .'" onclick="editPopup(this.href); return false;"><img class="fugue fugue-pencil" alt="Chỉnh sửa" src="'. BASE_URL .'/img/icons/space.gif"/></a>&nbsp;&nbsp;';
							$strAction   .= '<a class="remove-icon" href="'. BASE_URL .'/'. $data["controller"] .'/delete/id/'.$id.'"  onclick="deleteClick(this,this.href); return false;"><img class="fugue fugue-cross"  alt="Xóa"       src="'. BASE_URL .'/img/icons/space.gif"/></a>'; 
						}
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
	
	// serverside của cms question 
	public function serversideAction(){
		$this->_cols_view       = $this->_colsViewQuestion;		
		$this->_cols_view_title = $this->_cols_view_titleQuestion;		
		try {
			$request = $this->getRequest();
			$data    = $request->getParams();
			if ($request->isPost()) {
				
			}elseif($request->isGet()) {
				$iColumns        = $data['iColumns'];
				$iDisplayStart   = $data['iDisplayStart'];
				$iDisplayLength  = $data['iDisplayLength'];
				$sEcho			 = intval($data['sEcho']);
				$subject_id			= $data['subject_id'];
				$chapter_subject_id	= $data['chapter_subject_id'];
				$chkShowAllBankQuestion	= $data['chkShowAllBankQuestion'];
				$SltTypeQuestion		= $data['SltTypeQuestion'];
				
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
						$order[] = $iSortColName.' '.$iSortColDir;
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
				
				if(!empty($subject_id))
					$where .=' AND `subject_id`='.$subject_id; 
				if(!empty($chapter_subject_id))
					$where .=' AND `chapter_id`='.$chapter_subject_id;
				if($chkShowAllBankQuestion==0){
					$auth = Zend_Auth::getInstance();
			  		$userhaslogin = $auth->getStorage()->read();
					$where .=' AND `created_user`='.$userhaslogin->id;
				}
				if(!empty($SltTypeQuestion))
					$where .=' AND `type_question`='.$SltTypeQuestion; 
				
				$where.=" AND 1";
				//var_dump($where);
				$dataObj    = $this->_question->fetchAll($where,$order,$iDisplayLength,$iDisplayStart);
				
				$total 		= count($this->_question->fetchAll($where));
				$json_data  = new stdClass();
				$json_data->sEcho = $sEcho;
				$json_data->iTotalRecords = $total;
				$json_data->iTotalDisplayRecords =$total;
				$aaData = array();				
				if(count($dataObj)){
					foreach ($dataObj as $dataItem){
						$dataItemArray  = $dataItem->toArray();
						$id  			= $dataItemArray["id"];
						$auth = Zend_Auth::getInstance();
			  			$userhaslogin = $auth->getStorage()->read();
						
						if(!empty($dataItemArray["created_user"]))
						{
							$modelUser = new Default_Models_User();
							$idCreate_User = $dataItemArray["created_user"];
							$modelUser->find("id",$dataItemArray["created_user"]);
							if($modelUser->getId())
								$dataItemArray["created_user"] = $modelUser->getFirstname()." ".$modelUser->getLastname() ;
						}
						
						switch ($dataItemArray["type_question"]){
							case 1 : 
								$dataItemArray["type_question"] = "Đúng sai"; break ;
							case 2 : 
								$dataItemArray["type_question"] = "Nhiều lựa chọn"; break ;
							case 3 : 
								$dataItemArray["type_question"] = "Ghép cặp đôi"; break ;
							case 4 : 
								$dataItemArray["type_question"] = "Điền khuyết"; break ;
							case 5 : 
								$dataItemArray["type_question"] = "Luận đề"; break ;
							case 6 : 
								$dataItemArray["type_question"] = "Trả lời ngắn"; break ;
							default: $dataItemArray["type_question"]="";
						}
							
						/*
						 * Xử lý cắt chuỗi nếu tiêu đề câu hỏi dài quá
						 *		$len = 50; $text =  $rec->products_description ;
								if(strlen($text)<50) { $len= strlen($text); $i=$len;}
								else
								for($i=$len;$i>0;$i--)
								if($text[$i]==" ")  break;
								if($i==0) $i=$len;
								$text1= substr($text,0,$i);
								$text1.="...";
						 * 
						 */
								$len = 50; $text =  $dataItemArray["question_title"] ;
								if(strlen($text)<50) { $len= strlen($text); $i=$len;}
								else
								for($i=$len;$i>0;$i--)
									if(isset($text[$i]) && $text[$i]==" ")  break;
								if($i==0) $i=$len;
								$text1= substr($text,0,$i);
								$text1.="...";
						
						$dataItemArray["question_title"] = '<span class="test-has-question-'.$id.'">'.$text1.'</span>';
						$dataItemArray["timecreated"] = Zend_View_Helper_FormatDate::convertYmdToMdy($dataItemArray["timecreated"]);
						$tmpArr 		= array();
						$strAction    = '<a class="view-icon"   href="'. BASE_URL .'/'. $data["controller"] .'/preview/id/'.  $id .'" onclick="viewClick(this.href); return false;"><img class="fugue fugue-eye" alt="" src="'. BASE_URL .'/img/icons/space.gif"/></a>&nbsp;&nbsp;';
						if($userhaslogin->id==$idCreate_User || $userhaslogin->group_id == 5){
							$strAction   .= '<a class="edit-icon"   href="'. BASE_URL .'/'. $data["controller"] .'/edit/id/'.  $id .'" onclick="editPopup(this.href); return false;"><img class="fugue fugue-pencil" alt="Chỉnh sửa" src="'. BASE_URL .'/img/icons/space.gif"/></a>&nbsp;&nbsp;';
							$strAction   .= '<a class="remove-icon" href="'. BASE_URL .'/'. $data["controller"] .'/delete/ajax/1/id/'.$id.'"  onclick="deleteClick(this,this.href); return false;"><img class="fugue fugue-cross"  alt="Xóa"       src="'. BASE_URL .'/img/icons/space.gif"/></a>';
						} 
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
	
	public function previewAction(){
		$this->view->jQuery()->addJavascriptFile(BASE_URL . '/js/controller/test/test.js');
		try{
			$request = $this->getRequest();
			if ($request->isPost()) {
				/*
				$data = $request->getParams();					
				$question_id    = $data["question_id"];
				$answer_of_user = $data["answer_of_user"]; // is array of answer of user

				
				/*
				 * Nếu không có test id tức là preview câu hỏi trong cms
				 * Lúc đó $question_score: sẽ là giá trị default của question đó
				
				// Tính điểm câu hỏi
				$obj =  DoGrade::_DoGrade($question_score,$question_id,$answer_of_user);
				
				// Trả kết quả xuống view với những option tương ứng
				$this->view->review_test        = 0;
				$this->view->view_corect        = 1;
				$this->view->view_feedback 		= 1; 
				$this->view->view_send_result   = 1;
				$this->view->question_score     = $obj->num_score;
				$this->view->answer_of_user     = $answer_of_user;
				$this->view->question_id 		= $question_id;	
				$this->view->score_of_question 	= $question_score;
				$this->_helper->layout->disableLayout();
				 */	
				
			}elseif($request->isGet()){
				$data = $request->getParams();					
				$question_id    = $data["id"];
				// Trả kết quả xuống view với những option tương ứng
				$this->view->review_test        = 0;
				$this->view->view_corect        = 0;
				$this->view->view_feedback 		= 0; 
				$this->view->view_send_result   = 1;
				$this->view->question_score     = -1;
				$this->view->answer_of_user     = array();
				$this->view->question_id 		= $question_id;	
				$this->view->score_of_question 	= isset($question_score)?$question_score:0;
				$this->view->data			 	= $data;
				
			}
		}catch (Exception $ex){
			echo $ex->getMessage(); die();
		}	
	}
	
	public function deleteAction(){
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
				//throw new Exception("Hiện chức năng này đang bị khóa. Vui lòng liên hệ Admin");
				$data = $request->getParams();
				$question_ids = $data['id'];
				if(!is_array($question_ids))
					$question_ids = array($question_ids);					
				if(count($question_ids))
				foreach($question_ids as $question_idsItem){
					$this->_question->find("id",$question_idsItem);
					if($this->_question->getId()){
						//if(strlen(($modelsQuestion->getAdd_to_test()))==0){
						if(strlen($this->_question->getList_test_id() != 0)){
							throw new Exception("question id = ".$question_idsItem." đã được thêm vào bộ đề thi, bạn không thể xóa.");
						}
						//$a =" ";
						
					// BEGIN Xử lý không cho 1 user xóa câu hỏi của user khác
					$auth = Zend_Auth::getInstance();
					if ($auth->hasIdentity()){
						$userhaslogin = $auth->getStorage()->read();
						if($this->_question->getCreated_user()!= $userhaslogin->id)
							throw new Exception("Bạn không thể xóa câu hỏi của người khác.");
					}
					// END Xử lý không cho 1 user xóa câu hỏi của user khác					
					// BEGIN xóa các câu trả lời của câu hỏi đó	
						$textXML = $this->_question->getQuestiontext();
						$quesXML 		= new QuestionXML($textXML);
						$allAnswers = $quesXML->getAllAnswers();
						//1. xu li xoa answer.
						if(count($allAnswers)>0)
						foreach($allAnswers as $key=>$ansItem){
								// step 1: remove on database
								$this->_answer = new Default_Models_Answer();
								$this->_answer->delete('id',$ansItem['id']);
						}
					// END xóa các câu trả lời của câu hỏi đó						
						$this->_question->delete('id', $question_idsItem);
					}
					else
						throw new Exception("question id = ".$question_idsItem." not exists.");
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
	
	public function addAction(){
		$this->_redirect("question/index");
	}
	
	public function editAction(){
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
				$this->_redirect("error/error");
			}elseif($request->isGet()) {
				$data = $request->getParams();
				$question_id = $data['id'];			
				if(empty($data['id']))
						$this->_redirect("/question");				
				$this->_question->find('id', $question_id);
				if($this->_question->getId()){
					
					// BEGIN Xử lý không cho 1 user edit câu hỏi của user khác
					$auth = Zend_Auth::getInstance();
					if ($auth->hasIdentity()){
						$userhaslogin = $auth->getStorage()->read();
						if($userhaslogin->group_id!=5) // chỗ này để cho super admin có toàn quyền sửa các câu hỏi của người khác
							if($this->_question->getCreated_user()!= $userhaslogin->id)
								throw new Exception("Bạn không được phép chỉnh sửa câu hỏi của người khác.");
					}
					// END Xử lý không cho 1 user edit câu hỏi của user khác					
					
					$question_text = $this->_question->getQuestiontext();
					$this->_XML = new QuestionXML($question_text);
					$question_type = $this->_XML->getQuestionType();
					switch ($question_type){
						case 1: 
								$this->_redirect("question/edittruefalse/id/".$question_id);
								break;
						case 2: 
								$this->_redirect("question/editmultichoice/id/".$question_id);
								break;
						case 3: 
								$this->_redirect("question/editmatching/id/".$question_id);
								break;
						case 4: 
								$this->_redirect("question/editcompletion/id/".$question_id);
								break;
						case 5: 
								$this->_redirect("question/editessaytest/id/".$question_id);
								break;
						case 6: 
								$this->_redirect("question/editshortanswer/id/".$question_id);
								break;
						default: $this->_redirect("error/error");
					}
				}else{
					$this->_redirect("error/error");
				}				
			}
		}catch(Exception $ex){
			$this->_arrError[] = $ex->getMessage();  
			$this->view->arrError = $this->_arrError; 			
		}
	}
	
	public function editsuccessAction(){
		
	}
	
/*----------------------------------------------------------------------------------------------------------*
 *-------------------------------------- 1 MULTICHOICE QUESTION --------------------------------------------*
 *----------------------------------------------------------------------------------------------------------*/	
	
	public function addmultichoiceAction(){
//		$data = array();
//                $this->_question  = new Default_Models_Question();
//            $data['questiontext']		= 'test phat';
//            $data['type_question']		= '123';
//            $this->_question->setOptions($data);
//            $this->_question->save();
//            var_dump($this->_question->id);die;            
		$this->view->action = "editmultichoice";
		$this->view->nameTypeQuestion = "Câu hỏi nhiều lựa chọn";
		$ObjQuestion  = Array();
		$ObjQuestion['type']  = 2;
		$ObjQuestion['score'] = 1;
		$ObjQuestion['level'] = 0.5;
		$ObjQuestion['classification'] = 0.2;
		$ObjQuestion['subject_id'] = "";
		$ObjQuestion['chapter_id'] = "";
		$ObjQuestion['question_title'] = "";
		$ObjQuestion['content'] = "";
		$ObjQuestion['generalfeedback'] = "";
		$ObjQuestion['oneormoretrue'] = 0;
		$ObjQuestion['hidden'] = '';
		$ObjQuestion['id'] = '';
                $ObjQuestion['created_user'] = $this->getUserId();
		
		$ObjQuestion['isupdate'] = 0; // insert new question
		$this->view->ObjQuestion = $ObjQuestion;
		$this->renderquestion(2);
	}
	
	public function editmultichoiceAction(){
		$this->view->action = "editmultichoice";
		$this->view->nameTypeQuestion = "Câu hỏi nhiều lựa chọn";
		try{
		$request = $this->getRequest();
		if ($request->isPost()) {
		 
			// get all atrribute and validate
				$data = $request->getParams();
				$data = $this->_filter($data);
				$question_type 		= $data['type'];
				$quesID 			= $data['id'];
				$question_title  	= $data['question_title'];
				$content  			= $data['content'];
				$level  			= $data['level'];
				$classification  	=  isset($data['classification'])?$data['classification']:"";
				$score  			= $data['score'];
				$generalfeedback  	= $data['generalfeedback'];
				$oneormoretrue 		= $data['oneormoretrue'];
				$shuffle  			= isset($data['shuffle'])?$data['shuffle']:"";
				$answernumbering  	= isset($data['answernumbering'])?$data['answernumbering']:0;
				$displayanswer  	= isset($data['displayanswer'])?$data['displayanswer']:0;
				$hidden 			= $data['hidden'];
				// Array 
				$perscore  		= $data['perscore'];
				$ansID  		= $data['ansID'];
				$ans 			= $data['ans'];
				$feedback  		= $data['feedback'];
				// validate data nếu có lỗi thì thông báo trả lại form 
				if($data['isupdate']==1)
					$this->_validate($data,true);
				else 
					$this->_validate($data,false);
					
				if(count($this->_arrError)>0){					
					$this->view->arrError = $this->_arrError;
					
					$this->view->ObjQuestion = $data;
					
					$this->renderquestion($question_type);
										
				}else{ // Process Edit Question
					
					// Xử lý chuẩn hóa đường dẫn của hình khi upload image bằng tinyMce
					// thành dạng "../../../media/images/tinymce/giangvien/8.JPG" để dễ kiểm soát hơn <Cách này Chỉ là tình thế, ko hay> 
					$data =  $this->_getStandardImagePath($data);
					$ans 			= $data['ans'];
					// End upload image bằng tinyMce**********
					
					$XML = new QuestionXML();
					$XML->setQuestionType($question_type);
					$XML->setScoreToXml($score);
					$XML->setOneOrMoreTrueToXml($oneormoretrue);
					$XML->setShuffleToXml($shuffle);
					$XML->setDisplayAnswerToXml($displayanswer);
                                        
					
					$this->_question = new Default_Models_Question();
					$this->_question->find("id",$quesID);
					$allAnswers = Array();
					if($this->_question->getId()){
						$question_textXML  = $this->_question->getQuestiontext();
						$XML1 		= new QuestionXML($question_textXML);
						$allAnswers = $XML1->getAllAnswers();
					}
					//1. xu li cac answer bi xoa.
					if(count($allAnswers)>0)
					foreach($allAnswers as $key=>$ansItem){
						if(!in_array($ansItem['id'],$ansID)){ // id trong mảng ở DB không tồn tại trong mảng do người dùng post lên=> xóa 1 câu trả lời
							// step 1: remove on database
							$this->_answer = new Default_Models_Answer();
							$this->_answer->delete('id',$ansItem['id']);
						}							
					}
					$allAnswersID  = Array();
					if(count($allAnswers)>0)					
					foreach($allAnswers as $ansItem)
						$allAnswersID[]  = $ansItem['id'];
										
					//2. Xử lý các answer  bị chỉnh sửa
					foreach ($ansID as $key=>$ansIDItem){
						if(in_array($ansIDItem,$allAnswersID)){// <=> update answer							
							$this->_answer = new Default_Models_Answer();															
							$this->_answer->setId($ansIDItem);							
							$this->_answer->setAns_content($ans[$key]);
							$this->_answer->setFeedback($feedback[$key]);								
							$this->_answer->save();							
						}
					}
					//3 Xử lý thêm mới answer
					foreach ($ansID as $key=>$ansIDItem){
						if(empty($ansIDItem)){// <=> insert answer		
							$this->_answer = new Default_Models_Answer();															
							$this->_answer->setAns_content($ans[$key]);
							$this->_answer->setFeedback($feedback[$key]);
							$last_answer_id   = $this->_answer->save();
							$ansID[$key]      = $last_answer_id;
						}
					}
					//4. Xây dựng lại 
					foreach ($ans as $key=>$ansItem){					
						$XML->setOneAnswer(Array('id'=>$ansID[$key],'perscore'=>$perscore[$key],'order'=>$key));
					}
					$this->_question  = new Default_Models_Question();
//					$data['created_user']		= $this->getUserId();
					$data['questiontext']		= $XML->ParseXML() ;
					$data['type_question']		= $question_type ;
                                        
					$this->_question->setOptions($data);
//                                        echo $this->_question->questiontext;die;
					if(empty($data['id']))
						$this->_question->setId(null);	
										
					$this->_question->save();
//                                        var_dump($this->_question->id);die;
					$this->view->editsuccess = "success" ;
					$this->view->question_id = $quesID;
					if($data['isupdate']==0)
						$this->_redirect("/question");
					else
						$this->render("editsuccess"); 					 
					
				}
		}elseif($request->isGet()) {
			$data = $request->getParams();
			$id   = $data['id'];
			// ->get type cau hoi 
			$this->_question 	 = new Default_Models_Question();
			$result = $this->_question->find("id",$id);  			
                        if(!is_null($result)){     
				// sau khi toArray thi` 1 so truong trong question da vao` mang ObjQuestion roi`
				// con` lai cac thong tin answers ta chen` vao` mang o doan sau
					
				$ObjQuestion = $this->_question->toArray() ;				
				
				$question_textXML = $this->_question->getQuestiontext();
				$this->_XML = new QuestionXML($question_textXML);
				
				$ObjQuestion['isupdate'] = 1;
				$ObjQuestion['type']  = $this->_XML->getQuestionType();
				$ObjQuestion['score'] = $this->_XML->getScoreFromXml();
				$ObjQuestion['oneormoretrue']  = $this->_XML->getOneOrMoreTrueFromXml();
				$ObjQuestion['shuffle']		   = $this->_XML->getShuffleFromXml();
				$ObjQuestion['displayanswer']  = $this->_XML->getDisplayAnswerFromXml();
				//$ObjQuestion['answernumbering'] = $this->_XML->get
				$allAnswers   = $this->_XML->getAllAnswers();
				
				// lọc lấy các thông tin 
				$ans  		= Array();       // array answer content
				$perscore 	= Array();		// array perscore 
				$ansID   	= Array();         // array answer id
				$feedback  	= Array();       // array feedback of answer 
				
				foreach($allAnswers as $ansItem){
					$ansID[]      = $ansItem['id'];
					$perscore[]   = $ansItem['perscore'];
					$order[]	  = $ansItem['order'];
					
					$this->_answer       = new Default_Models_Answer();
					$this->_answer->find("id",$ansItem['id']);
					if($this->_answer->getId()){
						$feedback[] = $this->_answer->getFeedback();
						$ans[] 		= $this->_answer->getAns_content();
					}										
				}   
				
				$ObjQuestion['ans']    = $ans;
				$ObjQuestion['ansID']  = $ansID;
				$ObjQuestion['perscore'] = $perscore;
				$ObjQuestion['feedback'] = $feedback;				
				$this->view->ObjQuestion = $ObjQuestion;
				
				$this->renderquestion($ObjQuestion['type']);
			}
		}
		}catch (Exception $ex) {
			echo $ex->getMessage();
		}
	
	}
	
/*----------------------------------------------------------------------------------------------------------*
 *-------------------------------------- 2 TRUE FALSE QUESTION ---------------------------------------------*
 *----------------------------------------------------------------------------------------------------------*/	
	
	public function addtruefalseAction(){
		$this->view->action = "edittruefalse";
		$this->view->nameTypeQuestion = "Câu hỏi đúng sai";
		$ObjQuestion  = Array();
		$ObjQuestion['type'] = 1;
		$ObjQuestion['score'] = 1;
		$ObjQuestion['level'] = 0.5;
		$ObjQuestion['classification'] = 0.2;
		$ObjQuestion['subject_id'] = "";
		$ObjQuestion['chapter_id'] = "";
		$ObjQuestion['question_title'] = "";
		$ObjQuestion['content'] = "";
		$ObjQuestion['generalfeedback'] = "";
		$ObjQuestion['answertrue'] = 1;
                $ObjQuestion['hidden'] = '';
                $ObjQuestion['id'] = '';
                $ObjQuestion['created_user'] = $this->getUserId();
                
		$ObjQuestion['isupdate'] = 0; // insert new question
		$this->view->ObjQuestion = $ObjQuestion;
		$this->renderquestion(1);
	}

	public function edittruefalseAction(){
		$this->view->action = "edittruefalse";
		$this->view->nameTypeQuestion = "Câu hỏi đúng sai";
		try{
		$request = $this->getRequest();
		if ($request->isPost()) {
			// get all atrribute and validate
				$data = $request->getParams();
				$data = $this->_filter($data);
				$question_type 		= $data['type'];
				$quesID 			= $data['id'];
				$question_title  	= $data['question_title'];
				$content  			= $data['content'];
				$level  			= $data['level'];
				$classification  	= $data['classification'];
				$score  			= $data['score'];
				$answertrue  		= $data['answertrue'];
				$generalfeedback  	= $data['generalfeedback'];
				$hidden 			= $data['hidden'];
				// Array 
				$ansID  		= $data['ansID'];
				$ans 			= $data['ans'];
				$feedback  		= $data['feedback'];
				
				// validate data nếu có lỗi thì thông báo trả lại form 
				if($data['isupdate']==1)
					$this->_validate($data,true);
				else 
					$this->_validate($data,false);
					
				if(count($this->_arrError)>0){					
					$this->view->arrError = $this->_arrError;
					
					$this->view->ObjQuestion = $data;
					
					$this->renderquestion($question_type);
										
				}else{ // Process Add and Edit Question làm chung
					
					// Xử lý chuẩn hóa đường dẫn của hình khi upload image bằng tinyMce
					// thành dạng "../../../media/images/tinymce/giangvien/8.JPG" để dễ kiểm soát hơn <Cách này Chỉ là tình thế, ko hay> 
					$data =  $this->_getStandardImagePath($data);
					$ans 			= $data['ans'];
					// End upload image bằng tinyMce**********
					
					$XML = new QuestionXML();
					$XML->setQuestionType($question_type);
					$XML->setScoreToXml($score);
					$XML->setIscorrectToXml($answertrue);
					
					$this->_question = new Default_Models_Question();
					$this->_question->find("id",$quesID);
					$allAnswers = Array();
					if($this->_question->getId()){
						$question_textXML  = $this->_question->getQuestiontext();
						$XML1 		= new QuestionXML($question_textXML);
						$allAnswers = $XML1->getAllAnswers();
					}
					//1. xu li cac answer bi xoa.
					if(count($allAnswers)>0)
					foreach($allAnswers as $key=>$ansItem){
						if(!in_array($ansItem['id'],$ansID)){ // id trong mảng ở DB không tồn tại trong mảng do người dùng post lên=> xóa 1 câu trả lời
							// step 1: remove on database
							$this->_answer = new Default_Models_Answer();
							$this->_answer->delete('id',$ansItem['id']);
						}							
					}
					$allAnswersID  = Array();
					if(count($allAnswers)>0)					
					foreach($allAnswers as $ansItem)
						$allAnswersID[]  = $ansItem['id'];
										
					//2. Xử lý các answer  bị chỉnh sửa
					foreach ($ansID as $key=>$ansIDItem){
						if(in_array($ansIDItem,$allAnswersID)){// <=> update answer							
							$this->_answer = new Default_Models_Answer();															
							$this->_answer->setId($ansIDItem);							
							$this->_answer->setAns_content($ans[$key]);
							$this->_answer->setFeedback($feedback[$key]);								
							$this->_answer->save();							
						}
					}
					//3 Xử lý thêm mới answer
					foreach ($ansID as $key=>$ansIDItem){
						if(empty($ansIDItem)){// <=> insert answer		
							$this->_answer = new Default_Models_Answer();															
							$this->_answer->setAns_content($ans[$key]);
							$this->_answer->setFeedback($feedback[$key]);
							$last_answer_id   = $this->_answer->save();
							$ansID[$key]      = $last_answer_id;
						}
					}
					//4. Xây dựng lại 
					foreach ($ans as $key=>$ansItem){					
						$XML->setOneAnswer(Array('id'=>$ansID[$key],'perscore'=>$perscore[$key],'order'=>$key));
					}
					$this->_question  = new Default_Models_Question();
					//$data['created_user']		= $this->getUserId();
					$data['questiontext']		= $XML->ParseXML() ;
					$data['type_question']		= $question_type ;

					$this->_question->setOptions($data);
					if(empty($data['id']))
						$this->_question->setId(null);	
										
					$this->_question->save();					
					$this->view->editsuccess = "success" ;
					$this->view->question_id = $quesID;
					if($data['isupdate']==0)
						$this->_redirect("/question");
					else
						$this->render("editsuccess"); 					 
					 				 
									}
		}elseif($request->isGet()) {
			$data = $request->getParams();
			$id   = $data['id'];
			// ->get type cau hoi 
			$this->_question 	 = new Default_Models_Question();
			$result = $this->_question->find("id",$id);			
                        if(!is_null($result)){
				// sau khi toArray thi` 1 so truong trong question da vao` mang ObjQuestion roi`
				// con` lai cac thong tin answers ta chen` vao` mang o doan sau
				$ObjQuestion = $this->_question->toArray() ;
				$question_textXML = $this->_question->getQuestiontext();
				$this->_XML = new QuestionXML($question_textXML);
				
				$ObjQuestion['isupdate'] = 1;
				$ObjQuestion['type']  			= $this->_XML->getQuestionType();
				$ObjQuestion['score']			= $this->_XML->getScoreFromXml();
				$ObjQuestion['answertrue'] 		= $this->_XML->getIscorrectFromXml();
				
				$allAnswers   = $this->_XML->getAllAnswers();
				
				// lọc lấy các thông tin 
				$ans  		= Array();       // array answer content
				$perscore 	= Array();		// array perscore 
				$ansID   	= Array();       // array answer id
				$feedback  	= Array();       // array feedback of answer 
				
				foreach($allAnswers as $ansItem){
					$ansID[]      	  = $ansItem['id'];
					if (isset($ansItem['correct']) && !is_null($ansItem['correct']))
						$answertrue[]	  = $ansItem['correct'];
					
					$this->_answer       = new Default_Models_Answer();
					$this->_answer->find("id",$ansItem['id']);
					if($this->_answer->getId()){
						$feedback[] = $this->_answer->getFeedback();
						$ans[] 		= $this->_answer->getAns_content();
					}										
				}   

				$ObjQuestion['ans']    		= $ans;
				$ObjQuestion['ansID']  		= $ansID;
				$ObjQuestion['feedback'] 	= $feedback;				
				$this->view->ObjQuestion 	= $ObjQuestion;
				
				$this->renderquestion($ObjQuestion['type']);
			}
		}
		}catch (Exception $ex) {
			echo $ex->getMessage();
		}
	
	}	

/*----------------------------------------------------------------------------------------------------------*
 *-------------------------------------- 3 MATCHING QUESTION -----------------------------------------------*
 *----------------------------------------------------------------------------------------------------------*/	
	
	public function addmatchingAction(){
		$this->view->action = "editmatching";
		$this->view->nameTypeQuestion = "Câu hỏi ghép cặp đôi";
		$ObjQuestion  = Array();
		$ObjQuestion['type'] = 3;
		$ObjQuestion['score'] = 1;
		$ObjQuestion['level'] = 0.5;
		$ObjQuestion['classification'] = 0.2;
		$ObjQuestion['isupdate'] = 0; // insert new question
		$ObjQuestion['subject_id'] = "";
		$ObjQuestion['chapter_id'] = "";
		$ObjQuestion['question_title'] = "";
		$ObjQuestion['id'] = '';
		$ObjQuestion['content'] = "";
		$ObjQuestion['generalfeedback'] = "";
		$ObjQuestion['hidden'] = '';
                $ObjQuestion['created_user'] = $this->getUserId();
                
		$this->view->ObjQuestion = $ObjQuestion;
		$this->renderquestion(3);
	}
	
	public function editmatchingAction(){
		$this->view->action = "editmatching";
		$this->view->nameTypeQuestion = "Câu hỏi ghép cặp đôi";
		try{
				$request = $this->getRequest();
				if ($request->isPost()) {
					// get all atrribute and validate
						$data = $request->getParams();

						$data = $this->_filter($data);
						$question_type 		= $data['type'];
						$quesID 			= $data['id'];
						$question_title  	= $data['question_title'];
						$content  			= $data['content'];
						$level  			= $data['level'];
						$classification  	= isset($data['classification'])?$data['classification']:"";
						$score  			= $data['score'];
						$generalfeedback  	= $data['generalfeedback'];
						$hidden 			= $data['hidden'];
						// Array 
						$ansID  		= $data['ansID'];
						$ans 			= $data['ans'];
						$part			= $data['part'];
						$order			= isset($data['order'])?$data['order']:array();
						$couple_matching= $data['couple-matching'];
						 
						// validate data nếu có lỗi thì thông báo trả lại form 
						if($data['isupdate']==1)
							$this->_validate($data,true);
						else 
							$this->_validate($data,false);
							
						if(count($this->_arrError)>0){					
							$this->view->arrError = $this->_arrError;
							$this->view->ObjQuestion = $data;
							
							$this->renderquestion($question_type);
												
						}else{ // Process Edit Question
							
							// Xử lý chuẩn hóa đường dẫn của hình khi upload image bằng tinyMce
							// thành dạng "../../../media/images/tinymce/giangvien/8.JPG" để dễ kiểm soát hơn <Cách này Chỉ là tình thế, ko hay> 
							$data =  $this->_getStandardImagePath($data);
							$ans 			= $data['ans'];
							// End upload image bằng tinyMce**********
							
							$XML = new QuestionXML();
							$XML->setQuestionType($question_type);
							$XML->setScoreToXml($score);
							
							$this->_question = new Default_Models_Question();
							$this->_question->find("id",$quesID);
							// allAnswers là tất cả các ans cũ trong DB
							$allAnswers = Array();
							if($this->_question->getId()){
								$question_textXML  = $this->_question->getQuestiontext();
								$XML1 		= new QuestionXML($question_textXML);
								$allAnswers = $XML1->getAllAnswers();
							}
							/*
							//1. xu li cac answer bi xoa.
							if(count($allAnswers)>0)
							foreach($allAnswers as $key=>$ansItem){
								if(!in_array($ansItem['id'],$ansID)){ // id trong mảng ở DB không tồn tại trong mảng do người dùng post lên=> xóa 1 câu trả lời
									// step 1: remove on database
									$this->_answer = new Default_Models_Answer();
									$this->_answer->delete('id',$ansItem['id']);
								}							
							}
							*/
							// lấy ra tất cả các ans_id cũ trong DB
							$allAnswersID  = Array();
							if(count($allAnswers)>0)					
							foreach($allAnswers as $ansItem)
								$allAnswersID[]  = $ansItem['id'];
							//2. Xử lý các answer  bị chỉnh sửa
							foreach ($ansID as $key=>$ansIDItem){
								if(in_array($ansIDItem,$allAnswersID)){// <=> update answer	
									$this->_answer = new Default_Models_Answer();															
									$this->_answer->setId($ansIDItem);							
									$this->_answer->setAns_content($ans[$key]);														
									$this->_answer->save();							
								}
							}
							
							//3 Xử lý thêm mới answer
							foreach ($ansID as $key=>$ansIDItem){
								if(empty($ansIDItem)){// <=> insert answer		
									$this->_answer = new Default_Models_Answer();															
									$this->_answer->setAns_content($ans[$key]);
									$last_answer_id   = $this->_answer->save();
									$ansID[$key]      = $last_answer_id;
								}
							}
							$maxOrderA = 0;
							$maxOrderB = 0;
							//4. Xây dựng lại question XML
							foreach ($ans as $key=>$ansItem){
								if($part[$key]=="A"){
									$maxOrderA++;
									$XML->setOneAnswer(Array('id'=>$ansID[$key],'order'=>$maxOrderA,'part'=>$part[$key]));
								}
								else{
									$maxOrderB++;
									$XML->setOneAnswer(Array('id'=>$ansID[$key],'order'=>$maxOrderB,'part'=>$part[$key]));
								}			
							}
							$this->_question  = new Default_Models_Question();
							//$data['created_user']		= $this->getUserId();
							$data['questiontext']		= $XML->ParseXML() ;
							$data['type_question']		= $question_type ;
							$this->_question->setOptions($data);
							//nếu data['id' = rỗng <=> insert
							$is_insert        = false; 
							if(empty($data['id'])){
								$this->_question->setId(null);
								$is_insert = true;
							}						
							if($is_insert)
							 $quesID = $this->_question->save();					
							else 
								$this->_question->save();
							
							// xử lý phần couple matching
							// sau khi thực hiện insert,update trong phần question XML đã có đầy đủ thông tin của các answers
							// do client post lên dạng A1-B2 => cách đơn giản nhất chui vào DB lấy ra
							// và phần Correct lưu dưới dạng ans_id_1 - ans_id_2
							// mục đích: móc 2 cái ans_id cho vào correct
							
							$this->_question = new Default_Models_Question();
							$this->_question->find("id",$quesID);
							// allAnswers là tất cả các ans cũ trong DB
							$allAnswers = Array();
							if($this->_question->getId()){
								$question_textXML  = $this->_question->getQuestiontext();
								$XML1 		= new QuestionXML($question_textXML);
								$allAnswers = $XML1->getAllAnswers();
							}
														 
							if(count($couple_matching)>0){
								// parse A1-B2 => id1-id2
								foreach ($couple_matching as $couple_matchingItem){
										$arr     = explode('-',$couple_matchingItem);
										foreach ($allAnswers as $allAnswersItem){
											if($allAnswersItem["part"].$allAnswersItem["order"]==$arr[0])
												$id1  = $allAnswersItem["id"];
											if($allAnswersItem["part"].$allAnswersItem["order"]==$arr[1])
												$id2  = $allAnswersItem["id"];
										}
										$XML1->setOneCoupleMatchingCorrect(Array("id1"=>$id1,"id2"=>$id2));
								}
							}
							if(count($allAnswers)){
								foreach ($allAnswers as $allAnswersItem){
									$ans_id = $allAnswersItem["id"];
									$models_answer = new Default_Models_Answer();
									if($models_answer->find("id",$ans_id)!=0){
										$ans_content = $models_answer->getAns_content();
										if(empty($ans_content)){
											// ans content rỗng => xóa luôn ans này
											$models_answer->delete("id",$ans_id);
											// xóa couple matching ans
											$XML1->deleteOneAnswer($ans_id);
											$XML1->deleteOneCoupleMatchingCorrect($ans_id,$ans_id);
										}
									}
								}
							}
							$allAnswers = $XML1->getAllAnswers();
							if(count($allAnswers)>0){
								$indexA = 1;
								$indexB = 1;
								foreach ($allAnswers as $allAnswersItem){
									if($allAnswersItem["part"]=="A"){
										$ans_arr = Array("id"=>$allAnswersItem['id'],"order"=>$indexA,"part"=>$allAnswersItem['part']);
										$indexA++;
									}else{
										$ans_arr = Array("id"=>$allAnswersItem['id'],"order"=>$indexB,"part"=>$allAnswersItem['part']);
										$indexB++;										
									}
									$XML1->setOneAnswer($ans_arr);
								}
							}
							$this->_question->setQuestiontext($XML1->ParseXML());
							
							$this->_question->save();		
							$this->view->editsuccess = "success" ;
							$this->view->question_id = $quesID;
							if($data['isupdate']==0)
								$this->_redirect("/question");
							else
								$this->render("editsuccess"); 					 
									 	 					
													}
				}elseif($request->isGet()){
						$data = $request->getParams();
						$id   = $data['id'];
						// ->get type cau hoi 
						$this->_question 	 = new Default_Models_Question();
						$result = $this->_question->find("id",$id);  
                                                if(!is_null($result)){
							// sau khi toArray thi` 1 so truong trong question da vao` mang ObjQuestion roi`
							// con` lai cac thong tin answers ta chen` vao` mang o doan sau
								
							$ObjQuestion = $this->_question->toArray() ;				
							
							$question_textXML = $this->_question->getQuestiontext();
							$this->_XML = new QuestionXML($question_textXML);
							
							$ObjQuestion['isupdate'] = 1;
							$ObjQuestion['type']  = $this->_XML->getQuestionType();
							$ObjQuestion['score'] = $this->_XML->getScoreFromXml();
							$allAnswers   = $this->_XML->getAllAnswers();
							$allCoupleMatchingCorrect = $this->_XML->getAllMatchingCoupleCorrect("AnsCorrect");
							
							// lọc lấy các thông tin 
							$ans  		= Array();       // array answer content
							$ansID   	= Array();  // array answer id
							$part     = Array();     // array part ex: A,B  
							
							foreach($allAnswers as $ansItem){
								$ansID[]      = $ansItem['id'];
								$part[]  	  = $ansItem['part'];
								$order[]	  = $ansItem['order'];								
								$this->_answer       = new Default_Models_Answer();
								$this->_answer->find("id",$ansItem['id']);
								if($this->_answer->getId()){
									$ans[] 		= $this->_answer->getAns_content();
								}										
							}   
							// xử lý đẩy matching couple xuống view có dạng A1-B2
							// chứ ko phải là id1-id2
							$couple_matching = array();
							if(count($allCoupleMatchingCorrect)>0){
								foreach ($allCoupleMatchingCorrect as $item){
									$id1 = $item['id1'];
									$id2 = $item['id2'];
									$str = "";
									// tìm id1 trong mảng ansID
									$keyPos = array_search($id1, $ansID);
									$str.="A".$order[$keyPos].'-';
									
									$keyPos = array_search($id2, $ansID);
									$str.="B".$order[$keyPos];
									$couple_matching[] = $str;
								}
							}
							$ObjQuestion['ans']    = $ans;
							$ObjQuestion['ansID']  = $ansID;
							$ObjQuestion['part']   = $part;
							$ObjQuestion['order']  = $order;
							$ObjQuestion['couple-matching'] = $couple_matching;
							$this->view->ObjQuestion = $ObjQuestion;
							
							$this->renderquestion($ObjQuestion['type']);		
						}
				}
		}catch(Exception $ex){
			echo $ex->getMessage();die();
		}
	}
	
/*----------------------------------------------------------------------------------------------------------*
 *-------------------------------------- 4 COMPLETION TESTS  -----------------------------------------------*
 *----------------------------------------------------------------------------------------------------------*/	

	public function addcompletionAction(){
		$this->view->action = "editcompletion";
		$this->view->nameTypeQuestion = "Câu hỏi điền khuyết";
		$ObjQuestion  = Array();
		$ObjQuestion['type'] = 4;
		$ObjQuestion['score'] = 1;
		$ObjQuestion['level'] = 0.5;
		$ObjQuestion['classification'] = 0.2;
		$ObjQuestion['isupdate'] = 0; // insert new question
		$ObjQuestion['subject_id'] = "";
		$ObjQuestion['chapter_id'] = "";
		$ObjQuestion['question_title'] = "";
		$ObjQuestion['id'] = '';
		$ObjQuestion['content'] = "";
		$ObjQuestion['generalfeedback'] = "";
		$ObjQuestion['hidden'] = '';
		$ObjQuestion['created_user'] = $this->getUserId();
                
		$this->view->ObjQuestion = $ObjQuestion;
		$this->renderquestion(4);
	}
	
	public function editcompletionAction(){
		$this->view->action = "editcompletion";
		$this->view->nameTypeQuestion = "Câu hỏi điền khuyết";
		try{
				$request = $this->getRequest();
				if ($request->isPost()) {
			// get all atrribute and validate
				$data = $request->getParams();
				$data = $this->_filter($data);			
				$question_type 		= $data['type'];
				$quesID 			= $data['id'];
				$question_title  	= $data['question_title'];
				$content  			= $data['content'];
				$level  			= $data['level'];
				$classification  	= isset($data['classification'])?$data['classification']:"";
				$score  			= isset($data['score'])?$data['score']:0;
				$hidden 			= $data['hidden'];
				// Array 
				$ansID  		= isset($data['ansID'])?$data['ansID']:array();
				$ans 			= isset($data['ans'])?$data['ans']:array();
				
				// validate data nếu có lỗi thì thông báo trả lại form 
				if($data['isupdate']==1)
					$this->_validate($data,true);
				else 
					$this->_validate($data,false);
					
				if(count($this->_arrError)>0){					
					$this->view->arrError = $this->_arrError;
					$this->view->ObjQuestion = $data;
					$this->renderquestion($question_type);
										
				}else{ // Process Add and Edit Question làm chung
					
						// Xử lý chuẩn hóa đường dẫn của hình khi upload image bằng tinyMce
						// thành dạng "../../../media/images/tinymce/giangvien/8.JPG" để dễ kiểm soát hơn <Cách này Chỉ là tình thế, ko hay> 
						$data =  $this->_getStandardImagePath($data);
						$ans 			= $data['ans'];
						// End upload image bằng tinyMce**********
					
						$XML = new QuestionXML();
						$XML->setQuestionType($question_type);
						$XML->setScoreToXml($score);
						
						$this->_question = new Default_Models_Question();
						$this->_question->find("id",$quesID);
						$allAnswers = Array();
						if($this->_question->getId()){
							// Nếu edit thì load đoạn xml ra để edit
							$question_textXML  = $this->_question->getQuestiontext();
							$XML1 		= new QuestionXML($question_textXML);
							$allAnswers = $XML1->getAllAnswers();
						}
						//1. xu li xoa cac answer 
						if(count($allAnswers)>0)
							foreach($allAnswers as $key=>$ansItem){
								//  xóa toàn bộ câu trả lời của câu hỏi này trong bảng answer
									// step 1: remove on database
									$this->_answer = new Default_Models_Answer();
									$this->_answer->delete('id',$ansItem['id']);
							}
						//2 Xử lý thêm mới answer
						if(count($ans)>0)
						foreach ($ans as $key=>$ansItem){
								$this->_answer = new Default_Models_Answer();															
								$this->_answer->setAns_content($ansItem);
								//$this->_answer->setFeedback($feedback[$key]);
								$last_answer_id   = $this->_answer->save();
								$ansID[$key]      = $last_answer_id;
						}
						//4. Xây dựng lại 
						if(count($ans)>0)
						foreach ($ans as $key=>$ansItem){					
							$XML->setOneAnswer(Array('id'=>$ansID[$key]));
						}
						$this->_question  			= new Default_Models_Question();
						//$data['created_user']		= $this->getUserId();
						$data['questiontext']		= $XML->ParseXML() ;
						$data['type_question']		= $question_type ;
						$this->_question->setOptions($data);
						if(empty($data['id']))
							$this->_question->setId(null);	
											
						$this->_question->save();					
						$this->view->editsuccess = "success" ;
						$this->view->question_id = $quesID;
						if($data['isupdate']==0)
							$this->_redirect("/question");
						else
							$this->render("editsuccess"); 					 
							 	 		 
											}
				}elseif($request->isGet()){
						$data = $request->getParams();
						$id   = $data['id'];
						// ->get type cau hoi 
						$this->_question 	 = new Default_Models_Question();
						$result = $this->_question->find("id",$id);  
                                                if(!is_null($result)){
							// sau khi toArray thi` 1 so truong trong question da vao` mang ObjQuestion roi`
							// con` lai cac thong tin answers ta chen` vao` mang o doan sau
								
							$ObjQuestion = $this->_question->toArray() ;				
							
							$question_textXML = $this->_question->getQuestiontext();
							$this->_XML = new QuestionXML($question_textXML);
							
							$ObjQuestion['isupdate'] = 1;
							$ObjQuestion['type']  = $this->_XML->getQuestionType();
							$ObjQuestion['score'] = $this->_XML->getScoreFromXml();
							$allAnswers   = $this->_XML->getAllAnswers();
							$allCoupleMatchingCorrect = $this->_XML->getAllMatchingCoupleCorrect("AnsCorrect");
							
							// lọc lấy các thông tin 
							$ans  		= Array();       // array answer content
							$ansID   	= Array();       // array answer id
							
							foreach($allAnswers as $ansItem){
								$ansID[]      = $ansItem['id'];
								$this->_answer       = new Default_Models_Answer();
								$this->_answer->find("id",$ansItem['id']);
								if($this->_answer->getId()){
									$ans[] 		= $this->_answer->getAns_content();
								}										
							}   
							$ObjQuestion['ans']    = $ans;
							$ObjQuestion['ansID']  = $ansID;
							$this->view->ObjQuestion = $ObjQuestion;
							$this->renderquestion($ObjQuestion['type']);							
					}
				}
		}catch(Exception $ex){
			echo $ex->getMessage();
			die();			
		}
	}
	
	
/*----------------------------------------------------------------------------------------------------------*
 *-------------------------------------- 5 ESSAY TEST ------------------------------------------------------*
 *----------------------------------------------------------------------------------------------------------*/	
	
	
	public function addessaytestAction(){
		$this->view->action = "editessaytest";
		$this->view->nameTypeQuestion = "Câu hỏi tự luận";
		$ObjQuestion  = Array();
		$ObjQuestion['type'] = 5;
		$ObjQuestion['score'] = 1;
		$ObjQuestion['level'] = 0.5;
		$ObjQuestion['classification'] = 0.2;
		$ObjQuestion['subject_id'] = "";
		$ObjQuestion['chapter_id'] = "";
		$ObjQuestion['question_title'] = "";
		$ObjQuestion['id'] = '';
		$ObjQuestion['content'] = "";
		$ObjQuestion['generalfeedback'] = "";
		$ObjQuestion['hidden'] = '';
		$ObjQuestion['answer_content'] = '';
		$ObjQuestion['feedback'] = '';
                $ObjQuestion['created_user'] = $this->getUserId();
                
				
		$ObjQuestion['isupdate'] = 0; // insert new question
		$this->view->ObjQuestion = $ObjQuestion;
		$this->renderquestion(5);
	}

	public function editessaytestAction(){
		$this->view->action = "editessaytest";
		$this->view->nameTypeQuestion = "Câu hỏi tự luận";
		try{
				$request = $this->getRequest();
		if ($request->isPost()) {
			// get all atrribute and validate
				$data = $request->getParams();
				$data = $this->_filter($data);			
				$question_type 		= $data['type'];
				$quesID 			= $data['id'];
				$question_title  	= $data['question_title'];
				$content  			= $data['content'];
				$level  			= $data['level'];
				$classification  	= isset($data['classification'])?$data['classification']:0;
				$score  			= $data['score'];
				$hidden 			= $data['hidden'];
				// Array 
				$ansID  		= $data['ansID'];
				$ans 			= $data['ans'];
				$feedback  		= $data['feedback'];
				$data['answer_content']    = $ans[0];
				$data['feedback']    	  = $feedback[0];
				$data['ansID'] 		 	  = $ansID[0];
				
				// validate data nếu có lỗi thì thông báo trả lại form 
				if($data['isupdate']==1)
					$this->_validate($data,true);
				else 
					$this->_validate($data,false);
				if(count($this->_arrError)>0){					
					$this->view->arrError = $this->_arrError;
					
					$this->view->ObjQuestion = $data;
					
					$this->renderquestion($question_type);
										
				}else{ // Process Add and Edit Question làm chung
					
					// Xử lý chuẩn hóa đường dẫn của hình khi upload image bằng tinyMce
					// thành dạng "../../../media/images/tinymce/giangvien/8.JPG" để dễ kiểm soát hơn <Cách này Chỉ là tình thế, ko hay> 
					$data =  $this->_getStandardImagePath($data);
					$ans 			= $data['ans'];
					// End upload image bằng tinyMce**********
					
					$XML = new QuestionXML();
					$XML->setQuestionType($question_type);
					$XML->setScoreToXml($score);
					
					$this->_question = new Default_Models_Question();
					$this->_question->find("id",$quesID);
					$allAnswers = Array();
					if($this->_question->getId()){
						$question_textXML  = $this->_question->getQuestiontext();
						$XML1 		= new QuestionXML($question_textXML);
						$allAnswers = $XML1->getAllAnswers();
					}
					$allAnswersID  = Array();
					
					if(count($allAnswers)>0)					
					foreach($allAnswers as $ansItem)
						$allAnswersID[]  = $ansItem['id'];
										
					//2. Xử lý  answer  bị chỉnh sửa
					if(!empty($ansID[0]) && is_numeric($ansID[0])){
						foreach ($ansID as $key=>$ansIDItem){
							if(in_array($ansIDItem,$allAnswersID)){// <=> update answer							
								$this->_answer = new Default_Models_Answer();															
								$this->_answer->setId($ansIDItem);							
								$this->_answer->setAns_content($ans[$key]);
								$this->_answer->setFeedback($feedback[$key]);								
								$this->_answer->save();							
							}
						}
					}else{
					//3 Xử lý thêm mới answer
							$this->_answer = new Default_Models_Answer();															
							$this->_answer->setAns_content($ans[0]);
							$this->_answer->setFeedback($feedback[0]);
							$last_answer_id   = $this->_answer->save();
							$ansID[0]      = $last_answer_id;
					}
					//4. Xây dựng lại 
					foreach ($ans as $key=>$ansItem){			
						$XML->setOneAnswer(Array('id'=>$ansID[0]));
					}
					$this->_question  = new Default_Models_Question();
					//$data['created_user']		= $this->getUserId();
					$data['questiontext']		= $XML->ParseXML() ;
					$data['type_question']		= $question_type ;
					$this->_question->setOptions($data);
					if(empty($data['id']))
						$this->_question->setId(null);	
										
					$this->_question->save();					
					$this->view->editsuccess = "success" ;
					$this->view->question_id = $quesID;
					if($data['isupdate']==0)
						$this->_redirect("/question");
					else
						$this->render("editsuccess"); 					 
					 	 				 
									}
		}elseif($request->isGet()){
						$data = $request->getParams();
						$id   = $data['id'];
						// ->get type cau hoi 
						$this->_question 	 = new Default_Models_Question();
						$result = $this->_question->find("id",$id);  						
                                                if(!is_null($result)){    
							// sau khi toArray thi` 1 so truong trong question da vao` mang ObjQuestion roi`
							// con` lai cac thong tin answers ta chen` vao` mang o doan sau
								
							$ObjQuestion = $this->_question->toArray() ;				
							
							$question_textXML = $this->_question->getQuestiontext();
							$this->_XML = new QuestionXML($question_textXML);
							
							$ObjQuestion['isupdate'] = 1;
							$ObjQuestion['type']  = $this->_XML->getQuestionType();
							$ObjQuestion['score'] = $this->_XML->getScoreFromXml();
							$allAnswers   = $this->_XML->getAllAnswers();
							
							// với loại câu hỏi này chỉ có 1 câu trả lời
							// lọc lấy các thông tin 
							$ans  		= Array();       // array answer content
							$ansID   	= Array();       // array answer id
							$feedback   = Array();       
							
							if($allAnswers)
							foreach($allAnswers as $ansItem){
								$ansID[]      = $ansItem['id'];
								$this->_answer       = new Default_Models_Answer();
								$this->_answer->find("id",$ansItem['id']);
								if($this->_answer->getId()){
									$ans[]		  = $this->_answer->getAns_content();   //  answer content
									$feedback[]   = $this->_answer->getFeedback();
								}										
							}   
							$ObjQuestion['answer_content']    = $ans[0];
							$ObjQuestion['feedback']    	  = $feedback[0];
							$ObjQuestion['ansID'] 		 	  = $ansID[0];
							
							$this->view->ObjQuestion = $ObjQuestion;
							$this->renderquestion($ObjQuestion['type']);							
					}
				}
		}catch(Exception $ex){
			echo $ex->getMessage();
			die();			
		}
	}	

/*----------------------------------------------------------------------------------------------------------*
 *-------------------------------------- 6 Short answer  ---------------------------------------------------*
 *----------------------------------------------------------------------------------------------------------*/	

	public function addshortanswerAction(){
		$this->view->action = "editshortanswer";
                $this->view->nameTypeQuestion = "Câu hỏi trả lời ngắn";
		$ObjQuestion  = Array();
		$ObjQuestion['type'] = 6;
		$ObjQuestion['score'] = 1;
		$ObjQuestion['level'] = 0.5;
		$ObjQuestion['classification'] = 0.2;
		$ObjQuestion['isupdate'] = 0; // insert new question
		$ObjQuestion['subject_id'] = "";
		$ObjQuestion['chapter_id'] = "";
		$ObjQuestion['question_title'] = "";
		$ObjQuestion['id'] = '';
		$ObjQuestion['content'] = "";
		$ObjQuestion['generalfeedback'] = "";
		$ObjQuestion['hidden'] = '';
		$ObjQuestion['classification'] = '';
                $ObjQuestion['created_user'] = $this->getUserId();
                
		$this->view->ObjQuestion = $ObjQuestion;
		$this->renderquestion(6);
	}
	
	public function editshortanswerAction(){
		$this->view->action = "editshortanswer";
                $this->view->nameTypeQuestion = "Câu hỏi trả lời ngắn";
	try{
		$request = $this->getRequest();
		if ($request->isPost()) {
			// get all atrribute and validate
				$data = $request->getParams();
				$data = $this->_filter($data);
				$question_type 		= $data['type'];
				$quesID 			= $data['id'];
				$question_title  	= $data['question_title'];
				$content  			= $data['content'];
				$level  			= $data['level'];
				$classification  	= isset($data['classification'])?$data['classification']:'';
				$score  			= $data['score'];
				$generalfeedback  	= $data['generalfeedback'];
				$hidden 			= $data['hidden'];
				// Array 
				$perscore  		= $data['perscore'];
				$ansID  		= $data['ansID'];
				$ans 			= $data['ans'];
				$feedback  		= $data['feedback'];
				
				// validate data nếu có lỗi thì thông báo trả lại form 
				if($data['isupdate']==1)
					$this->_validate($data,true);
				else 
					$this->_validate($data,false);
					
				if(count($this->_arrError)>0){					
					$this->view->arrError = $this->_arrError;
					
					$this->view->ObjQuestion = $data;
					
					$this->renderquestion($question_type);
										
				}else{ // Process Edit Question
					
					// Xử lý chuẩn hóa đường dẫn của hình khi upload image bằng tinyMce
					// thành dạng "../../../media/images/tinymce/giangvien/8.JPG" để dễ kiểm soát hơn <Cách này Chỉ là tình thế, ko hay> 
					$data =  $this->_getStandardImagePath($data);
					$ans 			= $data['ans'];
					// End upload image bằng tinyMce**********
					
					$XML = new QuestionXML();
					$XML->setQuestionType($question_type);
					$XML->setScoreToXml($score);
					
					$this->_question = new Default_Models_Question();
					$this->_question->find("id",$quesID);
					$allAnswers = Array();
					if($this->_question->getId()){
						$question_textXML  = $this->_question->getQuestiontext();
						$XML1 		= new QuestionXML($question_textXML);
						$allAnswers = $XML1->getAllAnswers();
					}
					//1. xu li cac answer bi xoa.
					if(count($allAnswers)>0)
					foreach($allAnswers as $key=>$ansItem){
						if(!in_array($ansItem['id'],$ansID)){ // id trong mảng ở DB không tồn tại trong mảng do người dùng post lên=> xóa 1 câu trả lời
							// step 1: remove on database
							$this->_answer = new Default_Models_Answer();
							$this->_answer->delete('id',$ansItem['id']);
						}							
					}
					$allAnswersID  = Array();
					if(count($allAnswers)>0)					
					foreach($allAnswers as $ansItem)
						$allAnswersID[]  = $ansItem['id'];
										
					//2. Xử lý các answer  bị chỉnh sửa
					foreach ($ansID as $key=>$ansIDItem){
						if(in_array($ansIDItem,$allAnswersID)){// <=> update answer							
							$this->_answer = new Default_Models_Answer();															
							$this->_answer->setId($ansIDItem);							
							$this->_answer->setAns_content($ans[$key]);
							$this->_answer->setFeedback($feedback[$key]);								
							$this->_answer->save();							
						}
					}
					//3 Xử lý thêm mới answer
					foreach ($ansID as $key=>$ansIDItem){
						if(empty($ansIDItem)){// <=> insert answer		
							$this->_answer = new Default_Models_Answer();															
							$this->_answer->setAns_content($ans[$key]);
							$this->_answer->setFeedback($feedback[$key]);
							$last_answer_id   = $this->_answer->save();
							$ansID[$key]      = $last_answer_id;
						}
					}
					//4. Xây dựng lại 
					foreach ($ans as $key=>$ansItem){					
						$XML->setOneAnswer(Array('id'=>$ansID[$key],'perscore'=>$perscore[$key],'order'=>$key));
					}
					$this->_question  = new Default_Models_Question();
					
					$data['questiontext']		= $XML->ParseXML() ;
					$data['type_question']		= $question_type ;
					$this->_question->setOptions($data);
					if(empty($data['id']))
						$this->_question->setId(null);	
										
					$this->_question->save();					
					$this->view->editsuccess = "success" ;
					$this->view->question_id = $quesID;
					if($data['isupdate']==0)
						$this->_redirect("/question");
					else
						$this->render("editsuccess"); 					 
					 	 			 
					}
		}elseif($request->isGet()) {
			$data = $request->getParams();
			$id   = $data['id'];
			// ->get type cau hoi 
			$this->_question 	 = new Default_Models_Question();
			$result = $this->_question->find("id",$id);  
                        if(!is_null($result)){
				// sau khi toArray thi` 1 so truong trong question da vao` mang ObjQuestion roi`
				// con` lai cac thong tin answers ta chen` vao` mang o doan sau
					
				$ObjQuestion = $this->_question->toArray() ;				
				
				$question_textXML = $this->_question->getQuestiontext();
				$this->_XML = new QuestionXML($question_textXML);
				
				$ObjQuestion['isupdate'] = 1; // update true
				$ObjQuestion['type']  = $this->_XML->getQuestionType();
				$ObjQuestion['score'] = $this->_XML->getScoreFromXml();
				$allAnswers   = $this->_XML->getAllAnswers();
				
				// lọc lấy các thông tin 
				$ans  		= Array();       // array answer content
				$perscore 	= Array();		// array perscore 
				$ansID   	= Array();         // array answer id
				$feedback  	= Array();       // array feedback of answer 
				
				foreach($allAnswers as $ansItem){
					$ansID[]      = $ansItem['id'];
					$perscore[]   = $ansItem['perscore'];
					$order[]	  = $ansItem['order'];
					
					$this->_answer       = new Default_Models_Answer();
					$this->_answer->find("id",$ansItem['id']);
					if($this->_answer->getId()){
						$feedback[] = $this->_answer->getFeedback();
						$ans[] 		= $this->_answer->getAns_content();
					}										
				}   
				
				$ObjQuestion['ans']    		= $ans;
				$ObjQuestion['ansID'] 		= $ansID;
				$ObjQuestion['perscore'] 	= $perscore;
				$ObjQuestion['feedback'] 	= $feedback;				
				$this->view->ObjQuestion 	= $ObjQuestion;
				
				$this->renderquestion($ObjQuestion['type']);
				
			}
		}
		}catch (Exception $ex) {
			echo $ex->getMessage(); die();
		}
			
	}
	
	
	public function renderquestion($question_type){
		switch ($question_type){
			case 1: // true,false
					$this->view->jQuery()->addJavascriptFile(BASE_URL . '/js/templete_question/question_truefalse.js');
					$this->render("truefalse");	 
				break;
			case 2: // multi choice
					$this->view->jQuery()->addJavascriptFile(BASE_URL . '/js/templete_question/question_multichoice.js');
					$this->render("multichoice");	 
				break;
			case 3: // matching
					$this->view->jQuery()->addJavascriptFile(BASE_URL . '/js/templete_question/question_matching.js');
					$this->render("matching");	 
				break;
			case 4: // completion
					$this->view->jQuery()->addJavascriptFile(BASE_URL . '/js/templete_question/question_completion.js');
					$this->render("completion");	 
				break;
			case 5: // essaytest
					$this->view->jQuery()->addJavascriptFile(BASE_URL . '/js/templete_question/question_essaytest.js');
					$this->render("essaytest");	 
				break;
			case 6: // shortanswer
					$this->view->jQuery()->addJavascriptFile(BASE_URL . '/js/templete_question/question_shortanswer.js');
					$this->render("shortanswer");	 
				break;
		}

	}	
	
	public function getAsolutePath($textQuestion){
		$content = Zend_View_Helper_FormatDate::_tinyMce_get_absolute_path_image("../../../media","http://".$_SERVER['SERVER_NAME'].BASE_URL."/media",$textQuestion);
		$content = Zend_View_Helper_FormatDate::_tinyMce_get_absolute_path_image("../../media","http://".$_SERVER['SERVER_NAME'].BASE_URL."/media" ,$content);
		$content = Zend_View_Helper_FormatDate::_tinyMce_get_absolute_path_image("../media","http://".$_SERVER['SERVER_NAME'].BASE_URL."/media" ,$content);
		return $content;
	}
	
	public function getStandardImgTinyMce($content){
		$check = strchr($content,"../../../media/images/tinymce");
		if($check==false)
			$content = Zend_View_Helper_FormatDate::_tinyMce_get_absolute_path_image("../media/images/tinymce","../../../media/images/tinymce",$content);
		return $content;
	}
	
	public function _validate($data,$update=false){
		$this->_arrError = Array();
		$question_type 	= $data['type'];
		$quesID 		= $data['id'];
		$title  		= $data['question_title'];
		$content 		= $data['content'];
		$level 			= $data['level'];
		//$classification = $data['classification'];
		$score  		= $data['score'];
		$oneormoretrue 	= isset($data['oneormoretrue'])?$data['oneormoretrue']:0;
		
		// ARRAY (Những thuộc tính bên dưới nếu cần thiết thì check ko thì thôi) 
		$generalfeedback  	= $data['generalfeedback'];
		$perscore  			= isset($data['perscore'])?$data['perscore']:0;
		$ansID  			= isset($data['ansID'])?$data['ansID']:array();
		$ans  				= isset($data['ans'])?$data['ans']:array();
		$feedback  			= isset($data['feedback'])?$data['feedback']:"";
		if($question_type == 2){// multichoice
		// CHECK PHẦN PERSCORE NẾU QUÁ HOẶC NHỎ HƠN 100 THÌ BÁO LỖI
				   if($oneormoretrue == 1){ // CHỌN NHIỀU ĐÁP ÁN ĐÚNG
							$sumscore = 0;
						     foreach ($perscore as $tmpscore){
								if ($tmpscore > 0){
									$sumscore+=$tmpscore;
								}			
							 }	
							 if($sumscore < 100 || $sumscore >100)
							 	$this->_arrError[] = "Tổng mức điểm cho câu hỏi này phải đạt 100%. <br>Hiện tổng số điểm là :".$sumscore."%";
						
					}elseif($oneormoretrue == 0 ){ // CHỈ CÓ 1 ĐÁP ÁN ĐÚNG
						$flag = false; 
						     foreach ($perscore as $tmpscore){
								if ($tmpscore == 100){
									$flag = true;
								}			
							 }	
						if(!$flag) 
							$this->_arrError[] = "Nên có 1 phương án đạt 100%.<br> Nhờ đó có thể đạt được điểm tối đa cho câu hỏi này";
					}
		}
			// CHECK MỘT SỐ TRƯỜNG NHẬP TRỐNG 
			if(empty($title))  				$this->_arrError[] = "Tiêu đề câu hỏi trống.";
			if(empty($content)) 			$this->_arrError[] = "Nội dung câu hỏi trống.";
			if(empty($level)) 				$this->_arrError[] = "Chưa chọn độ khó .";
			//if(empty($classification)) 		$this->_arrError[] = "Chưa chọn độ phân cách.";
			if(empty($score)) 				$this->_arrError[] = "Điểm câu hỏi trống.";
			if(empty($data['subject_id'])) 			$this->_arrError[] = "Chưa chọn môn học.";
			if(empty($data['chapter_id'])) 			$this->_arrError[] = "Chưa chọn chủ đề môn học.";
			
			if($question_type!=3){
				$tmp_err = "";
				if(count($ans)>0)
				foreach ($ans as $key=>$ansItem){
					if (strlen($ansItem)==0){
						$tmp_err = "Nội dung câu trả lời trống.";
						break;
					}			
				}
				if($tmp_err!="")
					$this->_arrError[] = $tmp_err;
			}
		
		if($update){// is update 
			if(empty($quesID)) 				$this->_arrError[] = "Question ID is null.";			
				
		}else{//is insert 
		}
	}
	
	private function _filter($data) {
		
		if($data['isupdate'] == 0 ){ // add new question
			$data['add_to_test'] = "0";
			$data['created_user']		= $this->getUserId();
			$data['timecreated'] = date("Y-m-d G:i:s");  
		}
		if($data['isupdate'] == 1 ){
			$data['timemodified'] = date("Y-m-d G:i:s");
		}
		
					// filter cac script
        	$filter 	= new Zend_Filter_StripTags();
        	$data['question_title'] = $filter->filter($data['question_title']); 
		
			$data['content'] 				= Zend_Filter::filterStatic($data['content'], 'StringTrim');
			$data['question_title'] 		= Zend_Filter::filterStatic($data['question_title'], 'StringTrim');
			$data['level'] 					= Zend_Filter::filterStatic($data['level'], 'StringTrim');
			//$data['classification'] 		= Zend_Filter::filterStatic($data['classification'], 'StringTrim');
			$data['generalfeedback'] 		= Zend_Filter::filterStatic($data['generalfeedback'], 'StringTrim');
			if(empty($data['hidden']))
					$data['hidden'] = 'off';	
			//$data['content'] = BASIC_String::Remove_Magic_Quote($data['content']);
			// Chuyển đường dẫn ảnh sang đường dẫn tuyệt đối 
			//$data['content'] 				= $this->getStandardImgTinyMce($data['content']);
			//$data['generalfeedback'] 		= $this->getAsolutePath($data['generalfeedback']);
			
			if(isset($data["ans"]) && count($data["ans"]) > 0){
				for($i=0;$i<count($data["ans"]);$i++){
					$data['ans'][$i] = Zend_Filter::filterStatic($data['ans'][$i], 'StringTrim');
					//$data['ans'][$i] = $this->getAsolutePath($data['ans'][$i]);
					$data['ans'][$i] = str_replace('\"', '"',$data['ans'][$i]);
					$data['ans'][$i] = str_replace("\'", '"',$data['ans'][$i]);
				}
			}
			if(isset($data["feedback"]) && count($data["feedback"]) > 0){
				for($i=0;$i<count($data["feedback"]);$i++){
					$data['feedback'][$i] = Zend_Filter::filterStatic($data['feedback'][$i], 'StringTrim');
					if (!empty($data['feedback'][$i])){
						//$data['feedback'][$i] = $this->getAsolutePath($data['feedback'][$i]);
						$data['feedback'][$i] = str_replace('\"', '"',$data['feedback'][$i]);
						$data['feedback'][$i] = str_replace("\'", '"',$data['feedback'][$i]);
					}
				}
			}
		
		$data['timecreated'] 	= Zend_View_Helper_FormatDate::convertMdyToYmd(isset($data['timecreated'])?$data['timecreated']:"");
		$data['timemodified'] 	= Zend_View_Helper_FormatDate::convertMdyToYmd(isset($data['timemodified'])?$data['timemodified']:"");
		
		$data['content'] = str_replace('\"', '"',$data['content']);
		$data['content'] = str_replace("\'", '"',$data['content']);
		$data['question_title'] = str_replace('\"', '"',$data['question_title']);
		$data['question_title'] = str_replace("\'", "'",$data['question_title']);
		
		$data['generalfeedback'] = str_replace('\"', '"',$data['generalfeedback']);
		$data['generalfeedback'] = str_replace("\'", '"',$data['generalfeedback']);
		
		
		
		return $data;
	}
	
	private function _getStandardImagePath($data) {
            return $data;
			// Chuyển đường dẫn ảnh sang đường dẫn tuyệt đối 
			$data['content'] 				= $this->getStandardImgTinyMce($data['content']);
			$data['generalfeedback'] 		= $this->getStandardImgTinyMce($data['generalfeedback']);
			
			if(count($data["ans"]) > 0){
				for($i=0;$i<count($data["ans"]);$i++){
					$data['ans'][$i] = $this->getStandardImgTinyMce($data['ans'][$i]);
				}
			}
			if(count($data["feedback"]) > 0){
				for($i=0;$i<count($data["feedback"]);$i++){
					if (!empty($data['feedback'][$i])){
						$data['feedback'][$i] = $this->getStandardImgTinyMce($data['feedback'][$i]);
					}
				}
			}
		
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