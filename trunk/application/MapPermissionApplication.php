<?php
	class MapPermissionApplication{
		private $_domXml;
		private $_mapApp;
		
		public function __construct(){
			$this->_domXml = new DOMDocument('1.0', 'UTF-8');
			$this->_domXml->load(APPLICATION_PATH."/MapPermissionApplication.xml");	
			$this->loadToArray();
		}
		
		public function loadToArray(){
			$controllers    = $this->_domXml->getElementsByTagName("Controller");
			$mapApp  		= array();
			if($controllers!= NULL){
				for($i=0;$i<$controllers->length;$i++){
					$nodeController  = $controllers->item($i);
					$rowController	 = array();
					$rowController["name"]   = $nodeController->getAttribute("name");
					$rowController["detail"] = $nodeController->getAttribute("detail");
					$rowController["actions"]= array();
					$NodeActions 			 = $nodeController->getElementsByTagName("Action");					
					if($NodeActions!=NULL){
						for($j=0;$j<$NodeActions->length;$j++){
							$nodeAction    = $NodeActions->item($j);
							$rowAction = array();
							$rowAction["name"]   = $nodeAction->getAttribute("name");
							$rowAction["detail"] = $nodeAction->getAttribute("detail");
							$rowAction["dependence"] = $nodeAction->getAttribute("dependence");
							$rowController["actions"][] = $rowAction;							
						}
					}
					$mapApp[]   = $rowController;
				}
			}
			$this->_mapApp  = $mapApp;
		}
		
		public function getMapApp(){
			return $this->_mapApp;
			
		} 
	}
?>