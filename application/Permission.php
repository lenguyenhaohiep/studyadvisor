<?php
require_once APPLICATION_PATH . '/models/Groupuser.php';
require_once APPLICATION_PATH . '/models/Groupdetail.php';
require_once 'Zend/Controller/Plugin/Abstract.php';
require_once APPLICATION_PATH.'/MapPermissionApplication.php';

      class Zend_Controller_Plugin_Permission extends Zend_Controller_Plugin_Abstract
      {
      	  private $_MapPermissionApp;
      	  private $_mapApp;
      	  
      	  public function __construct(){
      	  	$zend_session_namespace = new Zend_Session_Namespace(NAMESPACE_MAP_APP);
      	  	// kiểm tra xem cái namespace đã lưu đối tượng MapApp chưa, nếu lưu rồi ta
      	  	// chỉ việc lấy ra mà không phải load lại nữa
      	  	// do cái mapApplication không bao giờ thay đổi
      	  	// trừ khi trong lúc code ta thay đổi chính vì vậy lúc up lên host ta tiến hành bỏ  mấy cái comment đi nha :d
      	  	//if($zend_session_namespace->__get(NAMESPACE_MAP_APP)==false){
      	  		$this->_MapPermissionApp = new MapPermissionApplication();
      	  		$zend_session_namespace->__set(NAMESPACE_MAP_APP,$this->_MapPermissionApp);     	  		
/*      	  	}else
      	  		$this->_MapPermissionApp  = $zend_session_namespace->__get(NAMESPACE_MAP_APP);
*/
      	  		$this->_mapApp  = $this->_MapPermissionApp->getMapApp();      	  		
      	  		
      	  }
          
      	  public function preDispatch(Zend_Controller_Request_Abstract $request)
          {
          		$controller = $this->_request->getControllerName();
          		$action     = $this->_request->getActionName();
          		$param		= $this->_request->getParams();          		
          		$is_ajax    = isset($param["ajax"])?$param["ajax"]:0;
          		
      	  		// kiểm tra với controller/action này có tồn tại trong MapApplication.xml không
      	  		// Nếu nó có tồn tại thì ta mới kiểm tra bước tiếp theo????? 
      	  		// Sở dĩ như vậy là bởi vì ý nghĩa của MapApplication là tất cả các controller/action
      	  		// mà ta có thể kiểm tra (all user case)
      	  		$ok = false; 
      	  		if(count($this->_mapApp)>0){
      	  			foreach($this->_mapApp as $mapAppItem){
      	  				if($ok) break;
      	  				$controllerName   = $mapAppItem["name"];
      	  				if($controllerName==$controller){
	      	  				$actions  		  = $mapAppItem["actions"];
	      	  				if(count($actions)>0)
	      	  					foreach($actions as $actionItem){
	      	  						$actionNameItem  = $actionItem["name"];
	      	  						if($actionNameItem==$action){	
	      	  							$ok = true;
	      	  							break;
	      	  						}
	      	  					}
      	  				}
      	  			}
      	  		}          		
          		if($ok){
	          		$models_group   = new Default_Models_GroupUser();
	      	  		$models_group_detail = new Default_Models_GroupDetail();
	      	  		// tìm tất cả các quyền được lưu trong cơ sở dữ liệu, từ có(1),không có(2), chưa đặt(0)
	      	  		$list_permissions    = $models_group_detail->fetchAll("`group_id`=".ROLE_IN_GROUPID);	
					// kiểm tra trong list_permissions này có cái action đó ko?
					// nếu không có : cho qua
					// nếu có       : giá trị =0 (chưa xác định) "cho qua"
					// nếu có       : giá trị =-1 redirect dến erorr/denied
					// nếu có       : giá trị =1  cho qua
					$value  = -2;
					if(count($list_permissions)>0){
						foreach($list_permissions as $list_permissionItem){
							//$list_permissionItem = new Default_Models_GroupDetail();							
							$actionNameItem   = $list_permissionItem->getAction();
							$ControllerNameItem   = $list_permissionItem->getController();
							if($actionNameItem==$action&&$ControllerNameItem==$controller){
								$value  = $list_permissionItem->getValue();
								break;	
							}						
						}
					}
					// value = -2(khong ton tai), -1: khong cho phep, 0: chua xet, 1: cho phep
					if($value == -1)
						$this->_denied($is_ajax);								      	  		
          		}      						          		
          }
          
          private function _denied($is_ajax=null){
				$this->_request->setControllerName("error");
				$this->_request->setActionName("denied"); 
				$this->_request->setParams(array("ajax"=>$is_ajax));       	
          }       
      }       
?>