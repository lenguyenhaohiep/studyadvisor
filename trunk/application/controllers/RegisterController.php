<?php
require_once LIBRARY_PATH.		'/FormatDate.php';
require_once APPLICATION_PATH . '/models/User.php';
require_once APPLICATION_PATH . '/models/Course.php';
require_once APPLICATION_PATH . '/models/Classs.php';
require_once APPLICATION_PATH . '/models/Classhasstudent.php';
require_once 'Zend/Filter/StripTags.php';

class RegisterController extends Zend_Controller_Action
{
	private $_user;
	private $_arrError;
	public function init()
	{
		$this->_user 	 = new Default_Models_User();
		$this->view->headLink()->appendStylesheet(BASE_URL. '/css/student.css');
		$this->_helper->layout->setLayout('register');
		// Phục vụ việc giữ lại Cái menu đang active ở bên dưới - focus menu
  		$controllerName   = $this->_request->getControllerName();
  		$actionName		  = $this->_request->getActionName();
  		$param			  = $this->_request->getParams();
  		$this->view->controllerName   = $controllerName;
  		$this->view->actionName 	  = $actionName;
  		$this->view->param			  = $param;	  		
		
	}

	public function indexAction()
	{
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
				$data = $request->getParams();
				$data = $this->_filter($data); 
				$this->_validate($data);
				
				if(count($this->_arrError)>0){					
					$this->view->arrError = $this->_arrError;
					$this->view->Obj = $data;
					$this->render("index"); 
				}else{
					$data['password'] = $data['txtPassword'];
					$data['group_id'] = 2;
					$data['isblock'] = 0;
					$data['date_create'] = date("Y-m-d G:i:s");
					$data['list_course_join'] = $data['course_id'];
                                        
                                        $course_id = $data['course_id'];
					$this->_user->setOptions($data);
					$last_insert_id = $this->_user->save();
					$this->view->Obj = $data;
					$this->view->user_id = $last_insert_id;
					if($last_insert_id)
					{
					        // setup Zend_Auth adapter for a database table
			                Zend_Loader::loadClass('Zend_Auth_Adapter_DbTable');
			                $db = Zend_Registry::get('db');
			                $authAdapter = new Zend_Auth_Adapter_DbTable($db);
			                $authAdapter->setTableName('quizuit_user');
			                $authAdapter->setIdentityColumn('username');
			                $authAdapter->setCredentialColumn('password');
			                // Set the input credential values to authenticate against
			                $authAdapter->setIdentity($data['username']);
			                $authAdapter->setCredential($data['password']);
			                
			                // do the authentication 
			                $auth = Zend_Auth::getInstance();
			                $result = $auth->authenticate($authAdapter);
			
			                if ($result->isValid()) {
			                    // success : store database row to auth's storage system
			                    // (not the password though!)
			                    $data = $authAdapter->getResultRowObject(null, 'password');
			                    $auth->getStorage()->write($data);
			                    if($data->isblock==1)
			                    {
							        Zend_Auth::getInstance()->clearIdentity();
							        Zend_Session::destroy();
							        $this->view->errMsg = 'Tài khoản này đã bị khóa';
			                    }else $this->render("registersuccess");
			                }
                                        
                                        //Cho thành viên này vào lớp đầu tiên của khóa học
                                        $classModel= new Default_Models_Classs();
                                        $class = $classModel->find("course_id", $course_id)->toArray();
                                        $class_id=$class['id'];
                                        $auth = Zend_Auth::getInstance();
                                        $userhaslogin = $auth->getStorage()->read();
                                        $classHasStudent = new Default_Models_ClassHasStudent();
                                        $classHasStudent->setUser_id($userhaslogin->id);
                                        $classHasStudent->setClass_id($class_id);
                                        $classHasStudent->save();
                                        
                                        
					}
					else throw new Exception("Đăng ký không thành công");
				}
				
				
			}elseif($request->isGet()) {
				
			}
		}catch(Exception $ex){
			echo $ex->getMessage();  
			die();
		}
	}
	
	public function registersuccessAction(){
			try {
			$request = $this->getRequest();
			if ($request->isPost()) {
				// $data = $request->getParams();
			}elseif($request->isGet()) {
				
			}
		}catch(Exception $ex){
			echo $ex->getMessage();  
		}		
	}
	
	public function editAction(){
		$this->view->action = "edit" ;
		$this->view->controller = "user" ;
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
					$this->render("user");
				}else{
					$this->_user  = new Default_Models_User() ;
					$this->_user->setOptions($data);
					if(empty($data['id']))
						$this->_user->setId(null);	
										
					$this->_user->save();					
					$this->_redirect("/user");
				}
				
			}elseif($request->isGet()) {
					$data = $request->getParams();
					$id = $data['id'];			
					$result = $this->_user->find('id', $id);
					if($this->_user->getId()){
						$Obj = $this->_user->toArray() ;
						$Obj['isupdate'] = 1;
						$this->view->Obj  = $Obj;
						$this->render("user");	 
					}
				}else{
					$this->_redirect("error/error");
				}				
			
		}catch(Exception $ex){
			echo "Lỗi hoặc phương thức sai".$ex->getMessage();
			die();
		}
	}		

	public function validateusernameAction(){
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
				$data = $request->getParams();
				$username_validate = $data['username'];
				$modelsUser  = new Default_Models_User();
				$where = " `username`='".$username_validate."'";
				$result =  $modelsUser->fetchAll($where);
				if(count($result)>0)
					$dataResponse = array("success"=>false,"username"=>$username_validate,"messageShow"=>"Tên đăng nhập <span style='color: #00ADEF; font-weight: bold;'>".$username_validate."</span> đã tồn tại. Vui lòng nhập lại tên đăng nhập khác");
				else
					$dataResponse = array("success"=>true,"username"=>$username_validate,"messageShow"=>"Bạn có thể sử dụng tên tài khoản này.");
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
	
	public function validateusercodeAction(){
		try {
			$request = $this->getRequest();
			if ($request->isPost()) {
				$data = $request->getParams();
				$user_code = $data['user_code'];
				$modelsUser  = new Default_Models_User();
				$where = " `user_code`='".$user_code."'";
				$result =  $modelsUser->fetchAll($where);
				if(count($result)>0)
					$dataResponse = array("success"=>false,"user_code"=>$user_code,"messageShow"=>"Mã số <span style='color: #00ADEF; font-weight: bold;'>".$user_code."</span> đã được sử dụng.");
				else
					$dataResponse = array("success"=>true,"user_code"=>$user_code,"messageShow"=>"Bạn có thể sử dụng mã số này.");
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
	
	public function _validate($data){
		$this->_arrError = Array();
			// CHECK MỘT SỐ TRƯỜNG NHẬP TRỐNG 
			if(empty($data['username']))  				$this->_arrError[] = "Tài khoảng đăng nhập trống.";
			if(empty($data['txtPassword'])) 			$this->_arrError[] = "Mật khẩu trống.";
			if(empty($data['txtRePassword'])) 			$this->_arrError[] = "Chưa nhập lại mật khẩu.";
			if(empty($data['firstname'])) 				$this->_arrError[] = "Chưa nhập họ và tên đệm.";
			if(empty($data['lastname'])) 				$this->_arrError[] = "Chưa nhập tên .";
			if(empty($data['user_code'])) 				$this->_arrError[] = "Chưa nhập mã số sinh viên.";
			if(empty($data['department'])) 				$this->_arrError[] = "Chưa nhập bạn học khoa nào.";
			if (!Zend_Validate::is($data['email'], 'EmailAddress'))   $this->_arrError[] = "Email không hợp lệ.";
			if($data['txtPassword']!= $data['txtRePassword']) $this->_arrError[] = "Nhập lại mật khẩu chưa chính xác, hãy nhập lại.";
			if(strlen($data['txtPassword']) < 5) 			$this->_arrError[] = "Mật khẩu phải từ 5 ký tự trở lên.";
			if(strlen($data['username']) < 5) 			$this->_arrError[] = "Tên tài khoản phải từ 5 ký tự trở lên.";
			$modelUser = new Default_Models_User();
			$where = "`user_code`='".$data['user_code']."'";
			$result = $modelUser->fetchAll($where);
			if(count($result)>0)
				$this->_arrError[] = "Mã số sinh viên này đã được đăng ký. Bạn không thể dùng mã số này được";
			$where = " `username`='".$data['username']."'";
			$result =  $modelUser->fetchAll($where);
			if(count($result)>0)
				$this->_arrError[] = "Tài khoản đăng nhập đã tồn tại.";
			
	}	
	
	private function _filter($data) {
			$data['username'] 					= Zend_Filter::filterStatic($data['username'], 'StringTrim');
			$data['txtPassword'] 				= Zend_Filter::filterStatic($data['txtPassword'], 'StringTrim');
			$data['txtRePassword'] 				= Zend_Filter::filterStatic($data['txtRePassword'], 'StringTrim');
			$data['user_code'] 					= Zend_Filter::filterStatic($data['user_code'], 'StringTrim');
			$data['firstname'] 					= Zend_Filter::filterStatic($data['firstname'], 'StringTrim');
			$data['lastname'] 					= Zend_Filter::filterStatic($data['lastname'], 'StringTrim');
			$data['email'] 						= Zend_Filter::filterStatic($data['email'], 'StringTrim');
			$data['department'] 				= Zend_Filter::filterStatic($data['department'], 'StringTrim');
			
			// filter cac script
        	$filter 	= new Zend_Filter_StripTags();
        	$data['username'] = $filter->filter($data['username']); 
        	$data['user_code'] = $filter->filter($data['user_code']);
        	$data['firstname'] = $filter->filter($data['firstname']);
        	$data['lastname'] = $filter->filter($data['lastname']);
        	$data['department'] = $filter->filter($data['department']);
			
		return $data;
	}	

}



 