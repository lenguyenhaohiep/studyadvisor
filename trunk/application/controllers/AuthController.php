<?php
require_once APPLICATION_PATH . '/models/User.php';
require_once 'Zend/Validate.php';
class AuthController extends Zend_Controller_Action 
{
    function init()
    {
        $this->initView();
        $this->view->controller = "auth";
    }
        
    function indexAction()
    {
        $this->_redirect('/');
    }
	
    function preDispatch()
    {
        $auth = Zend_Auth::getInstance();
        
        if (!$auth->hasIdentity()) {
        	$this->view->haslogin = false;
        }else
        {
        	$this->view->haslogin = true;
        	$this->view->userhaslogin = $auth->getStorage()->read();
        }
    }	
    
    function loginAction()
    { 	
    	
       $this->view->message = '';
        if ($this->_request->isPost()) {
            // collect the data from the user
            Zend_Loader::loadClass('Zend_Filter_StripTags');
            $filter = new Zend_Filter_StripTags();
            $username = $filter->filter($this->_request->getPost('txtUserName'));
            $password = $filter->filter($this->_request->getPost('txtPassword'));

            if (empty($username)) {
                $this->view->errMsg = 'Chưa có thông tin về tài khoản';
            } else {
                // setup Zend_Auth adapter for a database table
                Zend_Loader::loadClass('Zend_Auth_Adapter_DbTable');
                $db = Zend_Registry::get('db');
                $authAdapter = new Zend_Auth_Adapter_DbTable($db);
                $authAdapter->setTableName('quizuit_user');
                $authAdapter->setIdentityColumn('username');
                $authAdapter->setCredentialColumn('password');
                // Set the input credential values to authenticate against
                $authAdapter->setIdentity($username);
                $authAdapter->setCredential($password);
                
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
                    }else
                    {
                    	  $objSessionNamespace = new Zend_Session_Namespace( 'Zend_Auth' );
  						  $objSessionNamespace->setExpirationSeconds( 86400 );
                    	// Chuyển trang tương ứng với nhóm quyền
                    	
						// Begin xử lý image maneger tinyMce
						// 1: quantri
						// 3: giangvien
						// 6: giaovu
						if( $data->group_id==3 || $data->group_id==6 || $data->group_id == 1){
							$imagePath = 'media/images/tinymce/'.$data->username;
							// Tạo thư mục up ảnh cho giảng viên
							$this->mkdirImageForUserTeacher($data->username);
							unset($_SESSION['folder_user_upload_image']);
  							session_start();
							$_SESSION['folder_user_upload_image'] = $imagePath ;
						}
						
  						// End xử lý image maneger tinyMce  
						
						if($data->group_id == 1) 
                    		$this->_redirect('/index');
						if($data->group_id == 2) 
                    		$this->_redirect('/pagestudent');
                    		/*
						if($data->group_id == 3) 
                    		$this->_redirect('/pagesteacher');
						if($data->group_id == 4) 
                    		$this->_redirect('/pagetestuser');
						*/
						if($data->group_id == 5) 
                    		$this->_redirect('/index');
                    		
                    	$this->_redirect('/index');
                    		
                    }
                } else {
                    // failure: clear database row from sessionz
					// get the type of authentication failure 
                	switch ($result->getCode())
					{	
						//"Identity(login_name) not found" case				
					    case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
					         $this->view->errMsg = "Tài khoản không tồn tại";
					        break;
						//"Identity and Credential(passwd) do not match" case
					    case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
					         $this->view->errMsg = 'Mật khẩu và tài khoản không đúng';
					        break;
    					default:
        					$this->view->errMsg = 'Đăng nhập thất bại';
        				break;					        
					}         	 	
                }
            }
        }elseif($this->_request->isGet()) {
        }
        //$this->render();
     
    }
    
    function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        Zend_Session::destroy();
        unset($_SESSION['folder_user_upload_image']); 
        $this->_redirect('/');
    }
    
    function changepwAction()
    {
			$this->_helper->layout->setLayout('changepwd');
			$this->view->headLink()->appendStylesheet(BASE_URL. '/css/student.css');  			
	  		$this->view->headLink()->appendStylesheet(BASE_URL. '/js/jquery-ui-1.8.9.custom/css/custom-theme/jquery-ui-1.8.9.custom.css');	
    		$this->view->title = "Change password";
    		
			if ($this->_request->getMethod()== 'POST') {
				$user_obj = new  Default_Models_User();
				$user_obj->find("id", Zend_Auth::getInstance()->getIdentity()->id);
				if(Zend_Auth::getInstance()->getIdentity()->username=="giangvien")
					$this->view->errMsg = "Tài khoản này không thể đổi mật khẩu được, vui lòng Liên hệ Admin để đổi mật khẩu.";
				// request 
				$curent_pass = trim($this->_request->getPost('txtCurrentPass')) ;
				$new_pass = trim($this->_request->getPost('txtNewPass')) ;
				$confirm_pass = trim($this->_request->getPost('txtConfirmPass')) ;
				if (($curent_pass == '') || ($curent_pass == '') || ($curent_pass == ''))
				{
					$this->view->errMsg = "Bạn chưa điền đầy đủ thông tin";
				}
				else if(Zend_Auth::getInstance()->getIdentity()->username=="giangvien" || Zend_Auth::getInstance()->getIdentity()->username=="hocvien")
					$this->view->errMsg = "Tài khoản này không thể đổi mật khẩu được, vui lòng Liên hệ Admin để đổi mật khẩu.";
				else if ($curent_pass !== $user_obj->getPassword())
				{
					$this->view->errMsg = "Mật khẩu không đúng";
				}
				else if(strlen($new_pass)<5){
					$this->view->errMsg = "Mật khẩu phải từ 5 ký tự trở lên.";
				}
				else if ($new_pass != $confirm_pass)
				{
					$this->view->errMsg = "Mật khẩu mới và mật khẩu xác nhận không khớp";
				}else{
					
					$user_obj->setPassword($new_pass) ;
					$user_obj->save();
					$this->_redirect('/index');
				}
			}elseif ($this->_request->getMethod()== 'GET') {
			}
    }
    
	function  mkdirImageForUserTeacher($username){
			$dir = "/public_html/".BASE_URL."/media/images/tinymce/".$username ;
			$ftp_server    = "studyonline.com.vn";
			$ftp_user_name = "nguyendung";
			$ftp_user_pass = "!@#Dungthuc123";
			// set up basic connection
			$conn_id = ftp_connect($ftp_server);
		
			// login with username and password
			$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
			// try to create the directory $dir
			$return = ftp_mkdir($conn_id, $dir) ;
			ftp_site($conn_id, "CHMOD 777 $dir"); 
				//ftp_site($conn_id, "CHMOD 777 $dir") or die("FTP SITE CMD failed.");
			
			/*
			if (ftp_mkdir($conn_id, $dir)) {
			 echo "successfully created $dir\n";
			} else {
			 echo "There was a problem while creating $dir\n";
			}
			*/
			
			// close the connection
			ftp_close($conn_id);
			return $return;
	}
   
    
}
