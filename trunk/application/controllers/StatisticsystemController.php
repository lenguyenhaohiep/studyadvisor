<?php
require_once APPLICATION_PATH . '/models/Subject.php';
require_once APPLICATION_PATH . '/models/Classs.php';
require_once APPLICATION_PATH . '/models/User.php';
require_once APPLICATION_PATH . '/models/Test.php';
require_once APPLICATION_PATH . '/models/Question.php';
require_once APPLICATION_PATH . '/models/Subject.php';
require_once APPLICATION_PATH . '/models/Chaptersubject.php';
require_once APPLICATION_PATH . '/models/Classhasstudent.php';

class StatisticsystemController extends Zend_Controller_Action
{
	private $_arrError;
	private $_statisticsystem;
	
	public function init()
	{
        $objSessionNamespace = new Zend_Session_Namespace( 'Zend_Auth' );
		$objSessionNamespace->setExpirationSeconds( 86400 );
		$this->_helper->layout->setLayout('admin');
		//$this->_helper->layout->setLayout('teacher');
		$this->view->headLink()->appendStylesheet(BASE_URL. '/css/teacher.css');
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
	

	public function statistictestAction(){
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
				//$data = $request->getParams();
			}elseif($request->isGet()) {
				$dataStatistic = array();
				$modelTest = new Default_Models_Test();
				$resultTest = $modelTest->fetchAll();
				$dataStatistic['total'] = count($resultTest);
				$modelSubject = new Default_Models_Subject();
				$resultSubject = $modelSubject->fetchAll();
				$dataOneSubject=array();
				if(count($resultSubject)>0)
				foreach($resultSubject as $key=>$resultSubjectItem){
					//$resultSubjectItem = new Default_Models_Subject();
					$where = "`subject_id`='".$resultSubjectItem->getId()."'";
					$resultTemp =  $modelTest->fetchAll($where);
					$dataOneSubject[$key]['ObjSubject']=$resultSubjectItem->toArray();
					$dataOneSubject[$key]['AmountTest'] = count($resultTemp);
					if(count($resultTemp)>0){ 											
						$modelUser = new Default_Models_User();
						$modelUser->find("id",$resultTemp[0]->getUser_create());
						$dataOneSubject[$key]['UserCreate'] = $modelUser->toArray();
					}
				}
				$dataStatistic['OneSubject'] = $dataOneSubject;
				$this->view->dataStatistic = $dataStatistic;
				//var_dump($dataStatistic);die();
			}
		}catch(Exception $ex){
			echo $ex->getMessage();  
		} 
	}

	public function statisticquestionAction(){
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
			}elseif($request->isGet()) {
				$dataStatistic = array();
				$modelQuestion = new Default_Models_Question();
				$resultQuestion = $modelQuestion->fetchAll();
				$dataStatistic['total'] = count($resultQuestion);
				$modelSubject = new Default_Models_Subject();
				$resultSubject = $modelSubject->fetchAll();
				$dataOneSubject=array();
				if(count($resultSubject)>0){
					$dataStatistic['AmoutSubject'] = count($resultSubject);
					foreach($resultSubject as $key=>$resultSubjectItem){
						$where = "`subject_id`='".$resultSubjectItem->getId()."'";
						$modelQuestion = new Default_Models_Question();
						$resultTemp =  $modelQuestion->fetchAll($where);
						$dataOneSubject[$key]['ObjSubject']=$resultSubjectItem->toArray();
						$dataOneSubject[$key]['TotalQuestionInOneSubject'] = count($resultTemp);
						// BEGIN Tìm tổng số câu hỏi trong mỗi chương của môn học này
						$modelChapter = new Default_Models_ChapterSubject();
						$resultTemp = $modelChapter->fetchAll($where);
						$dataOneSubject[$key]['TotalChapterSubject'] = count($resultTemp);
						$InfoOneChapterSubject = array();
						if(count($resultTemp)>0)
							foreach($resultTemp as $keyResultTemp=>$resultTempItem){
								// BEGIN liệt kê số lượng câu hỏi trong 1 chủ đề, các type question trong mỗi chủ đề có bao nhiêu câu 
								//$resultTempItem= new Default_Models_ChapterSubject();
								$InfoOneChapterSubject[$keyResultTemp]['NameChapterSubject'] = $resultTempItem->getName();  
								$where = "`subject_id`='".$resultSubjectItem->getId()."'AND `chapter_id`='".$resultTempItem->getId()."'";
								$resultQuestion = $modelQuestion->fetchAll($where);
								$InfoOneChapterSubject[$keyResultTemp]['AmountQuestionInOneChapterSubject'] = count($resultQuestion);
								
									$countTrueFalse 	= 0; // 1
									$countMultiplechoice = 0; // 2
									$countMatching 		= 0;//3
									$countCompletion  	= 0;//4
									$countEssay 		= 0;//5
									$countShortAnswer  	= 0;// 6
								if(count($resultQuestion)>0)
								foreach($resultQuestion as $resultQuestionItem){
									//$resultQuestionItem = new Default_Models_Question();
									if($resultQuestionItem->getType_question()==1)
										$countTrueFalse++;
									if($resultQuestionItem->getType_question()==2)
										$countMultiplechoice++;
									if($resultQuestionItem->getType_question()==3)
										$countMatching++;
									if($resultQuestionItem->getType_question()==4)
										$countCompletion++;
									if($resultQuestionItem->getType_question()==5)
										$countEssay++;
									if($resultQuestionItem->getType_question()==6)
										$countShortAnswer++;
								}
								$arrayAmoutTypeQues = array();
								$arrayAmoutTypeQues['TrueFalse']= $countTrueFalse;
								$arrayAmoutTypeQues['Multiplechoice']= $countMultiplechoice;
								$arrayAmoutTypeQues['Matching']= $countMatching;
								$arrayAmoutTypeQues['Completion']= $countCompletion;
								$arrayAmoutTypeQues['Essay']= $countEssay;
								$arrayAmoutTypeQues['ShortAnswer']= $countShortAnswer;
								$InfoOneChapterSubject[$keyResultTemp]['AmountTypeQuesInOneChapterSubject'] =  $arrayAmoutTypeQues;
							}
							// END liệt kê số lượng câu hỏi trong 1 chủ đề, các type question trong mỗi chủ đề có bao nhiêu câu
							$dataOneSubject[$key]['InfoAllChapterSubject'] = $InfoOneChapterSubject;
							// END Tìm tổng số câu hỏi trong mỗi chương của môn học này
					}
							// BEGIN Tìm số câu hỏi do mỗi giảng viên tạo
							$sql = " SELECT DISTINCT created_user FROM `quizuit_questions`";
							$resultSqlCustom = $modelQuestion->customSql($sql);
							$arrQuestionTeacher = array();
							if(count($resultSqlCustom)>0)
							foreach($resultSqlCustom as $keyResultSql=>$resultSqlCustomItem){
								$modelUser = new Default_Models_User();
								$modelUser->find("id",trim($resultSqlCustomItem['created_user']));
								if($modelUser->getId()){
									$arrQuestionTeacher[$keyResultSql]['ObjTeacher'] = $modelUser->toArray();
									$where = "`created_user`='".$resultSqlCustomItem['created_user']."'";
									$resultQuestionTeacher = $modelQuestion->fetchAll($where);
									$arrQuestionTeacher[$keyResultSql]['TotalQuestionTeacherCreate'] = count($resultQuestionTeacher);
									$where = "`user_create`='".$resultSqlCustomItem['created_user']."'";
									$modelTestTeacher = new Default_Models_Test();
									$resultTestTeacher = $modelTestTeacher->fetchAll($where);
									$arrQuestionTeacher[$keyResultSql]['TotalTestTeacherCreate'] = count($resultTestTeacher);
								}
							}
							//$dataOneSubject[$key]['InfoQuestionTeacher'] = $arrQuestionTeacher;
							// END Tìm số câu hỏi do mỗi giảng viên tạo
					$dataStatistic['InfoQuestionTeacher'] = $arrQuestionTeacher;
					$dataStatistic['Subject'] = $dataOneSubject;
				}
				
				$this->view->dataStatistic = $dataStatistic;
			}
		}catch(Exception $ex){
			echo $ex->getMessage();  
		} 
	}
	
	public function statisticclassAction(){
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
			}elseif($request->isGet()) {
				$dataStatistic = array();
				$modelClass = new Default_Models_Classs();
				$resultClass = $modelClass->fetchAll();
				$dataStatistic['total'] = count($resultClass);
				$modelClassHasStudent = new Default_Models_ClassHasStudent();
				$sql="SELECT count(distinct user_id) as amout_user FROM `quizuit_class_has_student`";
				//SELECT count(*) FROM `quizuit_class_has_student` where class_id=8
				$resulCustomSql = $modelClassHasStudent->customSql($sql);
				$dataStatistic['totalStudentJoin'] = $resulCustomSql[0]['amout_user'];
				$where = "`hidden`='on'";
				$resultClassOn = $modelClass->fetchAll($where);
				$dataStatistic['OnClass']['totalClassOn'] = count($resultClassOn);
				$where_class_id="";
				$ObjClassOn=array();
				if(count($resultClassOn)>0)
				foreach($resultClassOn as $key=>$resultClassOnItem){
					//$resultClassOffItem = new Default_Models_Classs();
					$ObjClassOn[$key]['ObjClassOn'] = $resultClassOnItem->toArray();	
					$sql="SELECT count(distinct user_id) as amout_user FROM `quizuit_class_has_student` where `class_id`='".$resultClassOnItem->getId()."'";
					$resulCustomSql = $modelClassHasStudent->customSql($sql);				
					$ObjClassOn[$key]['AmountStudent'] = $resulCustomSql[0]['amout_user'];
					if($key==0)
						$where_class_id.=" `class_id`='".$resultClassOnItem->getId()."'";
					else					
						$where_class_id.= " or `class_id`='".$resultClassOnItem->getId()."'";
					//SELECT count(*) FROM `quizuit_class_has_student` where class_id=8
				}
				$dataStatistic['OnClass']['BigObjClassOn'] = $ObjClassOn;
				if(!empty($where_class_id)){
					$sql="SELECT count(distinct user_id) as amout_user FROM `quizuit_class_has_student` where ".$where_class_id;
					$resulCustomSql = $modelClassHasStudent->customSql($sql);				
					$dataStatistic['OnClass']['amountStudentInClassOn'] = $resulCustomSql[0]['amout_user'];
				}
				$where = "`hidden`='off'";
				$resultClassOff = $modelClass->fetchAll($where);
				$dataStatistic['OffClass']['totalClassOff'] = count($resultClassOff);
				$where_class_id="";
				$ObjClassOff=array();
				if(count($resultClassOff)>0)
				foreach($resultClassOff as $key=>$resultClassOffItem){
					//$resultClassOffItem = new Default_Models_Classs();
					$ObjClassOff[$key]['ObjClassOff'] = $resultClassOffItem->toArray();	
					$sql="SELECT count(distinct user_id) as amout_user FROM `quizuit_class_has_student` where `class_id`='".$resultClassOffItem->getId()."'";
					$resulCustomSql = $modelClassHasStudent->customSql($sql);				
					$ObjClassOff[$key]['AmountStudent'] = $resulCustomSql[0]['amout_user'];
					if($key==0)
						$where_class_id.=" `class_id`='".$resultClassOffItem->getId()."'";
					else					
						$where_class_id.= " or `class_id`='".$resultClassOffItem->getId()."'";
					//SELECT count(*) FROM `quizuit_class_has_student` where class_id=8
				}
				$dataStatistic['OffClass']['BigObjClassOff'] = $ObjClassOff;
				if(!empty($where_class_id)){
					$sql="SELECT count(distinct user_id) as amout_user FROM `quizuit_class_has_student` where ".$where_class_id;
					$resulCustomSql = $modelClassHasStudent->customSql($sql);				
					$dataStatistic['OffClass']['amountStudentInClassOff'] = $resulCustomSql[0]['amout_user'];
				}
				$this->view->dataStatistic = $dataStatistic;
			}
		}catch(Exception $ex){
			echo $ex->getMessage();  
			die();
		} 
	}
	
	public function statisticuserAction(){
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
				//$data = $request->getParams();
			}elseif($request->isGet()) {
				$dataStatistic = array();
				$modelGroupUser = new Default_Models_GroupUser();
				$resultGroupUser = $modelGroupUser->fetchAll();
				$modelUser = new Default_Models_User();
				$resulUser = $modelUser->fetchAll();
				$dataStatistic['totalUser'] = count($resulUser);
				$dataStatistic['total'] = count($resultGroupUser);
				$objGroupUser=array();
				if(count($resultGroupUser)>0)
				foreach($resultGroupUser as $key=>$resultGroupUserItem){
					$objGroupUser[$key]['ObjOneGroupUser'] = $resultGroupUserItem->toArray();
					$modelUser = new Default_Models_User();
					$resultUser = $modelUser->fetchAll(" `group_id`='".$resultGroupUserItem->getId()."'");
					$objGroupUser[$key]['CountOneGroupUser'] = count($resultUser);
				}	
				
				$AccBlock = $modelUser->fetchAll("`isblock`=1");
				$dataStatistic['UserBlock'] = count($AccBlock);
				$dataStatistic['GroupUser'] = $objGroupUser;
				$this->view->dataStatistic = $dataStatistic;
				//var_dump($dataStatistic);die();
			}
		}catch(Exception $ex){
			echo $ex->getMessage();  
		} 
	}
	
	public function exportwordstatisticquestionAction(){
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
			}elseif($request->isGet()) {
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
			}elseif($request->isGet()) {
				$this->_helper->layout->disableLayout();
				$dataStatistic = array();
				$modelQuestion = new Default_Models_Question();
				$resultQuestion = $modelQuestion->fetchAll();
				$dataStatistic['total'] = count($resultQuestion);
				$modelSubject = new Default_Models_Subject();
				$resultSubject = $modelSubject->fetchAll();
				$dataOneSubject=array();
				if(count($resultSubject)>0){
					$dataStatistic['AmoutSubject'] = count($resultSubject);
					foreach($resultSubject as $key=>$resultSubjectItem){
						$where = "`subject_id`='".$resultSubjectItem->getId()."'";
						$modelQuestion = new Default_Models_Question();
						$resultTemp =  $modelQuestion->fetchAll($where);
						$dataOneSubject[$key]['ObjSubject']=$resultSubjectItem->toArray();
						$dataOneSubject[$key]['TotalQuestionInOneSubject'] = count($resultTemp);
						// BEGIN Tìm tổng số câu hỏi trong mỗi chương của môn học này
						$modelChapter = new Default_Models_ChapterSubject();
						$resultTemp = $modelChapter->fetchAll($where);
						$dataOneSubject[$key]['TotalChapterSubject'] = count($resultTemp);
						$InfoOneChapterSubject = array();
						if(count($resultTemp)>0)
							foreach($resultTemp as $keyResultTemp=>$resultTempItem){
								// BEGIN liệt kê số lượng câu hỏi trong 1 chủ đề, các type question trong mỗi chủ đề có bao nhiêu câu 
								//$resultTempItem= new Default_Models_ChapterSubject();
								$InfoOneChapterSubject[$keyResultTemp]['NameChapterSubject'] = $resultTempItem->getName();  
								$where = "`subject_id`='".$resultSubjectItem->getId()."'AND `chapter_id`='".$resultTempItem->getId()."'";
								$resultQuestion = $modelQuestion->fetchAll($where);
								$InfoOneChapterSubject[$keyResultTemp]['AmountQuestionInOneChapterSubject'] = count($resultQuestion);
								
									$countTrueFalse 	= 0; // 1
									$countMultiplechoice = 0; // 2
									$countMatching 		= 0;//3
									$countCompletion  	= 0;//4
									$countEssay 		= 0;//5
									$countShortAnswer  	= 0;// 6
								if(count($resultQuestion)>0)
								foreach($resultQuestion as $resultQuestionItem){
									//$resultQuestionItem = new Default_Models_Question();
									if($resultQuestionItem->getType_question()==1)
										$countTrueFalse++;
									if($resultQuestionItem->getType_question()==2)
										$countMultiplechoice++;
									if($resultQuestionItem->getType_question()==3)
										$countMatching++;
									if($resultQuestionItem->getType_question()==4)
										$countCompletion++;
									if($resultQuestionItem->getType_question()==5)
										$countEssay++;
									if($resultQuestionItem->getType_question()==6)
										$countShortAnswer++;
								}
								$arrayAmoutTypeQues = array();
								$arrayAmoutTypeQues['TrueFalse']= $countTrueFalse;
								$arrayAmoutTypeQues['Multiplechoice']= $countMultiplechoice;
								$arrayAmoutTypeQues['Matching']= $countMatching;
								$arrayAmoutTypeQues['Completion']= $countCompletion;
								$arrayAmoutTypeQues['Essay']= $countEssay;
								$arrayAmoutTypeQues['ShortAnswer']= $countShortAnswer;
								$InfoOneChapterSubject[$keyResultTemp]['AmountTypeQuesInOneChapterSubject'] =  $arrayAmoutTypeQues;
							}
							// END liệt kê số lượng câu hỏi trong 1 chủ đề, các type question trong mỗi chủ đề có bao nhiêu câu
							$dataOneSubject[$key]['InfoAllChapterSubject'] = $InfoOneChapterSubject;
							// END Tìm tổng số câu hỏi trong mỗi chương của môn học này
					}
							// BEGIN Tìm số câu hỏi do mỗi giảng viên tạo
							$sql = " SELECT DISTINCT created_user FROM `quizuit_questions`";
							$resultSqlCustom = $modelQuestion->customSql($sql);
							$arrQuestionTeacher = array();
							if(count($resultSqlCustom)>0)
							foreach($resultSqlCustom as $keyResultSql=>$resultSqlCustomItem){
								$modelUser = new Default_Models_User();
								$modelUser->find("id",trim($resultSqlCustomItem['created_user']));
								if($modelUser->getId()){
									$arrQuestionTeacher[$keyResultSql]['ObjTeacher'] = $modelUser->toArray();
									$where = "`created_user`='".$resultSqlCustomItem['created_user']."'";
									$resultQuestionTeacher = $modelQuestion->fetchAll($where);
									$arrQuestionTeacher[$keyResultSql]['TotalQuestionTeacherCreate'] = count($resultQuestionTeacher);
									$where = "`user_create`='".$resultSqlCustomItem['created_user']."'";
									$modelTestTeacher = new Default_Models_Test();
									$resultTestTeacher = $modelTestTeacher->fetchAll($where);
									$arrQuestionTeacher[$keyResultSql]['TotalTestTeacherCreate'] = count($resultTestTeacher);
								}
							}
							//$dataOneSubject[$key]['InfoQuestionTeacher'] = $arrQuestionTeacher;
							// END Tìm số câu hỏi do mỗi giảng viên tạo
					$dataStatistic['InfoQuestionTeacher'] = $arrQuestionTeacher;
					$dataStatistic['Subject'] = $dataOneSubject;
				}
				
				$this->view->dataStatistic = $dataStatistic;
			}
		}catch(Exception $ex){
			echo $ex->getMessage();  
		} 				
			}
		}catch(Exception $ex){
			echo $ex->getMessage();  
		}
	}
	
	public function exportexcelstatisticquestionAction(){
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
			}elseif($request->isGet()) {
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
			}elseif($request->isGet()) {
				$this->_helper->layout->disableLayout();
				$dataStatistic = array();
				$modelQuestion = new Default_Models_Question();
				$resultQuestion = $modelQuestion->fetchAll();
				$dataStatistic['total'] = count($resultQuestion);
				$modelSubject = new Default_Models_Subject();
				$resultSubject = $modelSubject->fetchAll();
				$dataOneSubject=array();
				if(count($resultSubject)>0){
					$dataStatistic['AmoutSubject'] = count($resultSubject);
					foreach($resultSubject as $key=>$resultSubjectItem){
						$where = "`subject_id`='".$resultSubjectItem->getId()."'";
						$modelQuestion = new Default_Models_Question();
						$resultTemp =  $modelQuestion->fetchAll($where);
						$dataOneSubject[$key]['ObjSubject']=$resultSubjectItem->toArray();
						$dataOneSubject[$key]['TotalQuestionInOneSubject'] = count($resultTemp);
						// BEGIN Tìm tổng số câu hỏi trong mỗi chương của môn học này
						$modelChapter = new Default_Models_ChapterSubject();
						$resultTemp = $modelChapter->fetchAll($where);
						$dataOneSubject[$key]['TotalChapterSubject'] = count($resultTemp);
						$InfoOneChapterSubject = array();
						if(count($resultTemp)>0)
							foreach($resultTemp as $keyResultTemp=>$resultTempItem){
								// BEGIN liệt kê số lượng câu hỏi trong 1 chủ đề, các type question trong mỗi chủ đề có bao nhiêu câu 
								//$resultTempItem= new Default_Models_ChapterSubject();
								$InfoOneChapterSubject[$keyResultTemp]['NameChapterSubject'] = $resultTempItem->getName();  
								$where = "`subject_id`='".$resultSubjectItem->getId()."'AND `chapter_id`='".$resultTempItem->getId()."'";
								$resultQuestion = $modelQuestion->fetchAll($where);
								$InfoOneChapterSubject[$keyResultTemp]['AmountQuestionInOneChapterSubject'] = count($resultQuestion);
								
									$countTrueFalse 	= 0; // 1
									$countMultiplechoice = 0; // 2
									$countMatching 		= 0;//3
									$countCompletion  	= 0;//4
									$countEssay 		= 0;//5
									$countShortAnswer  	= 0;// 6
								if(count($resultQuestion)>0)
								foreach($resultQuestion as $resultQuestionItem){
									//$resultQuestionItem = new Default_Models_Question();
									if($resultQuestionItem->getType_question()==1)
										$countTrueFalse++;
									if($resultQuestionItem->getType_question()==2)
										$countMultiplechoice++;
									if($resultQuestionItem->getType_question()==3)
										$countMatching++;
									if($resultQuestionItem->getType_question()==4)
										$countCompletion++;
									if($resultQuestionItem->getType_question()==5)
										$countEssay++;
									if($resultQuestionItem->getType_question()==6)
										$countShortAnswer++;
								}
								$arrayAmoutTypeQues = array();
								$arrayAmoutTypeQues['TrueFalse']= $countTrueFalse;
								$arrayAmoutTypeQues['Multiplechoice']= $countMultiplechoice;
								$arrayAmoutTypeQues['Matching']= $countMatching;
								$arrayAmoutTypeQues['Completion']= $countCompletion;
								$arrayAmoutTypeQues['Essay']= $countEssay;
								$arrayAmoutTypeQues['ShortAnswer']= $countShortAnswer;
								$InfoOneChapterSubject[$keyResultTemp]['AmountTypeQuesInOneChapterSubject'] =  $arrayAmoutTypeQues;
							}
							// END liệt kê số lượng câu hỏi trong 1 chủ đề, các type question trong mỗi chủ đề có bao nhiêu câu
							$dataOneSubject[$key]['InfoAllChapterSubject'] = $InfoOneChapterSubject;
							// END Tìm tổng số câu hỏi trong mỗi chương của môn học này
					}
							// BEGIN Tìm số câu hỏi do mỗi giảng viên tạo
							$sql = " SELECT DISTINCT created_user FROM `quizuit_questions`";
							$resultSqlCustom = $modelQuestion->customSql($sql);
							$arrQuestionTeacher = array();
							if(count($resultSqlCustom)>0)
							foreach($resultSqlCustom as $keyResultSql=>$resultSqlCustomItem){
								$modelUser = new Default_Models_User();
								$modelUser->find("id",trim($resultSqlCustomItem['created_user']));
								if($modelUser->getId()){
									$arrQuestionTeacher[$keyResultSql]['ObjTeacher'] = $modelUser->toArray();
									$where = "`created_user`='".$resultSqlCustomItem['created_user']."'";
									$resultQuestionTeacher = $modelQuestion->fetchAll($where);
									$arrQuestionTeacher[$keyResultSql]['TotalQuestionTeacherCreate'] = count($resultQuestionTeacher);
									$where = "`user_create`='".$resultSqlCustomItem['created_user']."'";
									$modelTestTeacher = new Default_Models_Test();
									$resultTestTeacher = $modelTestTeacher->fetchAll($where);
									$arrQuestionTeacher[$keyResultSql]['TotalTestTeacherCreate'] = count($resultTestTeacher);
								}
							}
							//$dataOneSubject[$key]['InfoQuestionTeacher'] = $arrQuestionTeacher;
							// END Tìm số câu hỏi do mỗi giảng viên tạo
					$dataStatistic['InfoQuestionTeacher'] = $arrQuestionTeacher;
					$dataStatistic['Subject'] = $dataOneSubject;
				}
				
				$this->view->dataStatistic = $dataStatistic;
			}
		}catch(Exception $ex){
			echo $ex->getMessage();  
		} 				
			}
		}catch(Exception $ex){
			echo $ex->getMessage();  
		}
	}
	
	public function exportwordstatisticclassAction(){
	try {
			$request = $this->getRequest();
			if ($request->isPost()) {
			}elseif($request->isGet()) {
				$this->_helper->layout->disableLayout();
				$dataStatistic = array();
				$modelClass = new Default_Models_Classs();
				$resultClass = $modelClass->fetchAll();
				$dataStatistic['total'] = count($resultClass);
				$modelClassHasStudent = new Default_Models_ClassHasStudent();
				$sql="SELECT count(distinct user_id) as amout_user FROM `quizuit_class_has_student`";
				//SELECT count(*) FROM `quizuit_class_has_student` where class_id=8
				$resulCustomSql = $modelClassHasStudent->customSql($sql);
				$dataStatistic['totalStudentJoin'] = $resulCustomSql[0]['amout_user'];
				$where = "`hidden`='on'";
				$resultClassOn = $modelClass->fetchAll($where);
				$dataStatistic['OnClass']['totalClassOn'] = count($resultClassOn);
				$where_class_id="";
				$ObjClassOn=array();
				if(count($resultClassOn)>0)
				foreach($resultClassOn as $key=>$resultClassOnItem){
					//$resultClassOffItem = new Default_Models_Classs();
					$ObjClassOn[$key]['ObjClassOn'] = $resultClassOnItem->toArray();	
					$sql="SELECT count(distinct user_id) as amout_user FROM `quizuit_class_has_student` where `class_id`='".$resultClassOnItem->getId()."'";
					$resulCustomSql = $modelClassHasStudent->customSql($sql);				
					$ObjClassOn[$key]['AmountStudent'] = $resulCustomSql[0]['amout_user'];
					if($key==0)
						$where_class_id.=" `class_id`='".$resultClassOnItem->getId()."'";
					else					
						$where_class_id.= " or `class_id`='".$resultClassOnItem->getId()."'";
					//SELECT count(*) FROM `quizuit_class_has_student` where class_id=8
				}
				$dataStatistic['OnClass']['BigObjClassOn'] = $ObjClassOn;
				
				if(!empty($where_class_id)){
					$sql="SELECT count(distinct user_id) as amout_user FROM `quizuit_class_has_student` where ".$where_class_id;
					$resulCustomSql = $modelClassHasStudent->customSql($sql);				
					$dataStatistic['OnClass']['amountStudentInClassOn'] = $resulCustomSql[0]['amout_user'];
				}
				$where = "`hidden`='off'";
				$resultClassOff = $modelClass->fetchAll($where);
				$dataStatistic['OffClass']['totalClassOff'] = count($resultClassOff);
				$where_class_id="";
				$ObjClassOff=array();
				if(count($resultClassOff)>0)
				foreach($resultClassOff as $key=>$resultClassOffItem){
					//$resultClassOffItem = new Default_Models_Classs();
					$ObjClassOff[$key]['ObjClassOff'] = $resultClassOffItem->toArray();	
					$sql="SELECT count(distinct user_id) as amout_user FROM `quizuit_class_has_student` where `class_id`='".$resultClassOffItem->getId()."'";
					$resulCustomSql = $modelClassHasStudent->customSql($sql);				
					$ObjClassOff[$key]['AmountStudent'] = $resulCustomSql[0]['amout_user'];
					if($key==0)
						$where_class_id.=" `class_id`='".$resultClassOffItem->getId()."'";
					else					
						$where_class_id.= " or `class_id`='".$resultClassOffItem->getId()."'";
					//SELECT count(*) FROM `quizuit_class_has_student` where class_id=8
				}
				$dataStatistic['OffClass']['BigObjClassOff'] = $ObjClassOff;
				if(!empty($where_class_id)){
					$sql="SELECT count(distinct user_id) as amout_user FROM `quizuit_class_has_student` where ".$where_class_id;
					$resulCustomSql = $modelClassHasStudent->customSql($sql);				
					$dataStatistic['OffClass']['amountStudentInClassOff'] = $resulCustomSql[0]['amout_user'];
				}
				$this->view->dataStatistic = $dataStatistic;
			}
		}catch(Exception $ex){
			echo $ex->getMessage();  
		} 		
		
	}
	
	public function exportexcelstatisticclassAction(){
	try {
			$request = $this->getRequest();
			if ($request->isPost()) {
			}elseif($request->isGet()) {
				$this->_helper->layout->disableLayout();
				$dataStatistic = array();
				$modelClass = new Default_Models_Classs();
				$resultClass = $modelClass->fetchAll();
				$dataStatistic['total'] = count($resultClass);
				$modelClassHasStudent = new Default_Models_ClassHasStudent();
				$sql="SELECT count(distinct user_id) as amout_user FROM `quizuit_class_has_student`";
				//SELECT count(*) FROM `quizuit_class_has_student` where class_id=8
				$resulCustomSql = $modelClassHasStudent->customSql($sql);
				$dataStatistic['totalStudentJoin'] = $resulCustomSql[0]['amout_user'];
				$where = "`hidden`='on'";
				$resultClassOn = $modelClass->fetchAll($where);
				$dataStatistic['OnClass']['totalClassOn'] = count($resultClassOn);
				$where_class_id="";
				$ObjClassOn=array();
				if(count($resultClassOn)>0)
				foreach($resultClassOn as $key=>$resultClassOnItem){
					//$resultClassOffItem = new Default_Models_Classs();
					$ObjClassOn[$key]['ObjClassOn'] = $resultClassOnItem->toArray();	
					$sql="SELECT count(distinct user_id) as amout_user FROM `quizuit_class_has_student` where `class_id`='".$resultClassOnItem->getId()."'";
					$resulCustomSql = $modelClassHasStudent->customSql($sql);				
					$ObjClassOn[$key]['AmountStudent'] = $resulCustomSql[0]['amout_user'];
					if($key==0)
						$where_class_id.=" `class_id`='".$resultClassOnItem->getId()."'";
					else					
						$where_class_id.= " or `class_id`='".$resultClassOnItem->getId()."'";
					//SELECT count(*) FROM `quizuit_class_has_student` where class_id=8
				}
				$dataStatistic['OnClass']['BigObjClassOn'] = $ObjClassOn;
				if(!empty($where_class_id)){
					$sql="SELECT count(distinct user_id) as amout_user FROM `quizuit_class_has_student` where ".$where_class_id;
					$resulCustomSql = $modelClassHasStudent->customSql($sql);				
					$dataStatistic['OnClass']['amountStudentInClassOn'] = $resulCustomSql[0]['amout_user'];
				}
				$where = "`hidden`='off'";
				$resultClassOff = $modelClass->fetchAll($where);
				$dataStatistic['OffClass']['totalClassOff'] = count($resultClassOff);
				$where_class_id="";
				$ObjClassOff=array();
				if(count($resultClassOff)>0)
				foreach($resultClassOff as $key=>$resultClassOffItem){
					//$resultClassOffItem = new Default_Models_Classs();
					$ObjClassOff[$key]['ObjClassOff'] = $resultClassOffItem->toArray();	
					$sql="SELECT count(distinct user_id) as amout_user FROM `quizuit_class_has_student` where `class_id`='".$resultClassOffItem->getId()."'";
					$resulCustomSql = $modelClassHasStudent->customSql($sql);				
					$ObjClassOff[$key]['AmountStudent'] = $resulCustomSql[0]['amout_user'];
					if($key==0)
						$where_class_id.=" `class_id`='".$resultClassOffItem->getId()."'";
					else					
						$where_class_id.= " or `class_id`='".$resultClassOffItem->getId()."'";
					//SELECT count(*) FROM `quizuit_class_has_student` where class_id=8
				}
				$dataStatistic['OffClass']['BigObjClassOff'] = $ObjClassOff;
				if(!empty($where_class_id)){
					$sql="SELECT count(distinct user_id) as amout_user FROM `quizuit_class_has_student` where ".$where_class_id;
					$resulCustomSql = $modelClassHasStudent->customSql($sql);				
					$dataStatistic['OffClass']['amountStudentInClassOff'] = $resulCustomSql[0]['amout_user'];
				}
				$this->view->dataStatistic = $dataStatistic;
			}
		}catch(Exception $ex){
			echo $ex->getMessage();  
		} 		
		
	}
	
	public function exportwordstatisticuserAction(){
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
				//$data = $request->getParams();
			}elseif($request->isGet()) {
				$this->_helper->layout->disableLayout();
				$dataStatistic = array();
				$modelGroupUser = new Default_Models_GroupUser();
				$resultGroupUser = $modelGroupUser->fetchAll();
				$modelUser = new Default_Models_User();
				$resulUser = $modelUser->fetchAll();
				$dataStatistic['totalUser'] = count($resulUser);
				$dataStatistic['total'] = count($resultGroupUser);
				$objGroupUser=array();
				if(count($resultGroupUser)>0)
				foreach($resultGroupUser as $key=>$resultGroupUserItem){
					$objGroupUser[$key]['ObjOneGroupUser'] = $resultGroupUserItem->toArray();
					$modelUser = new Default_Models_User();
					$resultUser = $modelUser->fetchAll(" `group_id`='".$resultGroupUserItem->getId()."'");
					$objGroupUser[$key]['CountOneGroupUser'] = count($resultUser);
				}	
				
				$AccBlock = $modelUser->fetchAll("`isblock`=1");
				$dataStatistic['UserBlock'] = count($AccBlock);
				$dataStatistic['GroupUser'] = $objGroupUser;
				$this->view->dataStatistic = $dataStatistic;
				//var_dump($dataStatistic);die();
			}
		}catch(Exception $ex){
			echo $ex->getMessage();  
		} 
	}	

	public function exportwordstudentclassAction(){
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
			}elseif($request->isGet()) {
				$this->_helper->layout->disableLayout();
				$dataStatistic = array();
				$data = $request->getParams();
				if(empty($data['class_id']))
					throw new Exception("Không tồn tại lớp học.");
				$modelClass = new Default_Models_Classs();
				$modelClass->find("id",$data['class_id']);
				$dataStatistic['ObjClass'] = $modelClass->toArray();
				$objStudent=array(); 	
				$modelClassHasStudent = new Default_Models_ClassHasStudent();
				$result = $modelClassHasStudent->fetchAll("`class_id`='".$data['class_id']."'");
				
				if(count($result)>0)
				foreach($result as $key=>$resultItem){
					$modelUser = new Default_Models_User();
					$modelUser->find("id",$resultItem->getUser_id());
					$objStudent[$key] = $modelUser->toArray();
				}
				$dataStatistic['arrObjStu'] = $objStudent;
				$this->view->dataStatistic = $dataStatistic;
			}
		}catch(Exception $ex){
			echo $ex->getMessage();  
			die();
		}
		
	}
	

	public function viewpopupstudentAction(){
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
			}elseif($request->isGet()) {
				$dataStatistic = array();
				$data = $request->getParams();
				if(empty($data['groupid']))
					throw new Exception("Không tồn tại nhóm người dùng.");
				$arrObjUser = array();
				$modelUser = new Default_Models_User();
				$resultUser = $modelUser->fetchAll("`group_id`='".$data['groupid']."'");
				if(count($resultUser)>0)
				foreach($resultUser as $key=>$resultUserItem)
					$arrObjUser[$key] = $resultUserItem->toArray();
				$modelGroupUser = new Default_Models_GroupUser();
				$modelGroupUser->find("id",$data['groupid']);
				$dataStatistic['ObjGroupUser'] = $modelGroupUser->toArray();
				$dataStatistic['ObjUser'] = $arrObjUser;
				$this->view->dataStatistic = $dataStatistic;
			}
		}catch(Exception $ex){
			echo $ex->getMessage();  
			die();
		}		 
	}
	
	public function exportwordgroupuserAction(){
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
			}elseif($request->isGet()) {
				$this->_helper->layout->disableLayout();
				$dataStatistic = array();
				$data = $request->getParams();
				if(empty($data['groupid']))
					throw new Exception("Không tồn tại nhóm người dùng.");
				$arrObjUser = array();
				$modelUser = new Default_Models_User();
				$resultUser = $modelUser->fetchAll("`group_id`='".$data['groupid']."'");
				if(count($resultUser)>0)
				foreach($resultUser as $key=>$resultUserItem)
					$arrObjUser[$key] = $resultUserItem->toArray();
				$modelGroupUser = new Default_Models_GroupUser();
				$modelGroupUser->find("id",$data['groupid']);
				$dataStatistic['ObjGroupUser'] = $modelGroupUser->toArray();
				$dataStatistic['ObjUser'] = $arrObjUser;
				$this->view->dataStatistic = $dataStatistic;
			}
		}catch(Exception $ex){
			echo $ex->getMessage();  
			die();
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
			}
		}catch(Exception $ex){
			echo $ex->getMessage();  
			die();
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
		
		if ($data['action'] == 'add' || $data['action'] == 'edit') {
			$data['full_name'] 				= Zend_Filter::filterStatic($data['full_name'], 'StringTrim');
			$data['short_name'] 			= Zend_Filter::filterStatic($data['short_name'], 'StringTrim');
			$data['summary'] 				= Zend_Filter::filterStatic($data['summary'], 'StringTrim');
			
			//$data['full_name'] = BASIC_String::Remove_Magic_Quote($data['full_name']);
		}
		return $data;
	}	
	
	
	
	


	
} 